import test from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs';
import vm from 'node:vm';
import ts from 'typescript';
const source=fs.readFileSync(new URL('../../resources/frontend/utils/saleCountdown.ts',import.meta.url),'utf8');
const compiled=ts.transpileModule(source,{compilerOptions:{module:ts.ModuleKind.CommonJS}}).outputText;
const context={exports:{},Date,Number,Math};vm.runInNewContext(compiled,context);
const countdown=context.exports.saleCountdown;
test('missing or invalid deadlines do not invent a countdown',()=>{
 assert.equal(countdown(null,0),null);assert.equal(countdown('invalid',0),null);
});
test('Dhaka time is interpreted as an absolute instant with correct day/hour rollovers',()=>{
 const now=Date.parse('2026-10-17T00:00:00+06:00');
 const result=countdown('2026-10-18T02:03:04+06:00',now);
 assert.equal(result.days,1);assert.equal(result.hours,2);assert.equal(result.minutes,3);assert.equal(result.seconds,4);assert.equal(result.expired,false);
});
test('the sale expires exactly at its deadline and never displays negative time',()=>{
 const end='2026-10-17T12:00:00+06:00';const now=Date.parse(end);
 assert.equal(countdown(end,now-1).expired,false);assert.equal(countdown(end,now-1).seconds,1);
 assert.equal(countdown(end,now).expired,true);assert.equal(countdown(end,now+86400000).days,0);assert.equal(countdown(end,now+86400000).seconds,0);
});
