import { useState } from 'react';
import { LiaPrintSolid } from 'react-icons/lia';
import { currencyFormatter } from '../../../frontend/utils/numberFormatter';
import Button from '../../react/components/inputs/Button';
import { normalizeAssetPath } from '../utils/helpers';
import { InvoiceData } from '../types';
import { buildA4InvoiceHtml } from '../utils/a4InvoiceTemplate';
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

        printWindow.document.write(buildA4InvoiceHtml(invoiceData));
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
                                    <th className="text-left py-3 px-[15px] font-bold text-[14px] border-r border-black w-[82px]">Size</th>
                                    <th className="text-right py-3 px-[15px] font-bold text-[14px] border-r border-black w-[100px]">Unit Price</th>
                                    <th className="text-center py-3 px-[15px] font-bold text-[14px] border-r border-black w-[60px]">Qty</th>
                                    <th className="text-right py-3 px-[15px] font-bold text-[14px] w-[120px]">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                {invoiceData.items.map((item, index) => (
                                    <tr key={index} className="border-b border-black last:border-b-0">
                                        <td className="text-left py-3 px-[15px] text-[14px] border-r border-black text-gray-800 dark:text-gray-200">{item.name}</td>
                                        <td className="text-left py-3 px-[15px] text-[14px] border-r border-black text-gray-800 dark:text-gray-200">{item.size || '-'}</td>
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
