import test from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs';
import vm from 'node:vm';
import ts from 'typescript';
const source = fs.readFileSync(new URL('../../resources/frontend/utils/safeStorage.ts', import.meta.url), 'utf8');
const code = ts.transpileModule(source, {compilerOptions:{module:ts.ModuleKind.CommonJS}}).outputText;
function setup(mode = '') {
    const values = new Map();
    const storage = {
        getItem(key) { if (mode === 'get') throw new Error('denied'); return values.get(key) ?? null; },
        setItem(key, value) { if (mode === 'write') throw new Error('quota'); values.set(key, value); },
        removeItem: key => values.delete(key), clear: () => values.clear(),
    };
    const window = {};
    for (const kind of ['localStorage', 'sessionStorage']) Object.defineProperty(window, kind, {get() {if(mode === 'getter') throw new Error('SecurityError'); return storage;}});
    const context = {exports:{}, window};
    vm.runInNewContext(code, context);
    return {...context.exports, values};
}
for (const mode of ['getter', 'get', 'write']) test(`storage ${mode} failure keeps the current page usable`, () => {
    const {safeLocalStorage:s} = setup(mode);
    assert.doesNotThrow(() => s.getItem('auth'));
    s.setItem('auth', 'token'); assert.equal(s.getItem('auth'), 'token');
    s.removeItem('auth'); assert.equal(s.getItem('auth'), null);
});
test('normal storage persists and observes values changed by another tab', () => {
    const {safeLocalStorage:s, values} = setup();
    s.setItem('auth', 'token'); assert.equal(values.get('auth'), 'token');
    values.set('auth', 'changed'); assert.equal(s.getItem('auth'), 'changed');
    s.removeItem('auth'); assert.equal(values.has('auth'), false);
});
test('corrupt checkout JSON is discarded without clearing unrelated data', () => {
    const {safeSessionStorage:s, readStoredJson} = setup();
    s.setItem('arm-checkoutData', '{bad'); s.setItem('other', 'keep');
    assert.equal(readStoredJson(s, 'arm-checkoutData'), null);
    assert.equal(s.getItem('arm-checkoutData'), null); assert.equal(s.getItem('other'), 'keep');
    s.setItem('arm-checkoutData', '{"paymentMethod":"cod"}');
    assert.equal(readStoredJson(s, 'arm-checkoutData').paymentMethod, 'cod');
});
