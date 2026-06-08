import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { createRoot } from 'react-dom/client';
import { Toaster } from 'react-hot-toast';
import { Provider } from 'react-redux';
import { BrowserRouter, Routes, Route } from 'react-router-dom';
import AppContextProvider from '../Context';
import PopupIndex from '../components/popup/PopupIndex';
import '../i18next';
import PreOrdersPage from './PreOrders';
import IpBlocker from './security/IpBlocker';
import { store } from '../../store';

const sidebar = document.querySelector('.sidebar') as HTMLDivElement | null;
if (sidebar) {
    sidebar.classList.add('in-active');
    sidebar.style.width = '90px';
}

const queryClient = new QueryClient();

const PreOrdersWrapper: React.FC = () => {
    return <PreOrdersPage />;
};

const IpBlockerWrapper: React.FC = () => {
    return <IpBlocker />;
};

const root = createRoot(document.getElementById('app')!);
root.render(
    <Provider store={store}>
        <QueryClientProvider client={queryClient}>
            <BrowserRouter>
                <AppContextProvider>
                    <Toaster />
                    <Routes>
                        <Route path="/admin/pre-orders" element={<PreOrdersWrapper />} />
                        <Route path="/admin/configurations/ip-blocker" element={<IpBlockerWrapper />} />
                    </Routes>
                    <PopupIndex />
                </AppContextProvider>
            </BrowserRouter>
        </QueryClientProvider>
    </Provider>,
);
