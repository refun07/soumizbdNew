export function route(
    name: string,
    params: Record<string, string | number> = {},
): string {
    const routeMap: Record<string, string> = {
        'admin.orders.index': '/admin/orders',
        'admin.orders.show': '/admin/orders/:id',
        'admin.orders.downloadInvoice': '/admin/invoice-download/:id',
        'admin.orders.downloadChallan': '/admin/challan-download/:id',
    };

    let path = routeMap[name];

    if (!path) {
        throw new Error(`Route "${name}" is not defined.`);
    }

    Object.keys(params).forEach((key) => {
        path = path.replace(`:${key}`, encodeURIComponent(String(params[key])));
    });

    return path;
}
