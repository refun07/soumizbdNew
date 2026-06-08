import { useState } from 'react';
import { LiaPrintSolid, LiaTimesSolid } from 'react-icons/lia';
import { currencyFormatter } from '../../../frontend/utils/numberFormatter';
import Button from '../../react/components/inputs/Button';
import { normalizeAssetPath } from '../utils/helpers';
import { IPaymentSplit, InvoiceData } from '../types';
import { buildThermalReceiptHtml } from '../utils/thermalReceiptTemplate';

interface Props {
    isActive: boolean;
    onClose: () => void;
    invoiceData: InvoiceData | null;
}

type PrintFormat = 'thermal' | 'a4';

const paymentMethodLabels: Record<string, string> = {
    cash: 'Cash',
    card: 'Card',
    cash_on_delivery: 'Cash on Delivery',
    card_on_delivery: 'Card on Delivery',
    split: 'Split Payment',
};

const InvoiceModal = ({ isActive, onClose, invoiceData }: Props) => {
    const [printFormat, setPrintFormat] = useState<PrintFormat>('thermal');

    if (!isActive || !invoiceData) return null;

    const isSplitPayment =
        invoiceData.payment_method === 'split' &&
        invoiceData.payment_splits &&
        invoiceData.payment_splits.length > 0;

    const formatCurrency = (amount: number) => {
        return currencyFormatter(amount);
    };

    const handlePrint = () => {
        const printWindow = window.open('', '_blank');
        if (!printWindow) return;

        if (printFormat === 'thermal') {
            printWindow.document.write(buildThermalReceiptHtml(invoiceData));
            printWindow.document.close();
            printWindow.focus();
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 500);
            return;
        }

        const logoHtml = invoiceData.business.logo
            ? `<img src="${normalizeAssetPath(invoiceData.business.logo)}" alt="Logo" style="width: 140px; height: auto;">`
            : `<div style="width: 140px; height: 140px; background: #4B186F; display: flex; align-items: center; justify-content: center;">
                <span style="color: #9BD530; font-size: 36px; font-weight: 700; letter-spacing: 2px;">${invoiceData.business.name.substring(0, 2).toUpperCase()}</span>
               </div>`;

        const notesSection = invoiceData.notes
            ? `<div style="margin-bottom: 0;">
                <h3 style="font-weight: 700; font-size: 16px; text-transform: uppercase; margin-bottom: 12px;">NOTES / TERMS:</h3>
                <p style="font-size: 14px;">${invoiceData.notes}</p>
               </div>`
            : '';

        const itemsRows = invoiceData.items.map(item => `
            <tr style="border-bottom: 1px solid #000;">
                <td style="text-align: left; padding: 12px 15px; font-size: 14px; border-right: 1px solid #000;">${item.name}</td>
                <td style="text-align: right; padding: 12px 15px; font-size: 14px; border-right: 1px solid #000;">${formatCurrency(item.unit_price)}</td>
                <td style="text-align: center; padding: 12px 15px; font-size: 14px; border-right: 1px solid #000;">${item.quantity}</td>
                <td style="text-align: right; padding: 12px 15px; font-size: 14px;">${formatCurrency(item.amount)}</td>
            </tr>
        `).join('');

        let paymentSection = '';
        if (isSplitPayment) {
            const splitsRows = invoiceData.payment_splits.map(split => `
                <tr style="border-bottom: 1px solid #000;">
                    <td style="text-align: left; padding: 8px 12px; font-size: 14px; border-right: 1px solid #000;">${paymentMethodLabels[split.method] || split.method}</td>
                    <td style="text-align: right; padding: 8px 12px; font-size: 14px;">${formatCurrency(split.amount)}</td>
                </tr>
            `).join('');

            paymentSection = `
                <h3 class="footer-title">PAYMENT METHOD (SPLIT)</h3>
                <table style="width: 100%; border-collapse: collapse; border: 1px solid #000; margin-top: 8px; margin-bottom: 12px;">
                    <thead>
                        <tr style="background: #f5f5f5;">
                            <th style="text-align: left; padding: 8px 12px; font-size: 13px; border-right: 1px solid #000; border-bottom: 1px solid #000; font-weight: 600;">Method</th>
                            <th style="text-align: right; padding: 8px 12px; font-size: 13px; border-bottom: 1px solid #000; font-weight: 600;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${splitsRows}
                    </tbody>
                </table>
                <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 14px; margin-top: 8px; padding-top: 8px; border-top: 2px solid #000;">
                    <span>Total Paid:</span>
                    <span>${formatCurrency(invoiceData.total_paid)}</span>
                </div>
                ${invoiceData.change_return > 0 ? `
                <div style="display: flex; justify-content: space-between; color: #16a34a; font-size: 14px; margin-top: 4px;">
                    <span>Change Return:</span>
                    <span>${formatCurrency(invoiceData.change_return)}</span>
                </div>
                ` : ''}
            `;
        } else {
            paymentSection = `
                <h3 class="footer-title">PAYMENT METHOD</h3>
                <p>${paymentMethodLabels[invoiceData.payment_method] || invoiceData.payment_method}</p>
            `;
        }

        const printContent = `
            <!DOCTYPE html>
            <html>
            <head>
                <title>Invoice - ${invoiceData.order_number}</title>
                <style>
                    @page { margin: 0.5cm; }
                    @media print {
                        @page { margin: 0.5cm; }
                        body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                    }
                    * { box-sizing: border-box; }
                    body {
                        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                        font-size: 14px;
                        line-height: 1.5;
                        color: #000;
                        margin: 0;
                        padding: 40px;
                        width: 800px;
                        max-width: 100%;
                    }
                    .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 40px; }
                    h1 { font-size: 42px; font-weight: 800; margin: 0; text-transform: uppercase; letter-spacing: -1px; }
                    .invoice-meta { margin-top: 20px; font-size: 16px; line-height: 1.6; }
                    .invoice-meta strong { font-weight: 700; }
                    .info-section { display: grid; grid-template-columns: 1fr 1fr; gap: 50px; margin-bottom: 40px; }
                    .section-title { font-weight: 700; font-size: 16px; text-transform: uppercase; margin-bottom: 12px; }
                    .text-right { text-align: right; }
                    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; border: 1px solid #000; }
                    th { font-weight: 700; font-size: 14px; padding: 12px 15px; text-align: left; border-right: 1px solid #000; border-bottom: 1px solid #000; }
                    th:not(:last-child) { border-right: 1px solid #000; }
                    .text-right-th { text-align: right; }
                    .text-center-th { text-align: center; }
                    .summary-section { display: grid; grid-template-columns: 1fr 1fr; gap: 50px; }
                    .summary-table { margin-left: auto; width: 250px; border: 1px solid #000; }
                    .summary-row { display: grid; grid-template-columns: 1fr 1fr; border-bottom: 1px solid #000; }
                    .summary-row:last-child { border-bottom: none; }
                    .summary-row td { padding: 8px 15px; font-size: 14px; }
                    .summary-row td:last-child { text-align: right; }
                    .total-row { background: #f5f5f5; font-weight: 700; font-size: 16px; }
                    .footer { display: grid; grid-template-columns: 1fr 1fr; gap: 50px; margin-top: 60px; padding-top: 30px; }
                    .footer-title { font-weight: 700; font-size: 16px; text-transform: uppercase; margin-bottom: 12px; }
                    .print-button { display: none; }
                </style>
            </head>
            <body>
                <div class="header">
                    <div>
                        <h1>INVOICE</h1>
                        <div class="invoice-meta">
                            <p><strong>Invoice Number:</strong> ${invoiceData.order_number}</p>
                            <p><strong>Invoice Date:</strong> ${invoiceData.order_date}</p>
                        </div>
                    </div>
                    ${logoHtml}
                </div>

                <div class="info-section">
                    <div>
                        <h2 class="section-title">${invoiceData.business.name}</h2>
                        <p style="white-space: pre-line;">${invoiceData.business.address}</p>
                        ${invoiceData.business.phone ? `<p>${invoiceData.business.phone}</p>` : ''}
                        ${invoiceData.business.email ? `<p>${invoiceData.business.email}</p>` : ''}
                    </div>
                    <div class="text-right">
                        <h2 class="section-title">BILL TO</h2>
                        <p style="font-weight: 700;">${invoiceData.customer.name}</p>
                        ${invoiceData.customer.address ? `<p style="white-space: pre-line;">${invoiceData.customer.address}</p>` : ''}
                        ${invoiceData.customer.phone ? `<p>${invoiceData.customer.phone}</p>` : ''}
                        ${invoiceData.customer.email ? `<p>${invoiceData.customer.email}</p>` : ''}
                    </div>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th style="width: auto;">Item & Description</th>
                            <th class="text-right-th" style="width: 100px;">Unit Price</th>
                            <th class="text-center-th" style="width: 60px;">Qty</th>
                            <th class="text-right-th" style="width: 120px;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${itemsRows}
                    </tbody>
                </table>

                <div style="display: flex; justify-content: space-between;">
                    ${invoiceData.notes ? `
                    <div style="flex: 1;">
                        <h3 style="font-weight: 700; font-size: 16px; text-transform: uppercase; margin-bottom: 12px;">NOTES / TERMS:</h3>
                        <p style="font-size: 14px;">${invoiceData.notes}</p>
                    </div>
                    ` : ''}
                    <div style="${invoiceData.notes ? '' : 'width: 100%;'}">
                        <div class="summary-table">
                            <div class="summary-row">
                                <td>Sub-Total</td>
                                <td>${formatCurrency(invoiceData.subtotal)}</td>
                            </div>
                            ${invoiceData.tax_amount > 0 ? `
                            <div class="summary-row">
                                <td>Tax (${invoiceData.tax_rate}%)</td>
                                <td>${formatCurrency(invoiceData.tax_amount)}</td>
                            </div>
                            ` : ''}
                            ${invoiceData.shipping > 0 ? `
                            <div class="summary-row">
                                <td>Shipping</td>
                                <td>${formatCurrency(invoiceData.shipping)}</td>
                            </div>
                            ` : ''}
                            ${invoiceData.discount > 0 ? `
                            <div class="summary-row">
                                <td>Discount</td>
                                <td>-${formatCurrency(invoiceData.discount)}</td>
                            </div>
                            ` : ''}
                            <div class="summary-row total-row">
                                <td>TOTAL</td>
                                <td>${formatCurrency(invoiceData.total)}</td>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="footer">
                    <div>
                        ${paymentSection}
                    </div>
                    <div class="text-right">
                        <h3 class="footer-title">PREPARED BY</h3>
                        <p style="font-weight: 700;">${invoiceData.business.name}</p>
                        <p>Sales Department</p>
                    </div>
                </div>
            </body>
            </html>
        `;

        printWindow.document.write(printContent);
        printWindow.document.close();
        printWindow.focus();

        setTimeout(() => {
            printWindow.print();
            printWindow.close();
        }, 500);
    };

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 overflow-auto">
            <style>{`
                @media print {
                    body { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
                }
            `}</style>

            <div className="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-[800px] max-h-[90vh] overflow-y-auto text-gray-800 dark:text-gray-100">
                {/* Invoice Preview */}
                <div className="p-[60px]" id="invoice-preview">
                    {/* Header */}
                    <div className="flex justify-between items-start mb-[40px]">
                        <div>
                            <h1 className="text-[42px] font-extrabold tracking-tight uppercase leading-none text-gray-900 dark:text-gray-100">INVOICE</h1>
                            <p className="mt-5 text-base text-gray-700 dark:text-gray-300">
                                <span className="font-bold text-gray-800 dark:text-gray-100">Invoice Number:</span> {invoiceData.order_number}
                            </p>
                            <p className="text-base text-gray-700 dark:text-gray-300">
                                <span className="font-bold text-gray-800 dark:text-gray-100">Invoice Date:</span> {invoiceData.order_date}
                            </p>
                        </div>
                        {invoiceData.business.logo ? (
                            <img
                                src={normalizeAssetPath(invoiceData.business.logo)}
                                alt="Logo"
                                className="w-[140px] h-auto"
                            />
                        ) : (
                            <div className="w-[140px] h-[140px] bg-[#4B186F] flex items-center justify-center">
                                <span className="text-[#9BD530] text-4xl font-bold tracking-wider">{invoiceData.business.name.substring(0, 2).toUpperCase()}</span>
                            </div>
                        )}
                    </div>

                        {/* Invoice Content */}
                    <div id="invoice-content">
                        {/* Business & Customer Info */}
                        <div className="grid grid-cols-2 gap-[50px] mb-[40px]">
                            <div>
                                <h2 className="font-bold text-[16px] uppercase mb-3 text-gray-800 dark:text-gray-100">{invoiceData.business.name}</h2>
                                <p className="text-[14px] whitespace-pre-line leading-relaxed text-gray-700 dark:text-gray-300">{invoiceData.business.address}</p>
                                {invoiceData.business.phone && (
                                    <p className="text-[14px] mt-1 text-gray-700 dark:text-gray-300">{invoiceData.business.phone}</p>
                                )}
                                {invoiceData.business.email && (
                                    <p className="text-[14px] text-gray-700 dark:text-gray-300">{invoiceData.business.email}</p>
                                )}
                            </div>
                            <div className="text-right">
                                <h2 className="font-bold text-[16px] uppercase mb-3 text-gray-800 dark:text-gray-100">BILL TO</h2>
                                <p className="font-bold text-[14px] text-gray-800 dark:text-gray-100">{invoiceData.customer.name}</p>
                                {invoiceData.customer.address && (
                                    <p className="text-[14px] whitespace-pre-line mt-1 text-gray-700 dark:text-gray-300">{invoiceData.customer.address}</p>
                                )}
                                {invoiceData.customer.phone && (
                                    <p className="text-[14px] mt-1 text-gray-700 dark:text-gray-300">{invoiceData.customer.phone}</p>
                                )}
                                {invoiceData.customer.email && (
                                    <p className="text-[14px] text-gray-700 dark:text-gray-300">{invoiceData.customer.email}</p>
                                )}
                            </div>
                        </div>

                        {/* Items Table */}
                        <table className="w-full border-collapse mb-[20px] border border-black">
                            <thead>
                                <tr className="border-b border-black">
                                    <th className="text-left py-3 px-[15px] font-bold text-[14px] border-r border-black w-auto">Item & Description</th>
                                    <th className="text-right py-3 px-[15px] font-bold text-[14px] border-r border-black w-[100px]">Unit Price</th>
                                    <th className="text-center py-3 px-[15px] font-bold text-[14px] border-r border-black w-[60px]">Qty</th>
                                    <th className="text-right py-3 px-[15px] font-bold text-[14px] w-[120px]">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                {invoiceData.items.map((item, index) => (
                                    <tr key={index} className="border-b border-black last:border-b-0">
                                        <td className="text-left py-3 px-[15px] text-[14px] border-r border-black text-gray-800 dark:text-gray-200">{item.name}</td>
                                        <td className="text-right py-3 px-[15px] text-[14px] border-r border-black text-gray-800 dark:text-gray-200">{formatCurrency(item.unit_price)}</td>
                                        <td className="text-center py-3 px-[15px] text-[14px] border-r border-black text-gray-800 dark:text-gray-200">{item.quantity}</td>
                                        <td className="text-right py-3 px-[15px] text-[14px] text-gray-800 dark:text-gray-200">{formatCurrency(item.amount)}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>

                        {/* Notes & Totals */}
                        <div className="flex justify-between">
                            {invoiceData.notes && (
                                <div style={{ flex: 1 }}>
                                    <h3 className="font-bold text-[16px] uppercase mb-3 text-gray-800 dark:text-gray-100">NOTES / TERMS:</h3>
                                    <p className="text-[14px] text-gray-700 dark:text-gray-300">{invoiceData.notes}</p>
                                </div>
                            )}
                            <div className={invoiceData.notes ? '' : 'w-full'}>
                                <div className="border border-black ml-auto" style={{ width: '250px' }}>
                                    <div className="grid grid-cols-2 border-b border-black">
                                        <span className="text-left py-2 px-[15px] text-[14px] text-gray-700 dark:text-gray-300">Sub-Total</span>
                                        <span className="text-right py-2 px-[15px] text-[14px] text-gray-700 dark:text-gray-300">{formatCurrency(invoiceData.subtotal)}</span>
                                    </div>
                                    {invoiceData.tax_amount > 0 && (
                                        <div className="grid grid-cols-2 border-b border-black">
                                            <span className="text-left py-2 px-[15px] text-[14px] text-gray-700 dark:text-gray-300">Tax ({invoiceData.tax_rate}%)</span>
                                            <span className="text-right py-2 px-[15px] text-[14px] text-gray-700 dark:text-gray-300">{formatCurrency(invoiceData.tax_amount)}</span>
                                        </div>
                                    )}
                                    {invoiceData.shipping > 0 && (
                                        <div className="grid grid-cols-2 border-b border-black">
                                            <span className="text-left py-2 px-[15px] text-[14px] text-gray-700 dark:text-gray-300">Shipping</span>
                                            <span className="text-right py-2 px-[15px] text-[14px] text-gray-700 dark:text-gray-300">{formatCurrency(invoiceData.shipping)}</span>
                                        </div>
                                    )}
                                    {invoiceData.discount > 0 && (
                                        <div className="grid grid-cols-2 border-b border-black">
                                            <span className="text-left py-2 px-[15px] text-[14px] text-gray-700 dark:text-gray-300">Discount</span>
                                            <span className="text-right py-2 px-[15px] text-[14px] text-gray-700 dark:text-gray-300">-{formatCurrency(invoiceData.discount)}</span>
                                        </div>
                                    )}
                                    <div className="grid grid-cols-2 bg-gray-100 dark:bg-gray-700">
                                        <span className="text-left py-3 px-[15px] font-bold text-[16px] text-gray-800 dark:text-gray-100">TOTAL</span>
                                        <span className="text-right py-3 px-[15px] font-bold text-[16px] text-gray-800 dark:text-gray-100">{formatCurrency(invoiceData.total)}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Footer - Payment Method */}
                        <div className="grid grid-cols-2 gap-[50px] mt-[100px] pt-8 border-t border-gray-200 dark:border-gray-600">
                            <div>
                                <h3 className="font-bold text-[16px] uppercase mb-3 text-gray-800 dark:text-gray-100">
                                    {isSplitPayment ? 'PAYMENT METHOD (SPLIT)' : 'PAYMENT METHOD'}
                                </h3>
                                
                                {isSplitPayment ? (
                                    <div className="space-y-3">
                                        <table className="w-full border-collapse border border-gray-300 dark:border-gray-600">
                                            <thead>
                                                <tr className="bg-gray-100 dark:bg-gray-700">
                                                    <th className="text-left py-2 px-3 text-[13px] border-r border-gray-300 dark:border-gray-600 font-semibold">Method</th>
                                                    <th className="text-right py-2 px-3 text-[13px] font-semibold">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {invoiceData.payment_splits.map((split, index) => (
                                                    <tr key={index} className="border-b border-gray-300 dark:border-gray-600 last:border-b-0">
                                                        <td className="text-left py-2 px-3 text-[14px] text-gray-700 dark:text-gray-300">
                                                            {paymentMethodLabels[split.method] || split.method}
                                                        </td>
                                                        <td className="text-right py-2 px-3 text-[14px] text-gray-700 dark:text-gray-300">
                                                            {formatCurrency(split.amount)}
                                                        </td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                        
                                        <div className="flex justify-between font-bold text-[14px] pt-2 border-t-2 border-gray-300 dark:border-gray-600">
                                            <span>Total Paid:</span>
                                            <span>{formatCurrency(invoiceData.total_paid)}</span>
                                        </div>
                                        {invoiceData.change_return > 0 && (
                                            <div className="flex justify-between text-green-600 text-[14px]">
                                                <span>Change Return:</span>
                                                <span>{formatCurrency(invoiceData.change_return)}</span>
                                            </div>
                                        )}
                                    </div>
                                ) : (
                                    <p className="text-[14px] text-gray-700 dark:text-gray-300">
                                        {paymentMethodLabels[invoiceData.payment_method] || invoiceData.payment_method}
                                    </p>
                                )}
                            </div>
                            <div className="text-right">
                                <h3 className="font-bold text-[16px] uppercase mb-3 text-gray-800 dark:text-gray-100">PREPARED BY</h3>
                                <p className="font-bold text-[14px] text-gray-800 dark:text-gray-100">{invoiceData.business.name}</p>
                                <p className="text-[14px] text-gray-700 dark:text-gray-300">Sales Department</p>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Action Buttons */}
                <div className="flex flex-wrap items-center justify-between gap-3 p-6 border-t border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700">
                    <div className="flex items-center gap-2">
                        <span className="text-sm text-gray-600 dark:text-gray-300 font-medium">Print format:</span>
                        <button
                            onClick={() => setPrintFormat('thermal')}
                            className={`px-3 py-1.5 text-sm rounded-md border transition-colors ${
                                printFormat === 'thermal'
                                    ? 'bg-indigo-600 text-white border-indigo-600'
                                    : 'bg-white dark:bg-gray-600 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-500 hover:bg-gray-50 dark:hover:bg-gray-500'
                            }`}
                        >
                            4×6 Thermal
                        </button>
                        <button
                            onClick={() => setPrintFormat('a4')}
                            className={`px-3 py-1.5 text-sm rounded-md border transition-colors ${
                                printFormat === 'a4'
                                    ? 'bg-indigo-600 text-white border-indigo-600'
                                    : 'bg-white dark:bg-gray-600 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-500 hover:bg-gray-50 dark:hover:bg-gray-500'
                            }`}
                        >
                            A4 Document
                        </button>
                    </div>
                    <div className="flex items-center gap-3">
                        <Button variant="light" onClick={onClose}>
                            Close
                        </Button>
                        <Button variant="primary" onClick={handlePrint} className="flex items-center gap-2">
                            <LiaPrintSolid className="w-5 h-5" />
                            Print Invoice
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default InvoiceModal;
