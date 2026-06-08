import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { createRoot } from 'react-dom/client';
import { Toaster } from 'react-hot-toast';
import { Provider } from 'react-redux';
import { BrowserRouter } from 'react-router-dom';
import AppContextProvider from '../react/Context';
import PopupIndex from '../react/components/popup/PopupIndex';
import '../react/i18next';
import CartList from '../react/pages/CartList';
import { store } from '../store';

// collapse sidebar
const sidebar = document.querySelector('.sidebar') as HTMLDivElement | null;
if (sidebar) {
    sidebar.classList.add('in-active');
    sidebar.style.width = '90px';
}

const queryClient = new QueryClient();

// Get initial data from the DOM
const appElement = document.getElementById('app');
const initialCarts = appElement?.dataset.initialCarts
    ? JSON.parse(appElement.dataset.initialCarts)
    : null;
const filterData = appElement?.dataset.filterData
    ? JSON.parse(appElement.dataset.filterData)
    : null;

const root = createRoot(appElement!);
root.render(
    <Provider store={store}>
        <QueryClientProvider client={queryClient}>
            <BrowserRouter>
                <AppContextProvider>
                    <Toaster />
                    <CartList initialCarts={initialCarts} filterData={filterData} />
                    <PopupIndex />
                </AppContextProvider>
            </BrowserRouter>
        </QueryClientProvider>
    </Provider>,
);
