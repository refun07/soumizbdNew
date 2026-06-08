import React, { Suspense } from 'react';
import { createRoot } from 'react-dom/client';
import '../react/i18next';
import ErrorBoundary from './components/ErrorBoundary';
import App from './App';

declare global {
    interface Window {
        purchaseOrderData: {
            warehouses: Array<{ id: number; name: string }>;
            suppliers: Array<{ id: number; name: string }>;
            existingOrderItems: any[];
            initialFormData: any;
            useInventory: boolean;
            isEdit: boolean;
            isReturn: boolean;
            submitUrl: string;
            method: string;
        };
    }
}

const container = document.getElementById('purchase-order-root');

console.log('Purchase Order App - Container found:', !!container);
console.log('Purchase Order App - Data available:', !!window.purchaseOrderData);

if (container && window.purchaseOrderData) {
    const root = createRoot(container);
    const data = window.purchaseOrderData;

    console.log('Purchase Order App - Rendering with data:', data);

    root.render(
        <React.StrictMode>
            <ErrorBoundary>
                <Suspense fallback={<div className="p-8 text-center">Loading...</div>}>
                    <App
                        warehouses={data.warehouses}
                        suppliers={data.suppliers}
                        existingOrderItems={data.existingOrderItems}
                        initialFormData={data.initialFormData}
                        useInventory={data.useInventory}
                        isEdit={data.isEdit}
                        isReturn={data.isReturn}
                        submitUrl={data.submitUrl}
                        method={data.method}
                    />
                </Suspense>
            </ErrorBoundary>
        </React.StrictMode>
    );
} else {
    console.error('Purchase Order App - Missing container or data');
}
