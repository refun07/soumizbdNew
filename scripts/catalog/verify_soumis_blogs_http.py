#!/usr/bin/env python3
"""Verify all imported blogs, text integrity, and every local image URL."""
import concurrent.futures, hashlib, json, urllib.request
from pathlib import Path
from bs4 import BeautifulSoup
ROOT=Path(__file__).resolve().parents[2]
BASE='http://127.0.0.1:8008'
manifest=json.loads((ROOT/'database/imports/soumis-blogs/blogs.json').read_text())
def get_json(path):
 with urllib.request.urlopen(urllib.request.Request(BASE+path,headers={'Accept':'application/json'}),timeout=45) as response:return json.load(response)
issues=[];listed={};page=1
while True:
 result=get_json(f'/api/v1/blogs?page={page}')['result']['blogs']
 for blog in result['data']:listed[blog['slug']]=blog
 if page>=result['meta']['last_page']:break
 page+=1
if len(listed)!=len(manifest['articles']):issues.append(f'Listing count mismatch: {len(listed)}')
for source in manifest['articles']:
 if source['slug'] not in listed:issues.append('Missing in listing: '+source['slug']);continue
 detail=get_json('/api/v1/blogs/details/'+source['slug'])['result']['blog']
 for field,source_field in [('title','title'),('description','description'),('shortDescription','short_description'),('blogCategoryName','category')]:
  if detail[field]!=source[source_field]:issues.append(f'API {field} mismatch: '+source['slug'])
 if not source['published_at'] and detail['createdAt'] is not None:issues.append('Invented API date: '+source['slug'])
 soup=BeautifulSoup(detail['description'],'html.parser')
 digest=hashlib.sha256(' '.join(soup.get_text(' ',strip=True).split()).encode()).hexdigest()
 if digest!=source['source_text_sha256']:issues.append('Article text changed: '+source['slug'])
 for image in soup.select('img[src]'):
  if not image['src'].startswith('/uploads/soumis-blogs/'):issues.append('Unlocalized inline image: '+image['src'])
def image_check(path):
 with urllib.request.urlopen(urllib.request.Request(BASE+'/'+path,method='HEAD'),timeout=45) as response:
  if response.status!=200 or not response.headers.get_content_type().startswith('image/'):return 'Invalid image: '+path
 return None
with concurrent.futures.ThreadPoolExecutor(max_workers=4) as pool:issues.extend(filter(None,pool.map(image_check,manifest['images'].values())))
report={'api_blogs':len(listed),'listing_pages':page,'detail_pages_verified':len(manifest['articles']),'image_urls_verified':len(manifest['images']),'text_hashes_verified':len(manifest['articles']),'issues':issues}
(ROOT/'database/imports/soumis-blogs/http-verification.json').write_text(json.dumps(report,indent=2))
print(json.dumps(report,indent=2));raise SystemExit(bool(issues))
