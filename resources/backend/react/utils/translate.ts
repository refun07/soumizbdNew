import { t } from 'i18next';

/**
 * Generates translation key from text
 * Must match the backend generateTranslationKey() helper exactly
 */
function generateTranslationKey(input: string): string | null {
    if (!input) {
        return null;
    }

    // Convert to lowercase
    let key = input.toLowerCase();
    // Replace spaces with underscores
    key = key.replace(/ /g, '_');
    // Remove non-alphanumeric characters (except underscores)
    key = key.replace(/[^a-z0-9_]/g, '');
    
    // Return null if key is empty after sanitization
    return key.length === 0 ? null : key;
}

/**
 * Custom translate function that works with API backend
 * Falls back to original text if translation not found
 */
export const translate = (key?: string): string => {
    if (!key) return '';
    
    const translationKey = generateTranslationKey(key);
    
    // If key generation failed (e.g., only special characters), return original
    if (!translationKey) {
        return key;
    }
    
    const translated = t(translationKey);
    
    // If translation is the same as key, it means it wasn't found
    // Return the original text as fallback
    if (translated === translationKey) {
        return key;
    }
    
    return translated;
};

/**
 * Add a new translation to the backend
 * Call this when you want to register a new translation
 */
export const addTranslation = async (
    key: string, 
    value: string, 
    locale: string = 'en-US'
): Promise<void> => {
    const translationKey = generateTranslationKey(key);
    
    // Don't attempt to add if key generation failed
    if (!translationKey) {
        console.warn('Cannot add translation: key generation failed for:', key);
        return;
    }

    try {
        const response = await fetch('/api/v1/translations/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                key: translationKey,
                value: value,
                locale: locale
            })
        });

        if (!response.ok) {
            console.warn('Failed to add translation:', key);
        }
    } catch (error) {
        console.error('Error adding translation:', error);
    }
};
