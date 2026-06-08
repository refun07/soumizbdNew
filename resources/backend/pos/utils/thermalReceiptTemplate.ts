import { currencyFormatter } from '../../../frontend/utils/numberFormatter';
import { normalizeAssetPath } from './helpers';
import { InvoiceData } from '../types';

const paymentMethodLabels: Record<string, string> = {
    cash: 'Cash',
    card: 'Card',
    cash_on_delivery: 'Cash on Delivery',
    card_on_delivery: 'Card on Delivery',
    split: 'Split Payment',
};

const formatCurrency = (amount: number) => currencyFormatter(amount);

export const buildThermalReceiptHtml = (invoiceData: InvoiceData): string => {
    const logoHtml = invoiceData.business.logo
        ? `<img src="${normalizeAssetPath(invoiceData.business.logo)}" alt="Logo" style="max-width: 80px; max-height: 60px; margin: 0 auto 6px; display: block;">`
        : `<div style="width: 50px; height: 50px; background: #4B186F; display: flex; align-items: center; justify-content: center; margin: 0 auto 6px; border-radius: 4px;">
                <span style="color: #9BD530; font-size: 20px; font-weight: 700; letter-spacing: 1px;">${invoiceData.business.name.substring(0, 2).toUpperCase()}</span>
           </div>`;

    const businessContactLines = [
        invoiceData.business.address,
        invoiceData.business.phone,
        invoiceData.business.email,
    ].filter(Boolean).map(line => `<div style="font-size: 10px; color: #333;">${line}</div>`).join('');

    const customerInfoLines = [
        invoiceData.customer.name !== 'Walk In Customer' ? invoiceData.customer.name : null,
        invoiceData.customer.phone,
        invoiceData.customer.address,
    ].filter(Boolean).map(line => `<div style="font-size: 10px;">${line}</div>`).join('');

    const customerSection = customerInfoLines
        ? `<div style="margin-bottom: 8px;">
                <div style="font-size: 9px; font-weight: 700; text-transform: uppercase; color: #666; margin-bottom: 2px;">Customer</div>
                ${customerInfoLines}
           </div>`
        : '';

    const itemsRows = invoiceData.items.map(item => `
        <tr>
            <td style="padding: 3px 0; font-size: 10px; vertical-align: top; width: 100%;">
                <div style="font-weight: 600; word-break: break-word;">${item.name}</div>
                <div style="font-size: 9px; color: #555;">${item.quantity} x ${formatCurrency(item.unit_price)}</div>
            </td>
            <td style="padding: 3px 0; font-size: 10px; text-align: right; vertical-align: top; white-space: nowrap;">${formatCurrency(item.amount)}</td>
        </tr>
    `).join('');

    const summaryRows = [
        { label: 'Subtotal', value: formatCurrency(invoiceData.subtotal) },
        invoiceData.tax_amount > 0 ? { label: `Tax (${invoiceData.tax_rate}%)`, value: formatCurrency(invoiceData.tax_amount) } : null,
        invoiceData.shipping > 0 ? { label: 'Shipping', value: formatCurrency(invoiceData.shipping) } : null,
        invoiceData.discount > 0 ? { label: 'Discount', value: `-${formatCurrency(invoiceData.discount)}` } : null,
    ]
        .filter(Boolean)
        .map(
            row => `<tr>
            <td style="padding: 2px 0; font-size: 10px;">${row!.label}</td>
            <td style="padding: 2px 0; font-size: 10px; text-align: right; white-space: nowrap;">${row!.value}</td>
        </tr>`,
        )
        .join('');

    const isSplitPayment =
        invoiceData.payment_method === 'split' &&
        invoiceData.payment_splits &&
        invoiceData.payment_splits.length > 0;

    let paymentSection = '';
    if (isSplitPayment) {
        const splitsRows = invoiceData.payment_splits
            .map(
                split => `<tr>
                <td style="padding: 2px 0; font-size: 10px;">${paymentMethodLabels[split.method] || split.method}</td>
                <td style="padding: 2px 0; font-size: 10px; text-align: right; white-space: nowrap;">${formatCurrency(split.amount)}</td>
            </tr>`,
            )
            .join('');

        paymentSection = `
            <div style="margin-top: 8px;">
                <div style="font-size: 9px; font-weight: 700; text-transform: uppercase; color: #666; margin-bottom: 2px;">Payment (Split)</div>
                <table style="width: 100%; border-collapse: collapse;">${splitsRows}</table>
                <div style="display: flex; justify-content: space-between; font-size: 10px; font-weight: 700; margin-top: 4px; border-top: 1px dashed #999; padding-top: 4px;">
                    <span>Total Paid</span>
                    <span>${formatCurrency(invoiceData.total_paid)}</span>
                </div>
                ${
                    invoiceData.change_return > 0
                        ? `<div style="display: flex; justify-content: space-between; font-size: 10px; color: #16a34a; margin-top: 2px;">
                        <span>Change</span>
                        <span>${formatCurrency(invoiceData.change_return)}</span>
                    </div>`
                        : ''
                }
            </div>`;
    } else {
        paymentSection = `
            <div style="display: flex; justify-content: space-between; margin-top: 8px; font-size: 10px;">
                <span style="font-weight: 700; text-transform: uppercase; color: #666;">Payment</span>
                <span>${paymentMethodLabels[invoiceData.payment_method] || invoiceData.payment_method}</span>
            </div>
            ${
                invoiceData.payment_method !== 'cash_on_delivery' && invoiceData.payment_method !== 'card_on_delivery'
                    ? `<div style="display: flex; justify-content: space-between; font-size: 10px; margin-top: 4px;">
                    <span style="font-weight: 700; text-transform: uppercase; color: #666;">Paid</span>
                    <span style="font-weight: 700;">${formatCurrency(invoiceData.total_paid)}</span>
                </div>
                ${
                    invoiceData.change_return > 0
                        ? `<div style="display: flex; justify-content: space-between; font-size: 10px; color: #16a34a; margin-top: 2px;">
                        <span>Change</span>
                        <span>${formatCurrency(invoiceData.change_return)}</span>
                    </div>`
                        : ''
                }`
                    : ''
            }`;
    }

    const qrSection = invoiceData.qr_code_image
        ? `<div style="text-align: center; margin-top: 12px;">
                <img src="${invoiceData.qr_code_image}" alt="QR Code" style="width: 80px; height: 80px; margin: 0 auto; display: block;">
                <div style="font-size: 8px; color: #666; margin-top: 4px;">Scan to verify invoice</div>
           </div>`
        : '';

    const notesSection = invoiceData.notes
        ? `<div style="margin-top: 10px; border-top: 1px dashed #999; padding-top: 6px;">
                <div style="font-size: 9px; font-weight: 700; text-transform: uppercase; color: #666; margin-bottom: 2px;">Notes</div>
                <div style="font-size: 9px; color: #444; white-space: pre-line;">${invoiceData.notes}</div>
           </div>`
        : '';

    return `<!DOCTYPE html>
<html>
<head>
    <title>Receipt - ${invoiceData.order_number}</title>
    <style>
        @page { size: 4in 6in; margin: 2mm; }
        @media print {
            @page { size: 4in 6in; margin: 2mm; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; margin: 0; }
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 10px;
            line-height: 1.4;
            color: #000;
            width: 4in;
            padding: 2mm;
        }
    </style>
</head>
<body>
    <div style="text-align: center; margin-bottom: 8px;">
        ${logoHtml}
        <div style="font-size: 14px; font-weight: 700; letter-spacing: 0.5px;">${invoiceData.business.name}</div>
        ${businessContactLines}
    </div>

    <div style="border-top: 1px dashed #999; border-bottom: 1px dashed #999; padding: 6px 0; margin-bottom: 8px;">
        <div style="display: flex; justify-content: space-between; font-size: 10px; margin-bottom: 2px;">
            <span style="font-weight: 700;">Receipt</span>
            <span>${invoiceData.order_number}</span>
        </div>
        <div style="display: flex; justify-content: space-between; font-size: 9px; color: #555;">
            <span>${invoiceData.order_date}</span>
        </div>
    </div>

    ${customerSection}

    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="border-bottom: 1px dashed #000;">
                <th style="text-align: left; padding-bottom: 4px; font-size: 9px; text-transform: uppercase; font-weight: 700;">Item</th>
                <th style="text-align: right; padding-bottom: 4px; font-size: 9px; text-transform: uppercase; font-weight: 700;">Amount</th>
            </tr>
        </thead>
        <tbody>
            ${itemsRows}
        </tbody>
    </table>

    <div style="border-top: 1px dashed #000; margin-top: 4px; padding-top: 4px;">
        <table style="width: 100%; border-collapse: collapse;">
            ${summaryRows}
        </table>
        <div style="display: flex; justify-content: space-between; font-size: 13px; font-weight: 700; margin-top: 6px; border-top: 2px solid #000; padding-top: 6px;">
            <span>TOTAL</span>
            <span>${formatCurrency(invoiceData.total)}</span>
        </div>
    </div>

    ${paymentSection}
    ${notesSection}

    <div style="border-top: 1px dashed #999; margin-top: 12px; padding-top: 6px; text-align: center;">
        <div style="font-size: 10px; font-weight: 600;">Thank you for your purchase!</div>
    </div>

    ${qrSection}
</body>
</html>`;
};

export const printThermalReceipt = (invoiceData: InvoiceData) => {
    const printWindow = window.open('', '_blank');
    if (!printWindow) return;

    printWindow.document.write(buildThermalReceiptHtml(invoiceData));
    printWindow.document.close();
    printWindow.focus();

    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 500);
};
