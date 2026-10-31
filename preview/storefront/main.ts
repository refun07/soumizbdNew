// Isolated visual preview. Sample catalog and simulated API never enter the production bundle.
const language = { name: 'English', code: 'en-US', flag: '', is_rtl: 0 };
window.config = {
    defaultTheme: 'theme-one',
    defaultHeader: 'header-one',
    defaultCategoryCard: 'category-card-one',
    defaultProductCard: 'product-card-one',
    cartVisibilityToggle: '0',
    topbarHotline: '',
    topbarHotlineLabel: '',
    topbarPromoText: '',
    generalSettings: {
        appName: 'Shomiz',
        appMode: 'singleVendor',
        rootUrl: location.origin,
        apiUrl: `${location.origin}/__design-preview-api__/`,
        demoMode: 'Off',
        logo: '/images/storefront/soumis-logo.png',
        env: 'local',
        showCouponOnCart: '0',
        allowBackorder: false,
        customerLoginMethod: 'email',
        skipCart: false,
        productInfiniteScroll: false,
    },
    currency: {
        code: 'BDT',
        symbol: { position: 'symbol_first', show: '৳' },
        thousandSeparator: ',',
        numOfDecimals: 0,
        decimalSeparator: '.',
    },
    defaultLang: language,
    languages: [language],
    countries: [],
    paymentMethods: [],
    socialLogins: [],
    floatingChat: {
        call_enabled: false,
        whatsapp_enabled: false,
        whatsapp_number: '',
        secondary_whatsapp_number: '',
        messenger_enabled: false,
        messenger_url: '',
        tiktok_enabled: false,
        tiktok_url: '',
    },
};
const image = (name: string) => `/preview/storefront/images/${name}.jpg`;
const categories = [
    'Bath & body',
    'Hair',
    'Lips',
    'Brushes',
    'Makeup',
    'Skin care',
].map((name, index) => ({
    id: index + 1,
    name,
    slug: name.toLowerCase().replaceAll(' & ', '-').replaceAll(' ', '-'),
    thumbnailImage: image(
        ['ritual', 'bottles', 'skin', 'makeup', 'essentials', 'cream'][index],
    ),
    productsCount: [12, 10, 6, 25, 16, 18][index],
    children_categories: [],
    parent_category: null,
}));
const products = [
    'Hand cream',
    'Argan glow',
    'BB cream',
    'Detangling hair care',
    'Botanical face serum',
    'Soothing sun cream',
    'Face moisturiser',
    'Daily cleansing balm',
    'Nourishing body oil',
].map((name, index) => ({
    id: index + 1,
    name,
    slug: name.toLowerCase().replaceAll(' ', '-'),
    thumbnailImg: image(
        [
            'cream',
            'serum',
            'skin',
            'bottles',
            'serum',
            'essentials',
            'ritual',
            'skin',
            'bottles',
        ][index],
    ),
    basePrice: [1240, 1550, 1140, 830, 1560, 1225, 1445, 1665, 1390][index],
    discountedBasePrice: [1240, 1550, 1140, 830, 1560, 1225, 1445, 1665, 1390][
        index
    ],
    stockQty: 20,
    hasAvailableStock: true,
    hasVariation: index === 2 ? 1 : 0,
    inWishlist: false,
    unit: '',
    rating: { average: 4.8, total: 24 },
    badges: [],
    tags: [],
    shop_id: 1,
    shipping_class_id: 1,
    isPreOrder: false,
    preOrderData: null,
    shop: { id: 1, name: 'Shomiz', slug: 'shomiz' },
    categories: [categories[index % 6]],
}));
const details = (product: (typeof products)[number]) => ({
    ...product,
    deliveryHours: 48,
    codAvailable: true,
    hasEmi: 0,
    hasWarranty: 0,
    description: '<p>A thoughtful addition to your everyday beauty ritual.</p>',
    images: [product.thumbnailImg],
    videoLink: '',
    brand: { id: 1, name: 'Botanicals', slug: 'botanicals', logo: '' },
    rootCategory: categories[0],
    relatedProducts: products.slice(0, 3),
    relatedProductsSidebarEnabled: false,
    relatedProductsBottomEnabled: false,
    variations: [
        {
            id: product.id,
            productId: product.id,
            name: 'Standard',
            code: null,
            sku: `S-${product.id}`,
            stocks: [{ id: 1, warehouseId: 1, stockQty: 20 }],
            image: product.thumbnailImg,
            basePrice: product.basePrice,
            discountedBasePrice: product.basePrice,
            basePriceWithTax: product.basePrice,
            discountedBasePriceWithTax: product.basePrice,
            tax: 0,
            dealEnds: null,
            isPreOrder: false,
            isSoldOut: false,
            preOrderData: null,
        },
    ],
    variationCombinations: [],
    hasSizeChart: false,
    sizeChartImage: null,
    sizeChartTable: null,
    reviews: [],
    totalSold: 0,
});
let carts: any[] = [];
let failSubscription =
    new URLSearchParams(location.search).get('previewState') ===
    'subscribe-error';
let failCatalog =
    new URLSearchParams(location.search).get('previewState') ===
    'catalog-error';
(window as any).designPreview = {
    products,
    setSubscriptionFailure: (value: boolean) => {
        failSubscription = value;
    },
    setCatalogFailure: (value: boolean) => {
        failCatalog = value;
    },
};
const originalFetch = window.fetch.bind(window);
window.fetch = async (input, options) => {
    const request = new Request(input, options);
    const url = new URL(request.url);
    if (!url.pathname.startsWith('/__design-preview-api__/'))
        return originalFetch(input, options);
    const path = url.pathname
        .replace('/__design-preview-api__/', '')
        .replace(/^\//, '');
    let result: any = {};
    let status = 200;
    if (path === 'homepage/best-sellers')
        result =
            new URLSearchParams(location.search).get('previewState') === 'empty'
                ? []
                : products.slice(0, 3);
    else if (path === 'homepage/new-arrivals')
        result =
            new URLSearchParams(location.search).get('previewState') === 'empty'
                ? []
                : products.slice(3, 9);
    else if (path === 'homepage/theme-one/banner')
        result = { showThemeOneMainBanner: '1', banners: [] };
    else if (path === 'homepage/theme-one/featured-categories')
        result = { showFeaturedCategories: '1', categories };
    else if (path === 'homepage/theme-one/single-image-banner')
        result = { showThemeOneSingleImageBanner: '1' };
    else if (path === 'homepage/theme-one/reviews')
        result = {
            showThemeOneReviewSection: true,
            reviews: [
                {
                    avatar: '/images/reviews/1.png',
                    name: 'Carmen Hawkins',
                    designation: 'Sample review · design preview',
                    rating: '4.0',
                    text: 'A lovely addition to my morning routine. Everything feels thoughtfully chosen and beautifully simple.',
                },
                {
                    avatar: '/images/reviews/2.png',
                    name: 'Mae James',
                    designation: 'Sample review · design preview',
                    rating: '5.0',
                    text: 'My new everyday favourites. The little details make taking time for myself feel a little more special.',
                },
            ],
        };
    else if (path === 'homepage/footer')
        result = {
            homeFooterDescription:
                'A little care. A little confidence. Discover everyday beauty, inspired by nature and made for you.',
            defaultPages: [],
            quickLinkPages: [],
            homeFooterInfoItems: [],
            homeFooterEmail: 'hello@example.com',
        };
    else if (path === 'homepage/homepage-popup')
        result = { showHomepagePopup: '0' };
    else if (path === 'categories') result = { categories };
    else if (path === 'carts/check-abandoned')
        result = { is_abandoned: false, carts: [] };
    else if (path.startsWith('products/details/'))
        result = details(
            products.find((p) => p.slug === path.split('/').pop()) ||
                products[0],
        );
    else if (path === 'products')
        result = {
            products: {
                data: products.filter(
                    (p) =>
                        !url.searchParams.get('query') ||
                        p.name
                            .toLowerCase()
                            .includes(
                                url.searchParams.get('query')!.toLowerCase(),
                            ),
                ),
                meta: { current_page: 1, last_page: 1, total: 9, per_page: 12 },
            },
            brands: [],
            rootCategories: categories,
            filterAttributes: [],
            selectedCategory: null,
            selectedBrand: null,
            priceFilter: { minPrice: 0, maxPrice: 2000 },
        };
    else if (path === 'carts/create') {
        const data = await request.formData();
        const product = products.find(
            (p) => p.id === Number(data.get('productVariationId')),
        )!;
        const existing = carts.find((cart) => cart.product.id === product.id);
        if (existing) existing.qty++;
        else
            carts.push({
                id: product.id,
                product,
                variation: details(product).variations[0],
                shop: product.shop,
                qty: 1,
                warehouseId: 1,
            });
        result = { carts };
    } else if (path === 'carts/update' || path === 'carts/delete') {
        const data = await request.formData();
        const id = Number(data.get('id'));
        const action = data.get('action');
        carts = carts.flatMap((cart) =>
            cart.id !== id
                ? [cart]
                : path === 'carts/delete' || action === 'delete'
                ? []
                : [
                      {
                          ...cart,
                          qty: cart.qty + (action === 'increase' ? 1 : -1),
                      },
                  ],
        );
        result = { carts };
    } else if (path === 'subscribe') {
        result = {};
        if (failSubscription) status = 500;
    } else if (path === 'carts') result = { carts };
    else {
        status = 404;
        result = {
            message: 'This endpoint is not simulated in the design preview.',
        };
    }
    if (
        failCatalog &&
        (path === 'homepage/best-sellers' || path === 'homepage/new-arrivals')
    )
        status = 500;
    return new Response(
        JSON.stringify({
            success: status === 200,
            result,
            message:
                status === 200
                    ? 'Design preview only'
                    : 'Preview request failed. Please try again.',
        }),
        { status, headers: { 'Content-Type': 'application/json' } },
    );
};
await import('./render');
