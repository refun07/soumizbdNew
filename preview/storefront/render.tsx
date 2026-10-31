import { createRoot } from 'react-dom/client';
import { Provider } from 'react-redux';
import { BrowserRouter, Link, Route, Routes } from 'react-router-dom';
import { Toaster } from 'react-hot-toast';
import i18n from 'i18next';
import { initReactI18next } from 'react-i18next';
import { store } from '../../resources/frontend/store/store';
import { setAuthData } from '../../resources/frontend/store/features/auth/authSlice';
import PrimaryLayout from '../../resources/frontend/layouts/PrimaryLayout';
import BotanicalHome from '../../resources/frontend/pages/home/BotanicalHome';
import Search from '../../resources/frontend/pages/search-filter/Search';
import Category from '../../resources/frontend/pages/search-filter/Category';
import OnSaleProducts from '../../resources/frontend/pages/search-filter/OnSaleProducts';
import ProductDetails from '../../resources/frontend/pages/ProductDetails';
import '../../resources/frontend/index.css';

await i18n.use(initReactI18next).init({
    lng: 'en-US',
    fallbackLng: 'en-US',
    resources: { 'en-US': { translation: {} } },
    interpolation: { escapeValue: false },
});
store.dispatch(setAuthData({ isLoading: false, guestUserId: 1 }));
(window as any).designPreview.store = store;
createRoot(document.getElementById('app')!).render(
    <Provider store={store}>
        <BrowserRouter basename="/preview/storefront">
            <Routes>
                <Route element={<PrimaryLayout />}>
                    <Route index element={<BotanicalHome />} />
                    <Route path="product" element={<Search />} />
                    <Route path="product/:slug" element={<ProductDetails />} />
                    <Route
                        path="categories/:categorySlug"
                        element={<Category />}
                    />
                    <Route
                        path="on-sale-products"
                        element={<OnSaleProducts />}
                    />
                    <Route
                        path="*"
                        element={
                            <div
                                className="bot-container"
                                style={{ paddingBlock: 90 }}
                            >
                                <h1 style={{ fontSize: 32 }}>Design preview</h1>
                                <p>
                                    This isolated preview uses a sample catalog.
                                    Account, checkout, and other pages use the
                                    original application when connected to a
                                    database.
                                </p>
                                <Link
                                    to="/"
                                    className="bot-button"
                                    style={{ marginTop: 24 }}
                                >
                                    Back to the homepage
                                </Link>
                            </div>
                        }
                    />
                </Route>
            </Routes>
            <Toaster position="bottom-center" />
            <div
                style={{
                    position: 'fixed',
                    bottom: 12,
                    left: 12,
                    font: '11px Arial',
                    background: '#fff',
                    color: '#005b2d',
                    border: '1px solid #bfcebf',
                    padding: '8px 12px',
                    borderRadius: 20,
                    zIndex: 3,
                }}
            >
                Design preview · sample catalog
            </div>
        </BrowserRouter>
    </Provider>,
);
