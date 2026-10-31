#!/usr/bin/env python3
"""Verify every imported product through the actual public API and every local image URL."""
import concurrent.futures,json,urllib.request
from pathlib import Path
ROOT=Path(__file__).resolve().parents[2]
BASE='http://127.0.0.1:8008'
catalog=json.loads((ROOT/'database/imports/soumis/catalog.json').read_text())
def get_json(path):
 with urllib.request.urlopen(urllib.request.Request(BASE+path,headers={'Accept':'application/json'}),timeout=45) as response:return json.load(response)
issues=[];listed={};page=1
while True:
 result=get_json(f'/api/v1/products?page={page}')['result']['products']
 for product in result['data']:listed[product['slug']]=product
 if page>=result['meta']['last_page']:break
 page+=1
if len(listed)!=len(catalog['products']):issues.append(f'Listing count mismatch: {len(listed)}')
for source in catalog['products']:
 product=listed.get(source['slug'])
 if not product:issues.append('Missing in listing: '+source['slug']);continue
 if float(product['discountedBasePrice'])!=min(v['price'] for v in source['variants']):issues.append('API price mismatch: '+source['slug'])
 if product['stockQty']!=sum(v['stock_qty'] for v in source['variants']):issues.append('API stock mismatch: '+source['slug'])
 detail=get_json('/api/v1/products/details/'+source['slug'])['result']
 if len(detail['images'])!=len(source['local_gallery']):issues.append('API gallery count mismatch: '+source['slug'])
 if len(detail['variations'])!=len(source['variants']):issues.append('API variant count mismatch: '+source['slug'])
 if detail['description']!=source['description']:issues.append('API description mismatch: '+source['slug'])
def image_check(path):
 request=urllib.request.Request(BASE+'/'+path,method='HEAD')
 with urllib.request.urlopen(request,timeout=45) as response:
  if response.status!=200 or not response.headers.get_content_type().startswith('image/'):return 'Invalid image response: '+path
 return None
with concurrent.futures.ThreadPoolExecutor(max_workers=4) as pool:issues.extend(filter(None,pool.map(image_check,catalog['images'].values())))
report={'api_products':len(listed),'listing_pages':page,'detail_pages_verified':len(catalog['products']),'image_urls_verified':len(catalog['images']),'issues':issues}
(ROOT/'database/imports/soumis/http-verification.json').write_text(json.dumps(report,indent=2))
print(json.dumps(report,indent=2));raise SystemExit(bool(issues))
