import { currencyFormatter } from '../../../frontend/utils/numberFormatter';
import { normalizeAssetPath } from './helpers';
import { InvoiceData } from '../types';

const HALF_PAGE_ITEM_LIMIT = 20;

const paymentMethodLabels: Record<string, string> = {
    cash: 'Cash',
    card: 'Card',
    cash_on_delivery: 'Cash On Delivery',
    card_on_delivery: 'Card On Delivery',
    split: 'Split Payment',
};

const formatCurrency = (amount: number) => currencyFormatter(amount);

const escapeHtml = (value: string | number | null | undefined): string =>
    String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');

export const invoiceDensityForCount = (itemCount: number): string => {
    if (itemCount > HALF_PAGE_ITEM_LIMIT) {
        return 'full';
    }
    if (itemCount <= 4) {
        return 'comfortable';
    }
    if (itemCount <= 8) {
        return 'medium';
    }
    if (itemCount <= 12) {
        return 'compact';
    }
    if (itemCount <= 16) {
        return 'tight';
    }

    return 'dense';
};

const a4InvoiceCss = `
    @page { size: A4; margin: 8mm 10mm 8mm 10mm; }
    @media print {
        html, body { width: 210mm; height: 297mm; }
        body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .print-button { display: none !important; }
    }
    * { box-sizing: border-box; }
    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Hind Siliguri', sans-serif;
        color: #0f172a;
        background: #ffffff;
        line-height: 1.25;
        margin: 0;
        padding: 0;
        font-size: 11px;
    }
    table { width: 100%; border-collapse: collapse; table-layout: fixed; }
    td, th { padding: 0; vertical-align: top; }
    .invoice-box { border: 0; padding: 0; background: #ffffff; }
    .layout-half {
        max-height: 136mm;
        overflow: hidden;
        page-break-inside: avoid;
    }
    .invoice-header td { vertical-align: middle; }
    .company-logo-cell { width: 14%; padding-right: 6px; }
    .company-info-cell { width: 48%; }
    .invoice-meta-cell { width: 38%; text-align: right; vertical-align: top; }
    .brand-logo { max-width: 64px; max-height: 28px; }
    .brand { font-weight: 700; font-size: 16px; color: #d90429; }
    .shop-title { font-size: 16px; font-weight: 800; color: #0f172a; line-height: 1.05; text-transform: uppercase; }
    .shop-line { font-size: 10px; line-height: 1.35; color: #0f172a; white-space: nowrap; }
    .invoice-title { font-size: 26px; font-weight: 400; color: #000; line-height: 1; }
    .invoice-meta { font-size: 10px; line-height: 1.4; color: #0f172a; margin-top: 5px; }
    .invoice-meta-line { white-space: nowrap; }
    .divider { border-top: 1px solid #cbd5e1; margin: 6px 0; height: 1px; }
    .section-title { font-weight: 800; font-size: 12px; margin-bottom: 3px; color: #0f172a; text-transform: uppercase; }
    .bill-table td { vertical-align: middle; }
    .customer { font-size: 11px; line-height: 1.35; color: #0f172a; word-break: break-word; }
    .customer-label { font-weight: 700; }
    .bill-rule { border-top: 1px solid #cbd5e1; width: 92%; margin: 3px 0 5px; }
    .qr-placeholder {
        width: 42px; height: 42px; border: 3px solid #111827; margin: 0 auto;
        text-align: center; font-size: 8px; line-height: 36px; color: #111827; font-weight: 800;
    }
    .qr-code { display: block; width: 42px; height: 42px; margin: 0 auto; }
    table.cod-box {
        width: 100%;
        border: 1px solid #0f172a;
        border-collapse: separate !important;
        border-spacing: 0;
        table-layout: auto;
        color: #0f172a;
    }
    table.cod-box td.cod-cell {
        padding: 12px 16px !important;
        border: 0 !important;
        text-align: center;
        vertical-align: middle !important;
    }
    .cod-label { color: #64748b; font-size: 9px; letter-spacing: .6px; text-transform: uppercase; line-height: 1.5; border: 0 !important; }
    .cod-amount { font-size: 16px; line-height: 1.5; font-weight: 800; letter-spacing: .2px; border: 0 !important; }
    .items { margin-top: 4px; border: 0; table-layout: fixed; }
    .items th {
        font-size: 10px; font-weight: 700; text-align: center;
        border-top: 1px solid #cbd5e1; border-bottom: 1px solid #cbd5e1;
        padding: 3px; color: #0f172a; background: #f1f5f9;
    }
    .items td { font-size: 10px; padding: 3px; border-bottom: 1px solid #cbd5e1; }
    .item-name { line-height: 1.2; color: #172033; word-break: break-word; }
    .price, .qty { white-space: nowrap; }
    .price { text-align: right; font-weight: 700; color: #000; }
    .qty { text-align: center; font-weight: 700; color: #000; }
    .summary { width: 42%; margin-left: auto; margin-top: 4px; font-size: 10px; }
    .summary td { padding: 2px 0; }
    .summary .label { font-weight: 400; text-align: left; }
    .summary .value { text-align: right; white-space: nowrap; font-weight: 700; }
    .summary-total td { border-top: 1px solid #cbd5e1; font-weight: 800; padding-top: 4px; }
    .footer-line {
        margin-top: 6px; border-top: 1px solid #cbd5e1; padding-top: 4px;
        font-size: 8px; text-align: center; color: #64748b; letter-spacing: .6px;
    }

    .density-comfortable .shop-title { font-size: 16px; }
    .density-comfortable .invoice-title { font-size: 26px; }
    .density-comfortable .items th, .density-comfortable .items td { font-size: 10px; padding: 4px 3px; }
    .density-comfortable .qr-code, .density-comfortable .qr-placeholder { width: 44px; height: 44px; }
    .density-comfortable table.cod-box td.cod-cell { padding: 14px 16px !important; }

    .density-medium .shop-title { font-size: 14px; }
    .density-medium .shop-line { font-size: 9px; }
    .density-medium .invoice-title { font-size: 22px; }
    .density-medium .invoice-meta { font-size: 9px; margin-top: 3px; }
    .density-medium .section-title { font-size: 11px; }
    .density-medium .customer { font-size: 10px; line-height: 1.28; }
    .density-medium .items th, .density-medium .items td { font-size: 9px; padding: 2.5px 3px; }
    .density-medium .summary { font-size: 9px; margin-top: 3px; }
    .density-medium .summary td { padding: 1.5px 0; }
    .density-medium .divider { margin: 5px 0; }
    .density-medium .qr-code, .density-medium .qr-placeholder { width: 38px; height: 38px; }
    .density-medium table.cod-box td.cod-cell { padding: 12px 14px !important; }
    .density-medium .cod-amount { font-size: 14px; }
    .density-medium .brand-logo { max-width: 56px; max-height: 24px; }

    .density-compact .shop-title { font-size: 13px; }
    .density-compact .shop-line { font-size: 8.5px; }
    .density-compact .invoice-title { font-size: 18px; }
    .density-compact .invoice-meta { font-size: 8.5px; margin-top: 2px; }
    .density-compact .section-title { font-size: 10px; margin-bottom: 2px; }
    .density-compact .customer { font-size: 9px; line-height: 1.22; }
    .density-compact .items th, .density-compact .items td { font-size: 8.5px; padding: 1.5px 2px; }
    .density-compact .summary { font-size: 8.5px; margin-top: 2px; }
    .density-compact .summary td { padding: 1px 0; }
    .density-compact .divider { margin: 4px 0; }
    .density-compact .qr-code, .density-compact .qr-placeholder { width: 32px; height: 32px; }
    .density-compact table.cod-box td.cod-cell { padding: 10px 12px !important; }
    .density-compact .cod-label { font-size: 8px; }
    .density-compact .cod-amount { font-size: 12px; }
    .density-compact .brand-logo { max-width: 48px; max-height: 22px; }
    .density-compact .footer-line { margin-top: 4px; padding-top: 3px; font-size: 7px; }

    .density-tight .shop-title { font-size: 12px; }
    .density-tight .shop-line { font-size: 8px; }
    .density-tight .invoice-title { font-size: 16px; }
    .density-tight .invoice-meta { font-size: 8px; margin-top: 2px; }
    .density-tight .section-title { font-size: 9px; margin-bottom: 1px; }
    .density-tight .customer { font-size: 8px; line-height: 1.18; }
    .density-tight .items { margin-top: 2px; }
    .density-tight .items th, .density-tight .items td { font-size: 8px; padding: 1px 2px; }
    .density-tight .summary { font-size: 8px; margin-top: 2px; }
    .density-tight .summary td { padding: 0.5px 0; }
    .density-tight .divider { margin: 3px 0; }
    .density-tight .qr-code, .density-tight .qr-placeholder { width: 26px; height: 26px; }
    .density-tight table.cod-box td.cod-cell { padding: 8px 10px !important; }
    .density-tight .cod-label { font-size: 7px; }
    .density-tight .cod-amount { font-size: 11px; }
    .density-tight .brand-logo { max-width: 42px; max-height: 18px; }
    .density-tight .footer-line { margin-top: 3px; padding-top: 2px; font-size: 7px; }

    .density-dense .shop-title { font-size: 11px; }
    .density-dense .shop-line { font-size: 7.5px; }
    .density-dense .invoice-title { font-size: 14px; }
    .density-dense .invoice-meta { font-size: 7.5px; margin-top: 1px; }
    .density-dense .section-title { font-size: 8px; margin-bottom: 1px; }
    .density-dense .customer { font-size: 7.5px; line-height: 1.15; }
    .density-dense .items { margin-top: 1px; }
    .density-dense .items th, .density-dense .items td { font-size: 7px; padding: 0 2px; }
    .density-dense .summary { font-size: 7px; margin-top: 1px; }
    .density-dense .summary td { padding: 0; }
    .density-dense .divider { margin: 1.5px 0; }
    .density-dense .qr-code, .density-dense .qr-placeholder { width: 20px; height: 20px; }
    .density-dense table.cod-box td.cod-cell { padding: 7px 9px !important; }
    .density-dense .cod-label { font-size: 6px; }
    .density-dense .cod-amount { font-size: 9px; }
    .density-dense .brand-logo { max-width: 36px; max-height: 16px; }
    .density-dense .footer-line { display: none; }

    .density-full .shop-title { font-size: 18px; }
    .density-full .shop-line { font-size: 11px; }
    .density-full .invoice-title { font-size: 28px; }
    .density-full .invoice-meta { font-size: 11px; }
    .density-full .section-title { font-size: 13px; }
    .density-full .customer { font-size: 12px; line-height: 1.4; }
    .density-full .items th, .density-full .items td { font-size: 11px; padding: 5px 4px; }
    .density-full .summary { font-size: 11px; margin-top: 8px; }
    .density-full .summary td { padding: 3px 0; }
    .density-full .divider { margin: 8px 0; }
    .density-full .qr-code, .density-full .qr-placeholder { width: 52px; height: 52px; }
    .density-full table.cod-box td.cod-cell { padding: 14px 16px !important; }
    .density-full .cod-amount { font-size: 18px; }
    .density-full .brand-logo { max-width: 72px; max-height: 32px; }
    .density-full .footer-line { margin-top: 10px; padding-top: 6px; font-size: 9px; }
`;

export const formatInvoiceNumber = (value: string | number | null | undefined): string => {
    const raw = String(value ?? '').replace(/^[#\s]+/, '');
    if (!raw) {
        return 'RM';
    }

    return raw.toUpperCase().startsWith('RM') ? raw : `RM${raw}`;
};

export const buildA4InvoiceHtml = (invoiceData: InvoiceData): string => {
    const itemCount = invoiceData.items?.length ?? 0;
    const isHalfPage = itemCount <= HALF_PAGE_ITEM_LIMIT;
    const density = invoiceDensityForCount(itemCount);
    const invoiceNumber = formatInvoiceNumber(invoiceData.order_number);
    const isCod = invoiceData.payment_method === 'cash_on_delivery' || invoiceData.payment_method === 'cod';
    const paymentLabel = paymentMethodLabels[invoiceData.payment_method] || invoiceData.payment_method || 'Payment';
    const amountLabel = isCod ? 'COD' : 'TOTAL';
    const amountValue = invoiceData.total;
    const logoHtml = invoiceData.business.logo
        ? `<img src="${normalizeAssetPath(invoiceData.business.logo)}" alt="logo" class="brand-logo">`
        : `<div class="brand">${escapeHtml(invoiceData.business.name.substring(0, 2).toUpperCase())}</div>`;

    const customerRows = [
        invoiceData.customer.name ? `<div><span class="customer-label">Name :</span> ${escapeHtml(invoiceData.customer.name)}</div>` : '',
        invoiceData.customer.phone ? `<div><span class="customer-label">Phone :</span> ${escapeHtml(invoiceData.customer.phone)}</div>` : '',
        invoiceData.customer.address ? `<div><span class="customer-label">Address :</span> ${escapeHtml(invoiceData.customer.address)}</div>` : '',
    ].join('');

    const itemsRows = invoiceData.items
        .map(
            (item, index) => `
            <tr>
                <td class="qty">${index + 1}</td>
                <td class="item-name">${escapeHtml(item.name)}</td>
                <td class="qty">${escapeHtml(item.size || '-')}</td>
                <td class="qty">${escapeHtml(item.quantity)}</td>
                <td class="price">${escapeHtml(formatCurrency(item.unit_price))}</td>
                <td class="price">${escapeHtml(formatCurrency(item.amount))}</td>
            </tr>`,
        )
        .join('');

    const summaryRows = [
        `<tr><td class="label">Subtotal :</td><td class="value">${escapeHtml(formatCurrency(invoiceData.subtotal))}</td></tr>`,
        invoiceData.tax_amount > 0
            ? `<tr><td class="label">Tax :</td><td class="value">${escapeHtml(formatCurrency(invoiceData.tax_amount))}</td></tr>`
            : '',
        invoiceData.discount > 0
            ? `<tr><td class="label">Discount :</td><td class="value" style="color:#b91c1c;">-${escapeHtml(formatCurrency(invoiceData.discount))}</td></tr>`
            : '',
        invoiceData.shipping > 0
            ? `<tr><td class="label">Shipping</td><td class="value">${escapeHtml(formatCurrency(invoiceData.shipping))}</td></tr>`
            : '',
        `<tr class="summary-total"><td class="label">Total</td><td class="value">${escapeHtml(formatCurrency(invoiceData.total))}</td></tr>`,
    ].join('');

    const qrHtml = invoiceData.qr_code_image
        ? `<img src="${invoiceData.qr_code_image}" alt="Order QR code" class="qr-code">`
        : `<div class="qr-placeholder">QR</div>`;

    return `<!DOCTYPE html>
<html>
<head>
    <title>Invoice - ${escapeHtml(invoiceNumber)}</title>
    <style>${a4InvoiceCss}</style>
</head>
<body>
    <div class="invoice-box layout-${isHalfPage ? 'half' : 'full'} density-${density}">
        <table class="invoice-header">
            <tr>
                <td class="company-logo-cell">${logoHtml}</td>
                <td class="company-info-cell">
                    <div class="shop-title">${escapeHtml(invoiceData.business.name)}</div>
                    <div class="shop-line">Hotline: ${escapeHtml(invoiceData.business.phone || '01765765647')}</div>
                    ${invoiceData.business.address ? `<div class="shop-line">${escapeHtml(invoiceData.business.address)}</div>` : ''}
                </td>
                <td class="invoice-meta-cell">
                    <div class="invoice-title">Invoice</div>
                    <div class="invoice-meta">
                        <div class="invoice-meta-line">Invoice No : ${escapeHtml(invoiceNumber)}</div>
                        <div class="invoice-meta-line">Date : ${escapeHtml(invoiceData.order_date)}</div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="divider"></div>

        <table class="bill-table">
            <tr>
                <td style="width: 45%; padding-right: 7px;">
                    <div class="section-title">Bill To</div>
                    <div class="bill-rule"></div>
                    <div class="customer">${customerRows}</div>
                </td>
                <td style="width: 16%; text-align: center;">${qrHtml}</td>
                <td style="width: 39%; padding-left: 12px;">
                    <table class="cod-box" width="100%" cellpadding="12" cellspacing="0">
                        <tr>
                            <td class="cod-cell">
                                <span class="cod-label">${escapeHtml(paymentLabel)}</span>
                                <br><br>
                                <span class="cod-amount">${amountLabel} : ${escapeHtml(formatCurrency(amountValue))}</span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <div class="divider"></div>

        <div class="section-title" style="text-transform: none;">Product Details</div>
        <table class="items">
            <thead>
                <tr>
                    <th style="width: 8%;">S.No</th>
                    <th style="width: 38%; text-align: left;">Product</th>
                    <th style="width: 14%;">Size</th>
                    <th style="width: 12%;">Quantity</th>
                    <th style="width: 14%; text-align: right;">Unit price</th>
                    <th style="width: 14%; text-align: right;">Total price</th>
                </tr>
            </thead>
            <tbody>${itemsRows}</tbody>
        </table>

        <table class="summary">${summaryRows}</table>
        <div class="footer-line">Thank you for shopping with ${escapeHtml(invoiceData.business.name)}</div>
    </div>
</body>
</html>`;
};

export { HALF_PAGE_ITEM_LIMIT };
