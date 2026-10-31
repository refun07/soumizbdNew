#!/usr/bin/env python3
"""Collect every public blog listing page, sitemap article, full body and image."""
from __future__ import annotations
import concurrent.futures, hashlib, json, urllib.error, urllib.parse, xml.etree.ElementTree as ET
from datetime import datetime, timezone
from pathlib import Path
import scrape_soumis as source

ROOT=source.ROOT
OUT=ROOT/'database/imports/soumis-blogs'
source.CACHE=ROOT/'storage/app/catalog-imports/soumis-blogs'
source.MEDIA=ROOT/'public/uploads/soumis-blogs'
BASE=source.BASE

def article(url, listing):
    try:
        body,final,_=source.read(url);source.cache_page(url,body)
    except urllib.error.HTTPError as error:
        if error.code in {404,410}:return {'unavailable':url,'reason':f'HTTP {error.code}'}
        raise
    soup=source.soup_of(body);heading=soup.find('h1')
    if not heading:return {'unavailable':url,'reason':'No public article heading'}
    header=heading.parent
    content=header.find_next_sibling('div',class_='fs-16')
    if content is None:content=header.parent.select_one('div.overflow-hidden.fs-16')
    if content is None or len(content.get_text(strip=True))<30:raise ValueError('Article content container missing: '+url)
    for image in content.select('img'):
        if image.get('data-src'):image['src']=urllib.parse.urljoin(BASE,image['data-src'])
    category=header.select_one('i')
    title=source.norm(heading.get_text(' ',strip=True))
    image=soup.find('img',alt=title)
    og_images=soup.find_all('meta',property='og:image')
    og_image=og_images[-1] if og_images else None
    banner=(image.get('data-src') or image.get('src')) if image else (og_image.get('content') if og_image else None)
    thumbnail=listing.get('thumbnail') or banner
    if not banner or not thumbnail:raise ValueError('Article image missing: '+url)
    meta=lambda name:(soup.find('meta',attrs={'name':name}) or {}).get('content','')
    published=None
    for key in ['article:published_time','datePublished','date']:
        node=soup.find('meta',attrs={'property':key}) or soup.find('meta',attrs={'name':key})
        if node and node.get('content'):published=node['content'];break
    canonical=soup.find('link',rel='canonical')
    canonical_url=canonical.get('href',final) if canonical else final
    description=source.safe_html(content)
    text=source.norm(source.soup_of(description).get_text(' ',strip=True))
    return {'source_url':url,'canonical_url':canonical_url,'slug':urllib.parse.unquote(urllib.parse.urlparse(canonical_url).path.rstrip('/').split('/')[-1]),'title':title,'category':source.norm(category.get_text(' ',strip=True)) if category else listing.get('category','Uncategorized'),'short_description':listing.get('short_description') or meta('description'),'source_description':description,'description':description,'source_text_sha256':hashlib.sha256(text.encode()).hexdigest(),'published_at':published,'thumbnail_url':urllib.parse.urljoin(BASE,thumbnail),'banner_url':urllib.parse.urljoin(BASE,banner),'meta_image_url':urllib.parse.urljoin(BASE,og_image['content']) if og_image and og_image.get('content') else urllib.parse.urljoin(BASE,banner),'meta_title':soup.title.get_text(strip=True) if soup.title else title,'meta_description':meta('description'),'meta_keywords':meta('keywords')}

def main():
    for path in [OUT,source.CACHE/'html',source.MEDIA]:path.mkdir(parents=True,exist_ok=True)
    pending=[BASE+'/blog'];seen=set();listing={}
    while pending:
        url=pending.pop(0)
        if url in seen:continue
        seen.add(url);body,_,_=source.read(url);source.cache_page(url,body);soup=source.soup_of(body)
        for card in soup.select('.card'):
            heading=card.find('h2');link=heading.find('a',href=True) if heading else None
            if not link or '/blog/' not in link['href']:continue
            image=card.find('img');summary=card.find('p');category=card.find('i')
            listing.setdefault(urllib.parse.urljoin(BASE,link['href']),{'thumbnail':(image.get('data-src') or image.get('src')) if image else None,'short_description':source.norm(summary.get_text(' ',strip=True)) if summary else '', 'category':source.norm(category.get_text(' ',strip=True)) if category else ''})
        for link in soup.select('.pagination a[href]'):
            target=urllib.parse.urljoin(BASE,link['href'])
            if urllib.parse.urlparse(target).path=='/blog' and target not in seen:pending.append(target)
    sitemap,_,_=source.read(BASE+'/sitemap.xml');source.cache_page(BASE+'/sitemap.xml',sitemap)
    sitemap_urls={n.text.strip() for n in ET.fromstring(sitemap).iter() if n.tag.endswith('loc') and n.text and '/blog/' in n.text}
    all_urls=list(listing)+sorted(sitemap_urls-set(listing))
    print(f'Discovered {len(listing)} listed articles across {len(seen)} listing URLs; {len(all_urls)} candidate article URLs including the sitemap.',flush=True)
    articles=[];unavailable=[];failures=[]
    with concurrent.futures.ThreadPoolExecutor(max_workers=4) as pool:
        futures={pool.submit(article,url,listing.get(url,{})):url for url in all_urls}
        for future in concurrent.futures.as_completed(futures):
            try:
                result=future.result()
                if 'unavailable' in result:unavailable.append(result)
                else:articles.append(result)
            except Exception as error:failures.append({'url':futures[future],'error':str(error)})
    articles.sort(key=lambda a:all_urls.index(a['source_url']))
    unique={};aliases=[]
    for item in articles:
        if item['slug'] in unique:aliases.append(item['source_url'])
        else:unique[item['slug']]=item
    articles=list(unique.values())
    images={item[key] for item in articles for key in ['thumbnail_url','banner_url','meta_image_url']}
    for item in articles:images.update(image['src'] for image in source.soup_of(item['description']).select('img[src]'))
    image_map={}
    with concurrent.futures.ThreadPoolExecutor(max_workers=4) as pool:
        # This CDN is referenced by the source article bodies; page discovery stays source-only.
        hosts={'soumisbangladesh.com','www.soumisbangladesh.com','images.squarespace-cdn.com'}
        futures={pool.submit(source.download_image,url,hosts):url for url in images}
        for future in concurrent.futures.as_completed(futures):
            try:url,local=future.result();image_map[url]=local
            except Exception as error:failures.append({'url':futures[future],'error':str(error)})
    product_catalog=json.loads((ROOT/'database/imports/soumis/catalog.json').read_text())
    internal={BASE+'/shop':'/product',BASE+'/blog':'/blogs',BASE+'/':'/'}
    internal.update({p['source_url']:'/product/'+p['slug'] for p in product_catalog['products']})
    internal.update({c['source_url']:'/categories/'+c['slug'] for c in product_catalog['categories']})
    internal.update({a['source_url']:'/blogs/'+a['slug'] for a in articles})
    for item in articles:
        soup=source.soup_of(item['description'])
        for image in soup.select('img[src]'):
            if image['src'] in image_map:image['src']='/'+image_map[image['src']]
        for link in soup.select('a[href]'):
            parsed=urllib.parse.urlparse(link['href']);base=urllib.parse.urlunparse(parsed._replace(fragment=''))
            if base in internal:link['href']=internal[base]+('#'+parsed.fragment if parsed.fragment else '')
        item['description']=str(soup)
        if hashlib.sha256(source.norm(soup.get_text(' ',strip=True)).encode()).hexdigest()!=item['source_text_sha256']:raise ValueError('Article text changed during localization: '+item['source_url'])
        for key in ['thumbnail','banner','meta_image']:item['local_'+key]=image_map.get(item[key+'_url'])
    categories=list(dict.fromkeys(item['category'] for item in articles))
    result={'source':BASE,'collected_at':datetime.now(timezone.utc).isoformat(),'discovery':{'listing_urls':sorted(seen),'listed_articles':len(listing),'sitemap_articles':len(sitemap_urls),'candidate_urls':len(all_urls)},'articles':articles,'categories':categories,'images':image_map,'unavailable':unavailable,'aliases':aliases,'failures':failures}
    (OUT/'blogs.json').write_text(json.dumps(result,ensure_ascii=False,indent=2))
    print(json.dumps({'articles':len(articles),'categories':len(categories),'images':len(image_map),'unavailable':unavailable,'failures':failures,'articles_with_publication_dates':sum(bool(a['published_at']) for a in articles)},ensure_ascii=False),flush=True)
    if failures:raise SystemExit(1)

if __name__=='__main__':main()
