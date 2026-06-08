import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { createRoot } from 'react-dom/client';
import { Toaster } from 'react-hot-toast';
import { Provider } from 'react-redux';
import { BrowserRouter, Route, Routes } from 'react-router-dom';
import AppContextProvider from '../react/Context';
import PopupIndex from '../react/components/popup/PopupIndex';
import '../react/i18next';
import TicketDetail from '../react/pages/support/TicketDetail';
import TicketsList from '../react/pages/support/TicketsList';
import { store } from '../store';

// collapse sidebar
const sidebar = document.querySelector('.sidebar') as HTMLDivElement | null;
if (sidebar) {
    sidebar.classList.add('in-active');
    sidebar.style.width = '90px';
}

const queryClient = new QueryClient();

const SupportApp = () => (
    <Provider store={store}>
        <QueryClientProvider client={queryClient}>
            <BrowserRouter>
                <AppContextProvider>
                    <Toaster />
                    <Routes>
                        <Route path="/admin/support/tickets" element={<TicketsList />} />
                        <Route path="/admin/support/tickets/:id" element={<TicketDetail />} />
                    </Routes>
                    <PopupIndex />
                </AppContextProvider>
            </BrowserRouter>
        </QueryClientProvider>
    </Provider>
);

const root = createRoot(document.getElementById('app')!);
root.render(<SupportApp />);