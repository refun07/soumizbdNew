import test from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs';
import vm from 'node:vm';
import ts from 'typescript';

const source = fs.readFileSync(new URL('../../resources/frontend/utils/chunkRecovery.ts', import.meta.url), 'utf8');
const compiled = ts.transpileModule(source, { compilerOptions: { module: ts.ModuleKind.CommonJS } }).outputText;
function setup(storageFails = false) {
    let listener, reloads = 0, prevented = 0;
    const values = new Map();
    const context = { exports: {}, Date, sessionStorage: {
        getItem: (key) => { if (storageFails) throw new Error('Unavailable'); return values.get(key); },
        setItem: (key, value) => values.set(key, value),
    }, window: {
        addEventListener: (name, callback) => { assert.equal(name, 'vite:preloadError'); listener = callback; },
        location: { reload: () => reloads++ },
    } };
    vm.runInNewContext(compiled, context);
    context.exports.installChunkRecovery();
    return { fail: () => listener({ preventDefault: () => prevented++ }), stats: () => ({ reloads, prevented }) };
}
test('a stale page chunk reloads once; a persistent failure reaches the error boundary', () => {
    const recovery = setup();
    recovery.fail();
    assert.deepEqual(recovery.stats(), { reloads: 1, prevented: 1 });
    recovery.fail();
    assert.deepEqual(recovery.stats(), { reloads: 1, prevented: 1 });
});
test('unavailable session storage leaves manual recovery available', () => {
    const recovery = setup(true);
    assert.doesNotThrow(() => recovery.fail());
    assert.deepEqual(recovery.stats(), { reloads: 0, prevented: 0 });
});
