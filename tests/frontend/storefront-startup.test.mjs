import test from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs';
import vm from 'node:vm';
import ts from 'typescript';
const storageSource = fs.readFileSync(new URL('../../resources/frontend/utils/safeStorage.ts', import.meta.url),'utf8');
const checkoutSource = fs.readFileSync(new URL('../../resources/frontend/store/features/checkout/checkoutSlice.ts', import.meta.url),'utf8');
const compile = source => ts.transpileModule(source,{compilerOptions:{module:ts.ModuleKind.CommonJS}}).outputText;
function restore(value, denied = false) {
    const window = {};
    Object.defineProperty(window, 'sessionStorage', {get() {if(denied) throw new Error('SecurityError'); return {getItem:()=>value,removeItem:()=>{}};}});
    const storage = {exports:{},window}; vm.runInNewContext(compile(storageSource),storage);
    const checkout = {exports:{}, require: name => {
        if (name.includes('safeStorage')) return storage.exports;
        if (name === 'react-redux') return {useSelector:()=>{}};
        if (name === '@reduxjs/toolkit') return {createSlice: config=>({...config,actions:{}})};
        throw Error(name);
    }};
    vm.runInNewContext(compile(checkoutSource), checkout);
    return checkout.exports.default.initialState;
}
for (const value of ['{invalid', 'null', '[]', 'true', '{"customerDetails":null,"selectedShops":null,"coupons":null}']) test(`checkout starts safely with invalid saved state: ${value}`, () => {
    const state=restore(value); assert.equal(state.customerDetails.name,''); assert.ok(state.selectedShops); assert.ok(Array.isArray(state.coupons));
});
test('checkout starts when Facebook browser denies session storage',()=>assert.equal(restore(null,true).paymentMethod,''));
test('valid checkout details are retained',()=> {
    const state=restore('{"customerDetails":{"name":"Test"},"paymentMethod":"cod","shippingCharge":70}');
    assert.equal(state.customerDetails.name,'Test'); assert.equal(state.customerDetails.phone,''); assert.equal(state.paymentMethod,'cod'); assert.equal(state.shippingCharge,70);
});
const template=fs.readFileSync(new URL('../../resources/views/frontend/home.blade.php',import.meta.url),'utf8');
const bootScript=[...template.matchAll(/<script>([\s\S]*?)<\/script>/g)].map(m=>m[1]).find(s=>s.includes('Store loading delayed'));
function bootContext(boot, search = '') {
    const timers=[], listeners={}, signals=[];
    const context={window:{location:{search},setTimeout:(callback,delay)=>timers.push({callback,delay}),addEventListener:(name,callback)=>listeners[name]=callback},document:{documentElement:{classList:{add:()=>{}}},readyState:'complete',querySelector:()=>boot,addEventListener:(name,callback)=>listeners[name]=callback},Image:class {set src(value){signals.push(value)}}};
    vm.runInNewContext(bootScript,context);
    return {timers,listeners,signals};
}
test('failed entry bundle exposes retry independently of external resources',()=> {
    const classes=[],attributes={};
    const boot={classList:{add:c=>classes.push(c)},setAttribute:(k,v)=>attributes[k]=v};
    const {timers}=bootContext(boot);
    assert.equal(timers[0].delay,12000); timers[0].callback();
    assert.ok(classes.includes('storefront-boot-failed')); assert.equal(attributes['aria-label'],'Store loading delayed');
    assert.ok(template.indexOf(bootScript)<template.indexOf('fonts.googleapis.com'));
    assert.match(template,/fullUrlWithQuery/);
    assert.match(template,/media="print" onload="this.media='all'"/);
});
test('successful React mount leaves no recovery overlay',()=> {
    const {timers}=bootContext(null); assert.doesNotThrow(()=>timers[0].callback());
});
test('startup diagnostics are off for ordinary shoppers',()=> {
    const {signals,listeners}=bootContext(null,'?fbclid=example');
    listeners.DOMContentLoaded();listeners.pageshow();assert.equal(signals.length,0);
});
test('requested diagnostic reports only technical startup stages',()=> {
    const {signals,listeners}=bootContext({classList:{add:()=>{}},setAttribute:()=>{}},'?loading-check=3');
    listeners.error({error:{name:'TypeError'},filename:'https://example.test/build/assets/index-hash.js?private=never-send',lineno:3});
    assert.equal(signals.length,2); assert.match(signals[0],/stage=head/); assert.match(signals[1],/stage=script-TypeError/); assert.ok(!signals[1].includes('private'));
});
