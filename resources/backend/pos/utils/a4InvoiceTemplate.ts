import { currencyFormatter } from '../../../frontend/utils/numberFormatter';
import { normalizeAssetPath } from './helpers';
import { InvoiceData } from '../types';

const HALF_PAGE_ITEM_LIMIT = 10;

const paymentMethodLabels: Record<string, string> = {
    cash: 'Cash',
    card: 'Card',
    cash_on_delivery: 'Cash On Delivery',
    card_on_delivery: 'Card On Delivery',
    split: 'Split Payment',
};

const formatCurrency = (amount: number) =>
    String(currencyFormatter(amount) ?? '').replace(/ /g, '\u00A0');

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
    if (itemCount <= 1) {
        return 'spacious';
    }
    if (itemCount <= 3) {
        return 'few';
    }
    if (itemCount <= 5) {
        return 'comfortable';
    }
    if (itemCount <= 8) {
        return 'medium';
    }

    return 'compact';
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
    .shop-line { font-size: 12px; line-height: 1.35; color: #0f172a; white-space: nowrap; }
    .invoice-title { font-size: 26px; font-weight: 400; color: #000; line-height: 1; }
    .invoice-meta { font-size: 10px; line-height: 1.4; color: #0f172a; margin-top: 5px; }
    .invoice-meta-line { white-space: nowrap; }
    .divider { border-top: 1px solid #cbd5e1; margin: 6px 0; height: 1px; }
    .section-title { font-weight: 800; font-size: 12px; margin-bottom: 3px; color: #0f172a; text-transform: uppercase; }
    .bill-table td { vertical-align: middle; }
    .customer { font-size: 13px; line-height: 1.35; color: #0f172a; word-break: break-word; }
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
        padding: 3px 1px; color: #0f172a; background: #f1f5f9;
        white-space: nowrap !important; word-break: keep-all; overflow-wrap: normal; hyphens: none;
    }
    .items td { font-size: 10px; padding: 3px; border-bottom: 1px solid #cbd5e1; }
    .item-name { line-height: 1.2; color: #172033; word-break: break-word; }
    .price, .qty { white-space: nowrap !important; }
    .price { text-align: right; font-weight: 700; color: #000; white-space: nowrap !important; }
    .amount-inline { white-space: nowrap !important; }
    .items td.price { white-space: nowrap !important; }
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

    .density-spacious .shop-title,
    .density-few .shop-title,
    .density-comfortable .shop-title { font-size: 18px; }
    .density-spacious .shop-line,
    .density-few .shop-line,
    .density-comfortable .shop-line { font-size: 13px; }
    .density-spacious .invoice-title,
    .density-few .invoice-title,
    .density-comfortable .invoice-title { font-size: 30px; }
    .density-spacious .invoice-meta,
    .density-few .invoice-meta,
    .density-comfortable .invoice-meta { font-size: 12px; margin-top: 6px; }
    .density-spacious .section-title,
    .density-few .section-title,
    .density-comfortable .section-title { font-size: 14px; }
    .density-spacious .customer,
    .density-few .customer,
    .density-comfortable .customer { font-size: 15px; line-height: 1.4; }
    .density-spacious .summary,
    .density-few .summary,
    .density-comfortable .summary { font-size: 13px; margin-top: 8px; }
    .density-spacious .summary td,
    .density-few .summary td,
    .density-comfortable .summary td { padding: 3px 0; }
    .density-spacious .divider,
    .density-few .divider,
    .density-comfortable .divider { margin: 8px 0; }
    .density-spacious .qr-code, .density-spacious .qr-placeholder,
    .density-few .qr-code, .density-few .qr-placeholder,
    .density-comfortable .qr-code, .density-comfortable .qr-placeholder { width: 52px; height: 52px; }
    .density-spacious table.cod-box td.cod-cell,
    .density-few table.cod-box td.cod-cell,
    .density-comfortable table.cod-box td.cod-cell { padding: 16px 18px !important; }
    .density-spacious .cod-amount,
    .density-few .cod-amount,
    .density-comfortable .cod-amount { font-size: 20px; }
    .density-spacious .brand-logo,
    .density-few .brand-logo,
    .density-comfortable .brand-logo { max-width: 72px; max-height: 32px; }
    .density-spacious .footer-line,
    .density-few .footer-line,
    .density-comfortable .footer-line { margin-top: 8px; padding-top: 5px; font-size: 9px; }

    .density-spacious .shop-title { font-size: 20px; }
    .density-spacious .shop-line { font-size: 14px; }
    .density-spacious .invoice-title { font-size: 32px; }
    .density-spacious .invoice-meta { font-size: 12px; margin-top: 6px; }
    .density-spacious .section-title { font-size: 15px; }
    .density-spacious .customer { font-size: 16px; line-height: 1.4; }
    .density-spacious .items { margin-top: 6px; }
    .density-spacious .items th, .density-spacious .items td { font-size: 16px; padding: 5mm 1mm; }
    .density-spacious .items td.price { padding-left: 1mm; padding-right: 1mm; }
    .density-spacious .item-name { line-height: 1.4; }
    .density-spacious .summary { font-size: 13px; margin-top: 8px; }
    .density-spacious .qr-code, .density-spacious .qr-placeholder { width: 56px; height: 56px; }
    .density-spacious table.cod-box td.cod-cell { padding: 16px 18px !important; }
    .density-spacious .cod-amount { font-size: 22px; }
    .density-spacious .brand-logo { max-width: 80px; max-height: 36px; }
    .density-spacious .divider { margin: 8px 0; }
    .density-spacious .footer-line { margin-top: 8px; padding-top: 5px; font-size: 10px; }

    .density-few .items { margin-top: 5px; }
    .density-few .items th, .density-few .items td { font-size: 14px; padding: 2.2mm 1.5mm; }
    .density-few .item-name { line-height: 1.3; }

    .density-comfortable .items { margin-top: 3px; }
    .density-comfortable .items th, .density-comfortable .items td { font-size: 12px; padding: 1.1mm 1mm; }
    .density-comfortable .item-name { line-height: 1.25; }

    .density-medium .shop-title { font-size: 17px; }
    .density-medium .shop-line { font-size: 13px; }
    .density-medium .invoice-title { font-size: 28px; }
    .density-medium .invoice-meta { font-size: 11px; margin-top: 4px; }
    .density-medium .section-title { font-size: 13px; }
    .density-medium .customer { font-size: 14px; line-height: 1.32; }
    .density-medium .items { margin-top: 3px; }
    .density-medium .items th, .density-medium .items td { font-size: 11px; padding: 1mm 0.8mm; }
    .density-medium .summary { font-size: 11px; margin-top: 5px; }
    .density-medium .summary td { padding: 2px 0; }
    .density-medium .divider { margin: 6px 0; }
    .density-medium .qr-code, .density-medium .qr-placeholder { width: 46px; height: 46px; }
    .density-medium table.cod-box td.cod-cell { padding: 13px 15px !important; }
    .density-medium .cod-amount { font-size: 18px; }
    .density-medium .brand-logo { max-width: 64px; max-height: 28px; }
    .density-medium .footer-line { margin-top: 6px; padding-top: 4px; font-size: 8px; }

    .density-compact .shop-title { font-size: 16px; }
    .density-compact .shop-line { font-size: 12px; }
    .density-compact .invoice-title { font-size: 24px; }
    .density-compact .invoice-meta { font-size: 10px; margin-top: 3px; }
    .density-compact .section-title { font-size: 12px; margin-bottom: 2px; }
    .density-compact .customer { font-size: 13px; line-height: 1.28; }
    .density-compact .items { margin-top: 2px; }
    .density-compact .items th, .density-compact .items td { font-size: 10px; padding: 1.1mm 0.8mm; }
    .density-compact .summary { font-size: 10px; margin-top: 3px; }
    .density-compact .summary td { padding: 1.5px 0; }
    .density-compact .divider { margin: 5px 0; }
    .density-compact .qr-code, .density-compact .qr-placeholder { width: 42px; height: 42px; }
    .density-compact table.cod-box td.cod-cell { padding: 12px 14px !important; }
    .density-compact .cod-label { font-size: 8px; }
    .density-compact .cod-amount { font-size: 16px; }
    .density-compact .brand-logo { max-width: 56px; max-height: 24px; }
    .density-compact .footer-line { margin-top: 5px; padding-top: 3px; font-size: 8px; }

    .density-full .shop-title { font-size: 18px; }
    .density-full .shop-line { font-size: 13px; }
    .density-full .invoice-title { font-size: 28px; }
    .density-full .invoice-meta { font-size: 11px; }
    .density-full .section-title { font-size: 13px; }
    .density-full .customer { font-size: 14px; line-height: 1.4; }
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
                <td class="price" nowrap><span class="amount-inline">${escapeHtml(formatCurrency(item.unit_price))}</span></td>
                <td class="price" nowrap><span class="amount-inline">${escapeHtml(formatCurrency(item.amount))}</span></td>
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
                    <th nowrap style="width: 8%;">S.No</th>
                    <th style="width: 30%; text-align: left;">Product</th>
                    <th nowrap style="width: 10%;">Size</th>
                    <th nowrap style="width: 14%;">Quantity</th>
                    <th nowrap style="width: 19%; text-align: right;">Unit price</th>
                    <th nowrap style="width: 19%; text-align: right;">Total price</th>
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
