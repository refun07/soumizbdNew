import { t } from 'i18next';
import localAxios from './axios';

function getStrToLangKey(inputString: string) {
    // Convert to lowercase, replace spaces with underscores
    let modifiedString = inputString.toLowerCase().replace(/ /g, '_');

    // Remove non-alphanumeric characters and underscores
    let sanitizedString = modifiedString.replace(/[^a-z0-9_]/g, '');

    return sanitizedString;
}

/**
 * takes a string, generates the translation key using `getStrToLangKey` then returns the translation using i18next t function
 * @returns
 */
export const translate = (text?: string) => {
    if (!text) return '';

    // localAxios.post('/insert-translations-keys', {
    //     key: getStrToLangKey(text),
    //     value: text,
    // });

    // return t(getStrToLangKey(text));
    return text;
};
