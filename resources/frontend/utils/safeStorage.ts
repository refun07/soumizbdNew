// Embedded/private browsers can throw even when reading window.localStorage.
// Keep this page usable with temporary memory storage when persistence is denied.
export const createSafeStorage = (kind: 'localStorage' | 'sessionStorage') => {
    const memory = new Map<string, string>();
    let memoryOnly = false;
    const native = () => {
        if (memoryOnly || typeof window === 'undefined') return null;
        try { return window[kind]; } catch { memoryOnly = true; return null; }
    };
    return {
        getItem(key: string): string | null {
            try {
                const storage = native();
                if (storage) {
                    const value = storage.getItem(key);
                    if (value === null) memory.delete(key); else memory.set(key, value);
                    return value;
                }
            } catch { memoryOnly = true; }
            return memory.get(key) ?? null;
        },
        setItem(key: string, value: string) {
            memory.set(key, String(value));
            try { native()?.setItem(key, String(value)); } catch { memoryOnly = true; }
        },
        removeItem(key: string) {
            memory.delete(key);
            try { native()?.removeItem(key); } catch { memoryOnly = true; }
        },
        clear() {
            memory.clear();
            try { native()?.clear(); } catch { memoryOnly = true; }
        },
    };
};

export const safeLocalStorage = createSafeStorage('localStorage');
export const safeSessionStorage = createSafeStorage('sessionStorage');

export const readStoredJson = <T>(storage: ReturnType<typeof createSafeStorage>, key: string): T | null => {
    const value = storage.getItem(key);
    if (!value) return null;
    try { return JSON.parse(value) as T; } catch { storage.removeItem(key); return null; }
};
