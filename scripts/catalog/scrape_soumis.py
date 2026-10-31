#!/usr/bin/env python3
"""Read the public Soumis catalog and its read-only variant-price endpoint.
Requires beautifulsoup4. Never calls cart, checkout, account, or order endpoints.
"""
from __future__ import annotations
import argparse, concurrent.futures, hashlib, html, http.cookiejar, itertools, json, re, ssl, time
import urllib.error, urllib.parse, urllib.request, xml.etree.ElementTree as ET
from datetime import datetime, timezone
from pathlib import Path
from bs4 import BeautifulSoup

ROOT = Path(__file__).resolve().parents[2]
BASE = 'https://soumisbangladesh.com'
CACHE = ROOT / 'storage/app/catalog-imports/soumis'
OUT = ROOT / 'database/imports/soumis'
MEDIA = ROOT / 'public/uploads/soumis'
TLS = ssl.create_default_context(cafile='/etc/ssl/cert.pem')
HEADERS = [('User-Agent', 'Mozilla/5.0 (compatible; ShomizCatalogImport/1.0)'), ('Accept', 'text/html,application/json')]

def client():
    opener = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar()), urllib.request.HTTPSHandler(context=TLS))
    opener.addheaders = HEADERS
    return opener

def read(url, opener=None, data=None, allowed_hosts=None):
    parsed = urllib.parse.urlparse(url)
    if parsed.scheme != 'https' or parsed.hostname not in (allowed_hosts or {'soumisbangladesh.com', 'www.soumisbangladesh.com'}):
        raise ValueError(f'Unexpected source host: {url}')
    opener = opener or client()
    for attempt in range(3):
        try:
            request = urllib.request.Request(url, data=urllib.parse.urlencode(data).encode() if data is not None else None,
                headers={'X-Requested-With': 'XMLHttpRequest', 'Referer': BASE + '/shop'} if data is not None else {})
            with opener.open(request, timeout=40) as response:
                return response.read(), response.geturl(), response.headers.get_content_type()
        except urllib.error.HTTPError as error:
            if error.code < 500 or attempt == 2: raise
        except (urllib.error.URLError, TimeoutError):
            if attempt == 2: raise
        time.sleep(attempt + 1)

def soup_of(body): return BeautifulSoup(body, 'html.parser')
def norm(text): return re.sub(r'\s+', ' ', text or '').strip()
def links(soup, fragment):
    return {urllib.parse.urljoin(BASE, a['href']).split('#')[0] for a in soup.select('a[href]') if fragment in a['href'] and urllib.parse.urlparse(urllib.parse.urljoin(BASE, a['href'])).hostname == 'soumisbangladesh.com'}
def cache_page(url, body):
    path = CACHE / 'html' / (hashlib.sha256(url.encode()).hexdigest() + '.html')
    path.parent.mkdir(parents=True, exist_ok=True); path.write_bytes(body)

def price(value):
    text = soup_of(str(value)).get_text(' ', strip=True)
    values = re.findall(r'\d[\d,]*(?:\.\d+)?', text)
    if not values: raise ValueError(f'Missing numeric price: {text}')
    return float(values[-1].replace(',', ''))

def safe_html(node):
    if node is None: return ''
    content = soup_of(str(node))
    for tag in content.select('script,style,iframe,object,embed,form,input,button'): tag.decompose()
    for tag in content.find_all(True):
        for key in list(tag.attrs):
            if key not in {'href','src','alt','title','colspan','rowspan','width','height'}: del tag[key]
        for key in ['href','src']:
            if tag.get(key):
                target = urllib.parse.urljoin(BASE, tag[key])
                if urllib.parse.urlparse(target).scheme not in {'https','http','mailto','tel'}: del tag[key]
                else: tag[key] = target
    return ''.join(str(child) for child in content.contents)

def section_html(soup, label):
    for node in soup.select('div.opacity-50'):
        if label in norm(node.get_text(' ',strip=True)).lower():
            row = node.find_parent('div', class_='row')
            if row:
                detail = row.select_one('div.fs-14')
                return safe_html(detail)
    return ''

def scrape_product(url):
    opener = client()
    try:
        body, final, _ = read(url, opener); cache_page(url, body)
    except urllib.error.HTTPError as error:
        if error.code in {404,410}: return {'unavailable':url,'reason':f'HTTP {error.code}'}
        raise
    soup = soup_of(body); form = soup.find('form', id='option-choice-form'); heading = soup.find('h1')
    if not form or not heading: return {'unavailable':url,'reason':'No public product detail form'}
    source_id = form.select_one('input[name="id"]')['value']
    name = norm(heading.get_text(' ',strip=True))
    gallery_node = soup.select_one('.product-details-gallery__main') or soup.select_one('.product-gallery')
    gallery = []
    if gallery_node:
        for image in gallery_node.select('img'):
            src = image.get('data-src') or image.get('src')
            if src and '/uploads/' in src:
                src = urllib.parse.urljoin(BASE,src)
                if src not in gallery: gallery.append(src)
    og = soup.find('meta',property='og:image')
    if not gallery and og and og.get('content'): gallery = [og['content']]
    if not gallery: raise ValueError(f'No product images: {url}')
    meta = lambda key: (soup.find('meta',attrs={'name':key}) or {}).get('content','')
    base = {node['name']:node.get('value','') for node in form.select('input[name]') if node.get('type') not in {'radio','checkbox'}}
    base['quantity']='1'
    groups=[]
    for field in dict.fromkeys(node['name'] for node in form.select('input[type="radio"][name]')):
        inputs=form.select(f'input[type="radio"][name="{field}"]'); row=inputs[0].find_parent('div',class_='row')
        match=re.search(r'Select\s+([^:]+):',row.get_text(' ',strip=True) if row else '')
        label=norm(match.group(1)) if match else ('Color' if field=='color' else field)
        groups.append({'field':field,'name':label,'values':list(dict.fromkeys(node.get('value','') for node in inputs))})
    for select in form.select('select[name]'):
        options=[o.get('value',o.get_text(strip=True)) for o in select.select('option') if o.get('value',o.get_text(strip=True))]
        groups.append({'field':select['name'],'name':select.get('name','Option'),'values':options})
    combos=list(itertools.product(*(group['values'] for group in groups))) if groups else [()]
    if len(combos)>300: raise ValueError(f'Too many source combinations: {url}')
    variants=[]
    for combo in combos:
        payload={**base,**{group['field']:value for group,value in zip(groups,combo)}}
        data,_,_=read(BASE+'/product/variant_price',opener,payload); result=json.loads(data)
        if 'price' not in result: raise ValueError(f'Variant response missing price for {url}')
        variants.append({'source_code':str(result.get('variation','')), 'options':{group['name']:value for group,value in zip(groups,combo)}, 'price':price(result['price']), 'stock_qty':max(0,int(result.get('quantity',0))) if int(result.get('in_stock',0)) else 0,'source_response':result})
    sku_match=re.search(r'SKU:\s*([^<]+)',str(heading.parent))
    description=section_html(soup,'description:')
    how_to_use=section_html(soup,'how to use')
    if how_to_use: description+='\n<h2>How to use</h2>\n'+how_to_use
    meta_price=soup.find('meta',property='og:price:amount')
    return {'source_id':int(source_id),'source_url':url,'canonical_url':final,'name':name,'slug':urllib.parse.urlparse(final).path.rstrip('/').split('/')[-1], 'sku':norm(html.unescape(sku_match.group(1))) if sku_match else '', 'brand':norm(name.split('|',1)[1]) if '|' in name else '', 'description':description,'meta_title':soup.title.get_text(strip=True) if soup.title else name,'meta_description':meta('description'),'meta_keywords':meta('keywords'),'gallery_urls':gallery,'attributes':groups,'variants':variants,'source_meta_price':price(meta_price['content']) if meta_price and meta_price.get('content') else None}

def scan_category(item):
    url,category=item
    pending=[url];seen=set();product_urls=set()
    while pending:
        page=pending.pop(0)
        if page in seen: continue
        seen.add(page);body,_,_=read(page);cache_page(page,body);soup=soup_of(body)
        listing=soup.find('form',id='search-form') or soup
        product_urls.update(links(listing,'/product/'))
        for link in soup.select('.pagination a[href]'):
            target=urllib.parse.urljoin(BASE,link['href'])
            if target not in seen and urllib.parse.urlparse(target).path==urllib.parse.urlparse(url).path: pending.append(target)
    return {**category,'source_url':url,'product_urls':sorted(product_urls),'pages':len(seen)}

def download_image(url, allowed_hosts=None):
    suffix=Path(urllib.parse.urlparse(url).path).suffix.lower()
    if suffix not in {'.png','.webp','.jpeg','.jpg','.gif','.avif'}: suffix='.jpg'
    filename=hashlib.sha256(url.encode()).hexdigest()[:24]+suffix;dest=MEDIA/filename
    if not dest.exists():
        data,_,mime=read(url, allowed_hosts=allowed_hosts)
        if not mime.startswith('image/'): raise ValueError(f'Non-image response: {url}')
        dest.write_bytes(data)
    from PIL import Image
    with Image.open(dest) as image: image.verify()
    return url,str(dest.relative_to(ROOT/'public'))

def main():
    parser=argparse.ArgumentParser();parser.add_argument('--workers',type=int,default=4);parser.add_argument('--reuse-products',action='store_true');args=parser.parse_args()
    for path in [CACHE/'html',OUT,MEDIA]:path.mkdir(parents=True,exist_ok=True)
    pages={}
    for suffix in ['/','/shop','/sitemap.xml','/products/pre-order']:
        body,_,_=read(BASE+suffix);cache_page(BASE+suffix,body);pages[suffix]=body
    shop=soup_of(pages['/shop']);home=soup_of(pages['/']);preorder_urls=links(soup_of(pages['/products/pre-order']),'/product/')
    shop_urls=links(shop,'/product/');site_urls={node.text.strip() for node in ET.fromstring(pages['/sitemap.xml']).iter() if node.tag.endswith('loc') and node.text and '/product/' in node.text}
    categories={}
    for node in shop.select('a[href]'):
        url=urllib.parse.urljoin(BASE,node['href'])
        if not url.startswith(BASE+'/category/'):continue
        name=norm(node.get_text(' ',strip=True))
        if not name:continue
        category=categories.setdefault(url,{'name':name,'slug':url.rstrip('/').split('/')[-1],'parent_url':None,'image_url':None})
        panel=node.find_parent('div',class_='sub-menu')
        if panel and panel.get('id','').startswith('cat-menu-'):
            key=panel['id'].removeprefix('cat-menu-')
            trigger=shop.find(attrs={'data-id':key})
            if trigger:
                parent=trigger if trigger.name=='a' else trigger.find_parent('a',href=re.compile('/category/')) or trigger.parent.find('a',href=re.compile('/category/'))
                if parent:category['parent_url']=urllib.parse.urljoin(BASE,parent['href'])
        hero=home.find('a',href=url)
        if hero and hero.find('img'):
            image=hero.find('img');category['image_url']=image.get('data-src') or image.get('src')
    with concurrent.futures.ThreadPoolExecutor(max_workers=args.workers) as pool:
        category_list=list(pool.map(scan_category,categories.items()))
    all_urls=shop_urls|site_urls|preorder_urls|{url for category in category_list for url in category['product_urls']}
    print(f'Discovered {len(all_urls)} product URLs, {len(category_list)} categories',flush=True)
    old={}
    existing=OUT/'catalog.json'
    if args.reuse_products and existing.exists():old={p['source_url']:p for p in json.loads(existing.read_text())['products']}
    products=[];unavailable=[];failures=[]
    with concurrent.futures.ThreadPoolExecutor(max_workers=args.workers) as pool:
        futures={pool.submit(scrape_product,url):url for url in sorted(all_urls) if url not in old}
        products.extend(old[url] for url in sorted(all_urls) if url in old)
        for index,future in enumerate(concurrent.futures.as_completed(futures),1):
            url=futures[future]
            try:
                product=future.result()
                if 'unavailable' in product:unavailable.append(product)
                else: products.append(product)
            except Exception as error: failures.append({'url':url,'error':str(error)})
            if index%10==0:print(f'Product details checked {index}/{len(futures)}',flush=True)
    by_id={};aliases=[]
    for product in products:
        if product['source_id'] in by_id: aliases.append({'url':product['source_url'],'canonical_source_id':product['source_id']});continue
        by_id[product['source_id']]=product
    products=sorted(by_id.values(),key=lambda p:p['source_id'])
    by_url={p['source_url']:p for p in products}
    for product in products:
        product['category_urls']=[c['source_url'] for c in category_list if product['source_url'] in c['product_urls'] or product['canonical_url'] in c['product_urls']]
        product['is_preorder']=product['source_url'] in preorder_urls
    for category in category_list:
        if not category['image_url']:
            member=next((p for p in products if category['source_url'] in p['category_urls']),None)
            if member:category['image_url']=member['gallery_urls'][0]
    images={url for p in products for url in p['gallery_urls']}|{c['image_url'] for c in category_list if c['image_url']}
    for product in products:
        images.update(node['src'] for node in soup_of(product['description']).select('img[src]') if urllib.parse.urlparse(node['src']).hostname=='soumisbangladesh.com')
    image_map={}
    with concurrent.futures.ThreadPoolExecutor(max_workers=args.workers) as pool:
        futures={pool.submit(download_image,url):url for url in sorted(images)}
        for future in concurrent.futures.as_completed(futures):
            try:url,local=future.result();image_map[url]=local
            except Exception as error:failures.append({'url':futures[future],'error':str(error)})
    for product in products:
        product['local_gallery']=[image_map[url] for url in product['gallery_urls'] if url in image_map]
        for remote,local in image_map.items():product['description']=product['description'].replace(remote,'/'+local)
    for category in category_list:category['local_image']=image_map.get(category['image_url'])
    catalog={'source':BASE,'collected_at':datetime.now(timezone.utc).isoformat(),'discovery':{'shop_urls':len(shop_urls),'sitemap_urls':len(site_urls),'all_candidate_urls':len(all_urls)},'categories':category_list,'products':products,'images':image_map,'unavailable':unavailable,'aliases':aliases,'failures':failures}
    (OUT/'catalog.json').write_text(json.dumps(catalog,ensure_ascii=False,indent=2))
    print(json.dumps({'products':len(products),'variants':sum(len(p['variants']) for p in products),'categories':len(category_list),'images':len(image_map),'unavailable':len(unavailable),'aliases':len(aliases),'failures':failures},ensure_ascii=False),flush=True)
    if failures:raise SystemExit(1)

if __name__=='__main__':main()
