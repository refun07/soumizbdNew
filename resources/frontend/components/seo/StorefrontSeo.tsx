import { useEffect } from 'react';
import { useLocation } from 'react-router-dom';

type Metadata = {
    title: string;
    description: string;
    keywords: string;
    canonical: string;
    image: string;
    type: string;
    robots: string;
    schemas: Record<string, unknown>[];
};

function applyMetadata(data: Metadata) {
    document.title = data.title;
    const tags: [string, string, string][] = [
        ['name', 'description', data.description],
        ['name', 'keywords', data.keywords],
        ['name', 'robots', data.robots],
        ['property', 'og:title', data.title],
        ['property', 'og:description', data.description],
        ['property', 'og:type', data.type],
        ['property', 'og:url', data.canonical],
        ['property', 'og:image', data.image],
        ['name', 'twitter:card', 'summary_large_image'],
        ['name', 'twitter:title', data.title],
        ['name', 'twitter:description', data.description],
        ['name', 'twitter:image', data.image],
    ];
    for (const [attribute, name, value] of tags) {
        let tag = document.head.querySelector<HTMLMetaElement>(
            `meta[${attribute}="${name}"]`,
        );
        if (!tag) {
            tag = document.createElement('meta');
            tag.setAttribute(attribute, name);
            document.head.appendChild(tag);
        }
        tag.content = value;
    }
    let canonical = document.head.querySelector<HTMLLinkElement>(
        'link[rel="canonical"]',
    );
    if (!canonical) {
        canonical = document.createElement('link');
        canonical.rel = 'canonical';
        document.head.appendChild(canonical);
    }
    canonical.href = data.canonical;
    let schema = document.getElementById('storefront-structured-data');
    if (!schema) {
        schema = document.createElement('script');
        schema.setAttribute('type', 'application/ld+json');
        schema.id = 'storefront-structured-data';
        document.head.appendChild(schema);
    }
    schema.textContent = JSON.stringify({
        '@context': 'https://schema.org',
        '@graph': data.schemas,
    });
}

export default function StorefrontSeo() {
    const { pathname, search } = useLocation();
    useEffect(() => {
        const path = pathname + search;
        const initial = (
            window as unknown as {
                storefrontSeo?: { path: string; data: Metadata };
            }
        ).storefrontSeo;
        if (initial?.path === path) {
            applyMetadata(initial.data);
            return;
        }
        const abort = new AbortController();
        fetch(`/seo/metadata?path=${encodeURIComponent(path)}`, {
            signal: abort.signal,
            headers: { Accept: 'application/json' },
        })
            .then(async (response) => {
                if (!response.ok && response.status !== 404)
                    throw new Error('Metadata unavailable');
                return response.json() as Promise<Metadata>;
            })
            .then((data) => {
                if (!abort.signal.aborted) applyMetadata(data);
            })
            .catch(() => {
                if (!abort.signal.aborted)
                    applyMetadata({
                        title: window.config.generalSettings.appName,
                        description: '',
                        keywords: '',
                        image: '',
                        canonical:
                            window.config.generalSettings.rootUrl.replace(
                                /\/$/,
                                '',
                            ) + pathname,
                        type: 'website',
                        robots: 'noindex, follow',
                        schemas: [],
                    });
            });
        return () => abort.abort();
    }, [pathname, search]);
    return null;
}
