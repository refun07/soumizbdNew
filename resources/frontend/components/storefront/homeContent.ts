import schema from '../../../storefront/home-content.json';

type DefaultValue<T> = T extends { default: infer V }
    ? V extends unknown[]
        ? number[]
        : V
    : never;
export type HomeContent = {
    [S in keyof typeof schema]: {
        [K in keyof (typeof schema)[S]['fields']]: DefaultValue<
            (typeof schema)[S]['fields'][K]
        >;
    };
};

export function getHomeContent(): HomeContent {
    const saved = window.config.botanicalContent;
    return Object.fromEntries(
        Object.entries(schema).map(([section, definition]) => [
            section,
            Object.fromEntries(
                Object.entries(definition.fields).map(([key, field]) => [
                    key,
                    (
                        saved as
                            | Record<string, Record<string, unknown>>
                            | undefined
                    )?.[section]?.[key] ?? field.default,
                ]),
            ),
        ]),
    ) as HomeContent;
}
