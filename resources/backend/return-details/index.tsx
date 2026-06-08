import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { createRoot } from 'react-dom/client';
import { Toaster } from 'react-hot-toast';
import { Provider } from 'react-redux';
import { BrowserRouter } from 'react-router-dom';
import AppContextProvider from '../react/Context';
import PopupIndex from '../react/components/popup/PopupIndex';
import '../react/i18next';
import ReturnDetails from '../react/pages/ReturnDetails';
import { store } from '../store';
// collapse sidebar
const sidebar = document.querySelector('.sidebar') as HTMLDivElement | null;
if (sidebar) {
    sidebar.classList.add('in-active');
    sidebar.style.width = '90px';
}
const queryClient = new QueryClient();

const root = createRoot(document.getElementById('app')!);
root.render(
    <Provider store={store}>
        <QueryClientProvider client={queryClient}>
            <BrowserRouter>
                <AppContextProvider>
                    <Toaster />
                    <ReturnDetails />
                    <PopupIndex />
                </AppContextProvider>
            </BrowserRouter>
        </QueryClientProvider>
    </Provider>,
);
