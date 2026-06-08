import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { createRoot } from 'react-dom/client';
import { Toaster } from 'react-hot-toast';
import { Provider } from 'react-redux';
import { BrowserRouter, Routes, Route, useLocation, useParams, useNavigate } from 'react-router-dom';
import ConfirmStatusUpdatePopup from '../../frontend/components/popups/order/ConfirmStatusUpdatePopup';
import AppContextProvider from '../react/Context';
import PopupIndex from '../react/components/popup/PopupIndex';
import '../react/i18next';
import Orders from '../react/pages/Orders';
import OrderDetail from '../react/pages/OrderDetail';
import { store } from '../store';

// collapse sidebar
const sidebar = document.querySelector('.sidebar') as HTMLDivElement | null;
if (sidebar) {
    sidebar.classList.add('in-active');
    sidebar.style.width = '90px';
}
const queryClient = new QueryClient();

// Wrapper for Orders component
const OrdersWrapper: React.FC = () => {
    return <Orders />;
};

// Wrapper for OrderDetail component
const OrderDetailWrapper: React.FC = () => {
    const { orderId } = useParams<{ orderId: string }>();
    return <OrderDetail />;
};

const root = createRoot(document.getElementById('app')!);
root.render(
    <Provider store={store}>
        <QueryClientProvider client={queryClient}>
            <BrowserRouter>
                <AppContextProvider>
                    <Toaster />
                    <Routes>
                        <Route path="/admin/orders" element={<OrdersWrapper />} />
                        <Route path="/admin/orders/:orderId" element={<OrderDetailWrapper />} />
                    </Routes>
                    <PopupIndex />
                    <ConfirmStatusUpdatePopup />
                </AppContextProvider>
            </BrowserRouter>
        </QueryClientProvider>
    </Provider>,
);
