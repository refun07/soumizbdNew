import React, {
    ChangeEvent,
    KeyboardEvent,
    useCallback,
    useEffect,
    useMemo,
    useRef,
    useState,
} from 'react';
import toast from 'react-hot-toast';
import { BiExitFullscreen, BiExpand } from 'react-icons/bi';
import { FaListUl, FaThLarge, FaTrashAlt } from 'react-icons/fa';
import { FiSettings, FiEdit2 } from 'react-icons/fi';
import { IoHandRightOutline } from 'react-icons/io5';
import { LiaTimesSolid } from 'react-icons/lia';
import { AiOutlinePlus } from 'react-icons/ai';
import { Drawer } from 'vaul';
import { useSearchParams } from 'react-router-dom';
import {
    currencyFormatter,
    paddedNumber,
} from '../../frontend/utils/numberFormatter';
import Button from '../react/components/inputs/Button';
import Counter from '../react/components/inputs/Counter';
import Image from '../react/components/Image';
import Input from '../react/components/inputs/Input';
import SearchForm from '../react/components/inputs/SearchForm';
import SelectInput from '../react/components/inputs/SelectInput';
import Spinner from '../react/components/loader/Spinner';
import Pagination from '../react/components/pagination/Pagination';
import { IPaginationMeta } from '../react/types';
import { objectToFormData } from '../react/utils/ObjectFormData';
import { translate } from '../react/utils/translate';
import ProductCard from './components/ProductCard';
import InvoiceModal from './components/InvoiceModal';
import CustomerModal from './popup/CustomerModal';
import HoldOrdersModal from './popup/HoldOrdersModal';
import ModalWrapper from './popup/ModalWrapper';
import OrderHistoryModal from './popup/OrderHistoryModal';
import SubmitOrderModal from './popup/SubmitOrderModal';
import {
    IAddress,
    ICustomer,
    IFilterData,
    IFilters,
    IPosCartGroup,
    IPosProductVariation,
} from './types';
import {
    getCustomerFilter,
    getPosFilterData,
    getPosProductsVariations,
    holdOrder,
    addToPosCartGroup,
    updatePosCartItem,
    getAddresses,
} from './utils/actions';
import {
    getRemainingStockForVariation,
    getStockBadgeMeta,
    getStockForWarehouse,
} from './utils/helpers';

type ScanFeedback = {
    product: IPosProductVariation | null;
    message: string;
    tone: 'ok' | 'low' | 'out' | 'error';
};

type ProductViewMode = 'product-first' | 'classic';

type ProductGroup = {
    productId: number;
    productName: string;
    image: string;
    variations: IPosProductVariation[];
};

const PRODUCT_FIRST_VARIATION_LIMIT = 80;

const App = () => {
    const [searchParams, setSearchParams] = useSearchParams();
    const [isLoading, setIsLoading] = useState({
        loadingProduct: false,
        loadingSelectedProduct: true,
    });

    const [filterData, setFilterData] = useState<IFilterData>({
        warehouses: [],
        brands: [],
        categories: [],
    });
    const [filters, setFilters] = useState<IFilters>({
        page: 1,
        warehouseId: '',
        brandId: '',
        categoryId: '',
        searchKey: '',
    });
    const [posProducts, setPosProducts] = useState<IPosProductVariation[]>([]);
    const [paginationData, setPaginationData] = useState<IPaginationMeta>();

    const [customerFilters, setCustomerFilters] = useState<ICustomer[]>();
    const [customer, setCustomer] = useState<ICustomer>();
    const [address, setAddress] = useState<IAddress | null>(null);
    const [posCartGroup, setPosCartGroup] = useState<IPosCartGroup>();

    // Track pending customer from URL params for auto-selection
    const [pendingCustomerId, setPendingCustomerId] = useState<number | null>(null);
    const hasAutoSelectedCustomer = useRef(false);

    const [discount, setDiscount] = useState<number | ''>('');
    const [shipping, setShipping] = useState<number | ''>('');
    const [tax, setTax] = useState<number | ''>('');

    const [showInvoiceModal, setShowInvoiceModal] = useState(false);
    const [currentInvoiceData, setCurrentInvoiceData] = useState<any>(null);
    const [isFullscreen, setIsFullscreen] = useState(false);
    const [searchDraft, setSearchDraft] = useState('');
    const [scanQuery, setScanQuery] = useState('');
    const [scanFeedback, setScanFeedback] = useState<ScanFeedback | null>(null);
    const [stockOverride, setStockOverride] = useState(false);
    const [productViewMode, setProductViewMode] =
        useState<ProductViewMode>('product-first');
    const [selectedProductGroup, setSelectedProductGroup] =
        useState<ProductGroup | null>(null);

    const [isCustomerModalOpen, setIsCustomerModalOpen] = useState(false);
    const [customerModalViewMode, setCustomerModalViewMode] = useState<'select' | 'manage' | 'address-picker'>('select');
    const [modalAddresses, setModalAddresses] = useState<IAddress[]>([]);
    const [modalCustomer, setModalCustomer] = useState<ICustomer | null>(null);
    const [isAddingNewCustomer, setIsAddingNewCustomer] = useState(false);
    const [isFilterDialogOpen, setIsFilterDialogOpen] = useState(false);
    const [isCartDrawerOpen, setIsCartDrawerOpen] = useState(false);
    const [isMobile, setIsMobile] = useState(false);

    const canOverrideStock = Boolean(window.config?.canOverrideStock);
    const selectedWarehouseId = filters.warehouseId
        ? Number(filters.warehouseId)
        : null;
    const productGroups = useMemo(() => {
        const groupedProducts = new Map<number, ProductGroup>();

        posProducts.forEach((variation) => {
            const existingGroup = groupedProducts.get(variation.productId);

            if (existingGroup) {
                existingGroup.variations.push(variation);
                return;
            }

            groupedProducts.set(variation.productId, {
                productId: variation.productId,
                productName: variation.product.name,
                image: variation.image || variation.product.thumbnailImg,
                variations: [variation],
            });
        });

        return Array.from(groupedProducts.values());
    }, [posProducts]);

    useEffect(() => {
        const handleFullscreenChange = () => {
            setIsFullscreen(!!document.fullscreenElement);
        };
        document.addEventListener('fullscreenchange', handleFullscreenChange);
        return () => document.removeEventListener('fullscreenchange', handleFullscreenChange);
    }, []);

    useEffect(() => {
        const checkMobile = () => setIsMobile(window.innerWidth < 1024);
        checkMobile();
        window.addEventListener('resize', checkMobile);
        return () => window.removeEventListener('resize', checkMobile);
    }, []);

    useEffect(() => {
        if (!isMobile) return;
        const saved = localStorage.getItem('pos_mobile_filters');
        if (saved) {
            try {
                const parsed = JSON.parse(saved);
                const newParams = new URLSearchParams(searchParams);
                if (parsed.warehouseId && !newParams.get('warehouseId')) {
                    newParams.set('warehouseId', parsed.warehouseId);
                }
                if (parsed.brandId && !newParams.get('brandId')) {
                    newParams.set('brandId', parsed.brandId);
                }
                if (parsed.categoryId && !newParams.get('categoryId')) {
                    newParams.set('categoryId', parsed.categoryId);
                }
                if (newParams.toString() !== searchParams.toString()) {
                    setSearchParams(newParams, { replace: true });
                }
            } catch {
                // ignore parse errors
            }
        }
    }, [isMobile]);

    useEffect(() => {
        if (!isMobile) return;
        localStorage.setItem('pos_mobile_filters', JSON.stringify({
            warehouseId: filters.warehouseId,
            brandId: filters.brandId,
            categoryId: filters.categoryId,
        }));
    }, [filters, isMobile]);

    useEffect(() => {
        getPosFilterData().then((data) => setFilterData(data));
        getCustomerFilter().then((data) => setCustomerFilters(data));
    }, []);

    useEffect(() => {
        if (!canOverrideStock) {
            setStockOverride(false);
        }
    }, [canOverrideStock]);

    // Handle abandoned cart transfer - load cart from query params
    useEffect(() => {
        const posCartGroupId = searchParams.get('posCartGroupId');
        const customerId = searchParams.get('customerId');

        if (posCartGroupId) {
            // Store customerId for later auto-selection when customerFilters load
            if (customerId) {
                setPendingCustomerId(Number(customerId));
            }

            // Load the cart data
            loadAbandonedCartData(Number(posCartGroupId));

            // Clean up URL params
            const newSearchParams = new URLSearchParams(searchParams);
            newSearchParams.delete('posCartGroupId');
            newSearchParams.delete('customerId');
            setSearchParams(newSearchParams, { replace: true });
        }
    }, []);

    // Auto-select customer when both customerFilters and pendingCustomerId are available
    useEffect(() => {
        if (
            customerFilters &&
            pendingCustomerId !== null &&
            !hasAutoSelectedCustomer.current
        ) {
            const matchedCustomer = customerFilters.find(
                (c) => c.id === pendingCustomerId,
            );
            if (matchedCustomer) {
                setCustomer(matchedCustomer);
                hasAutoSelectedCustomer.current = true;
                setPendingCustomerId(null);
            }
        }
    }, [customerFilters, pendingCustomerId]);

    const loadAbandonedCartData = async (posCartGroupId: number) => {
        try {
            // Fetch the cart group data
            const response = await fetch(`/${window.location.pathname.split('/')[1]}/api/pos/cart/hold`);
            const result = await response.json();

            if (result.success && result.result) {
                // Find the specific cart group
                const cartGroup = result.result.find((group: any) => group.id === posCartGroupId);

                if (cartGroup) {
                    setPosCartGroup(cartGroup);

                    // Set the warehouse from the first cart item
                    if (cartGroup.posCarts && cartGroup.posCarts.length > 0) {
                        const firstCart = cartGroup.posCarts[0];
                        if (firstCart.warehouseId) {
                            // Update filters with warehouse
                            const newFilters = { ...filters, warehouseId: String(firstCart.warehouseId) };
                            setFilters(newFilters);

                            // Update search params
                            const newSearchParams = new URLSearchParams(searchParams);
                            newSearchParams.set('warehouseId', String(firstCart.warehouseId));
                            setSearchParams(newSearchParams);
                        }
                    }

                    toast.success('Cart loaded from abandoned order');
                } else {
                    toast.error('Cart not found');
                }
            }
        } catch (error) {
            console.error('Error loading abandoned cart:', error);
            toast.error('Failed to load cart data');
        }
    };

    useEffect(() => {
        if (!filters.warehouseId) {
            return;
        }
        setIsLoading((currentLoading) => ({
            ...currentLoading,
            loadingProduct: true,
        }));

        const productFilters: IFilters = {
            ...filters,
            limit:
                productViewMode === 'product-first'
                    ? PRODUCT_FIRST_VARIATION_LIMIT
                    : undefined,
        };

        const timerId = setTimeout(() => {
            getPosProductsVariations(productFilters)
                .then((data) => {
                    setPosProducts(data.data);
                    setPaginationData(data.meta);
                })
                .finally(() => {
                    setIsLoading((currentLoading) => ({
                        ...currentLoading,
                        loadingProduct: false,
                    }));
                });
        }, 500);

        return () => clearTimeout(timerId);
    }, [filters, productViewMode]);

    useEffect(() => {
        const filters: IFilters = {
            page: Number(searchParams.get('page')) || 1,
            warehouseId: searchParams.get('warehouseId'),
            brandId: searchParams.get('brandId'),
            categoryId: searchParams.get('categoryId'),
            searchKey: searchParams.get('searchKey'),
        };
        setFilters(filters);
    }, [searchParams]);

    useEffect(() => {
        setSearchDraft(searchParams.get('searchKey') || '');
    }, [searchParams]);

    const handleSearch = (e: ChangeEvent<HTMLInputElement>) => {
        setSearchDraft(e.target.value);
    };

    useEffect(() => {
        const timerId = setTimeout(() => {
            const nextSearchParams = new URLSearchParams(searchParams);
            const normalizedSearch = searchDraft.trim();

            if (normalizedSearch) {
                nextSearchParams.set('searchKey', searchDraft);
            } else {
                nextSearchParams.delete('searchKey');
            }

            nextSearchParams.set('page', '1');

            if (nextSearchParams.toString() !== searchParams.toString()) {
                setSearchParams(nextSearchParams, { replace: true });
            }
        }, 350);

        return () => clearTimeout(timerId);
    }, [searchDraft, searchParams, setSearchParams]);

    const handleFilter = (name: string, value: string | number) => {
        const nextSearchParams = new URLSearchParams(searchParams);

        if (value === '' || value === null) {
            nextSearchParams.delete(name);
        } else {
            nextSearchParams.set(name, String(value));
        }

        nextSearchParams.set('page', '1');
        setSearchParams(nextSearchParams);
    };

    const handleProductViewModeToggle = () => {
        setSelectedProductGroup(null);
        setProductViewMode((currentMode) =>
            currentMode === 'product-first' ? 'classic' : 'product-first',
        );

        const nextSearchParams = new URLSearchParams(searchParams);
        if (nextSearchParams.get('page') !== '1') {
            nextSearchParams.set('page', '1');
            setSearchParams(nextSearchParams);
        }
    };

    const findMatchedProduct = (
        products: IPosProductVariation[],
        query: string,
    ) => {
        const normalizedQuery = query.trim().toLowerCase();

        return (
            products.find(
                (product) =>
                    product.sku?.toLowerCase() === normalizedQuery ||
                    product.code?.toLowerCase() === normalizedQuery,
            ) ||
            products.find(
                (product) =>
                    product.product.name?.toLowerCase() === normalizedQuery,
            ) ||
            products[0] ||
            null
        );
    };

    const addProductToCart = async (product: IPosProductVariation) => {
        try {
            const productInCart = posCartGroup?.posCarts.find(
                (cart) => cart.productVariationId === product.id,
            );

            if (productInCart) {
                const posCarts = await updatePosCartItem(
                    objectToFormData({
                        id: productInCart.id,
                        action: 'increase',
                        overrideStock: stockOverride ? 1 : 0,
                    }),
                );

                setPosCartGroup({
                    posCartGroupId: posCartGroup!.posCartGroupId,
                    posCarts,
                });
            } else {
                const newPosCartGroup = await addToPosCartGroup(
                    objectToFormData({
                        firstCartProduct: posCartGroup?.posCartGroupId ? null : 1,
                        posCartGroupId: posCartGroup?.posCartGroupId || null,
                        productVariationId: product.id,
                        warehouseId: selectedWarehouseId,
                        qty: 1,
                        overrideStock: stockOverride ? 1 : 0,
                    }),
                );

                setPosCartGroup({
                    posCartGroupId:
                        posCartGroup?.posCartGroupId ||
                        newPosCartGroup.posCartGroupId,
                    posCarts: newPosCartGroup.posCarts,
                });
            }
        } catch (error) {
            const message =
                error instanceof Error
                    ? error.message
                    : translate('Failed to add product');
            toast.error(message);
        }
    };

    const handleQuantityChange = (
        posCartId: number,
        action: 'increase' | 'decrease' | 'delete',
    ) => {
        updatePosCartItem(
            objectToFormData({
                id: posCartId,
                action,
                overrideStock: stockOverride ? 1 : 0,
            }),
        )
            .then((posCarts) => setPosCartGroup({ ...posCartGroup!, posCarts }))
            .catch((error) => {
                const message =
                    error instanceof Error
                        ? error.message
                        : translate('Failed to update cart');
                toast.error(message);
            });
    };

    const handleSubmitOrderSuccess = (invoiceData?: any) => {
        console.log('handleSubmitOrderSuccess called with:', invoiceData);
        setPosCartGroup(undefined);
        setCustomer(undefined);
        setAddress(null);
        setDiscount('');
        setShipping('');
        setTax('');

        if (invoiceData) {
            console.log('Setting invoice data and showing modal');
            setCurrentInvoiceData(invoiceData);
            setShowInvoiceModal(true);
        } else {
            console.log('No invoice data received');
        }
    };

    const handleHoldOrder = () => {
        if (!posCartGroup) return;

        console.log(posCartGroup);

        try {
            holdOrder(posCartGroup?.posCartGroupId);
            setPosCartGroup(undefined);
        } catch (error: any) {
            toast.error(error.response.data.message);
        }
    };

    const handleResetOrder = () => {
        setPosCartGroup(undefined);
    };

    const handleCustomerFiltersUpdate = useCallback((customers: ICustomer[]) => {
        setCustomerFilters(customers);
    }, []);

    const handleCustomerSuccess = useCallback((newCustomer: ICustomer, newAddress?: IAddress) => {
        setCustomer(newCustomer);
        setAddress(newAddress || null);
        if (newCustomer.id !== 0 && !customerFilters?.find(c => c.id === newCustomer.id)) {
            setCustomerFilters([newCustomer, ...(customerFilters || [])]);
        }
    }, [customerFilters]);

    const handleFullScreenView = async () => {
        try {
            if (document.fullscreenElement) {
                await document.exitFullscreen();
                return;
            }

            const app = document.getElementById('app');
            if (app?.requestFullscreen) {
                await app.requestFullscreen();
            }
        } catch (error) {
            toast.error(translate('Unable to toggle fullscreen'));
        }
    };

    const totalPrice =
        (posCartGroup?.posCarts.reduce(
            (acc, posCart) =>
                acc + posCart.variation.discountedBasePrice * posCart.qty,
            0,
        ) || 0) -
        Number(discount) +
        Number(shipping) +
        Number(tax);

    useEffect(() => {
        if (!selectedWarehouseId || !scanQuery.trim()) {
            setScanFeedback(null);
            return;
        }

        const timerId = setTimeout(async () => {
            try {
                const result = await getPosProductsVariations({
                    ...filters,
                    page: 1,
                    warehouseId: selectedWarehouseId,
                    searchKey: scanQuery.trim(),
                });

                const matchedProduct = findMatchedProduct(
                    result.data,
                    scanQuery,
                );

                if (!matchedProduct) {
                    setScanFeedback({
                        product: null,
                        message: translate('No matching barcode or QR result'),
                        tone: 'error',
                    });
                    return;
                }

                const remainingStock = getRemainingStockForVariation(
                    matchedProduct,
                    selectedWarehouseId,
                    posCartGroup?.posCarts || [],
                    matchedProduct.id,
                );

                const stockTone =
                    remainingStock <= 0
                        ? 'out'
                        : remainingStock <= 5
                          ? 'low'
                          : 'ok';

                setScanFeedback({
                    product: matchedProduct,
                    message:
                        remainingStock > 0
                            ? `${translate('In stock')}: ${remainingStock}`
                            : stockOverride
                              ? translate('Out of stock, override available')
                              : translate('Out of stock'),
                    tone: stockTone,
                });
            } catch {
                setScanFeedback({
                    product: null,
                    message: translate('Unable to look up stock right now'),
                    tone: 'error',
                });
            }
        }, 250);

        return () => clearTimeout(timerId);
    }, [
        filters,
        posCartGroup?.posCarts,
        scanQuery,
        selectedWarehouseId,
        stockOverride,
    ]);

    const getCartLineLeftStock = (
        posCart: IPosCartGroup['posCarts'][number],
    ) => {
        return getStockForWarehouse(posCart.variation, posCart.warehouseId) - posCart.qty;
    };

    const getProductGroupStock = (productGroup: ProductGroup) => {
        return productGroup.variations.reduce(
            (total, variation) =>
                total +
                Math.max(
                    getRemainingStockForVariation(
                        variation,
                        selectedWarehouseId,
                        posCartGroup?.posCarts || [],
                        variation.id,
                    ),
                    0,
                ),
            0,
        );
    };

    const hasSelectableVariations = (productGroup: ProductGroup) => {
        return (
            productGroup.variations.length > 1 ||
            productGroup.variations.some(
                (variation) => variation.combinations.length > 0,
            )
        );
    };

    const canSelectProductGroup = (productGroup: ProductGroup) => {
        return stockOverride || getProductGroupStock(productGroup) > 0;
    };

    const handleProductGroupSelect = (productGroup: ProductGroup) => {
        if (!canSelectProductGroup(productGroup)) {
            toast.error(translate('Stock not available for this item'));
            return;
        }

        if (hasSelectableVariations(productGroup)) {
            setSelectedProductGroup(productGroup);
            return;
        }

        void addProductToCart(productGroup.variations[0]);
    };

    const getScanFeedbackClassName = () => {
        switch (scanFeedback?.tone) {
            case 'ok':
                return 'border-emerald-200 bg-emerald-50 text-emerald-700';
            case 'low':
                return 'border-amber-200 bg-amber-50 text-amber-800';
            case 'out':
                return 'border-red-200 bg-red-50 text-red-700';
            default:
                return 'border-border bg-background-primary-light text-muted';
        }
    };

    const handleScanSubmit = async (
        event?: KeyboardEvent<HTMLInputElement>,
    ) => {
        if (event && event.key !== 'Enter') {
            return;
        }

        event?.preventDefault();

        if (!scanFeedback?.product) {
            toast.error(translate('No matching product found'));
            return;
        }

        await addProductToCart(scanFeedback.product);
    };

    return (
        <>
            <InvoiceModal
                isActive={showInvoiceModal}
                onClose={() => setShowInvoiceModal(false)}
                invoiceData={currentInvoiceData}
            />
            <ModalWrapper
                isActive={!!selectedProductGroup}
                size="lg"
                onClose={() => setSelectedProductGroup(null)}
                className="max-sm:w-[calc(100%-16px)]"
            >
                <div className="theme-modal__header">
                    <h5 className="text-muted">
                        {selectedProductGroup?.productName}
                    </h5>
                    <button
                        type="button"
                        className="text-xl text-white sm:text-muted"
                        onClick={() => setSelectedProductGroup(null)}
                    >
                        <LiaTimesSolid />
                    </button>
                </div>

                <div className="theme-modal__body">
                    <div className="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                        {selectedProductGroup?.variations.map((variation) => {
                            const remainingStock = getRemainingStockForVariation(
                                variation,
                                selectedWarehouseId,
                                posCartGroup?.posCarts || [],
                                variation.id,
                            );
                            const stockBadge = getStockBadgeMeta(
                                Math.max(remainingStock, 0),
                            );
                            const canAdd = stockOverride || remainingStock > 0;
                            const handleVariantSelect = async () => {
                                if (!canAdd) {
                                    toast.error(
                                        translate(
                                            'Stock not available for this item',
                                        ),
                                    );
                                    return;
                                }

                                await addProductToCart(variation);
                                setSelectedProductGroup(null);
                            };

                            return (
                                <div
                                    key={variation.id}
                                    className={`rounded-md border border-border p-4 transition-colors ${
                                        canAdd
                                            ? 'cursor-pointer hover:border-primary/40'
                                            : 'cursor-not-allowed opacity-80'
                                    }`}
                                    onClick={() => void handleVariantSelect()}
                                    onKeyDown={(event) => {
                                        if (
                                            event.key === 'Enter' ||
                                            event.key === ' '
                                        ) {
                                            event.preventDefault();
                                            void handleVariantSelect();
                                        }
                                    }}
                                    role="button"
                                    tabIndex={0}
                                    aria-disabled={!canAdd}
                                >
                                    <div className="flex gap-3">
                                        <Image
                                            src={
                                                variation.image ||
                                                variation.product.thumbnailImg
                                            }
                                            alt={variation.product.name}
                                            width={80}
                                            height={80}
                                            className="h-20 w-20 rounded-md object-cover"
                                        />
                                        <div className="min-w-0 flex-1">
                                            <p className="text-sm text-muted">
                                                SKU:{' '}
                                                {variation.sku || variation.code}
                                            </p>
                                            <p className="text-sm text-muted">
                                                Code: {variation.code}
                                            </p>
                                            <p className="mt-1 font-bold text-primary">
                                                {currencyFormatter(
                                                    variation.discountedBasePrice,
                                                )}
                                            </p>
                                            <span
                                                className={`mt-2 inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ${stockBadge.className}`}
                                            >
                                                {stockOverride &&
                                                remainingStock <= 0
                                                    ? translate('Override')
                                                    : stockBadge.label}
                                            </span>
                                        </div>
                                    </div>

                                    <div className="mt-4 flex flex-wrap gap-2 border-t border-border pt-3">
                                        {variation.combinations.map(
                                            (combination) => (
                                                <span
                                                    key={combination.id}
                                                    className="rounded bg-background-hover px-2 py-1 text-xs text-foreground"
                                                >
                                                    {combination.name}
                                                </span>
                                            ),
                                        )}
                                    </div>

                                    <Button
                                        type="button"
                                        variant="primary"
                                        className="mt-4 w-full"
                                        disabled={!canAdd}
                                        onClick={(event) => {
                                            event.stopPropagation();
                                            void handleVariantSelect();
                                        }}
                                    >
                                        {canAdd
                                            ? translate('Add to Cart')
                                            : translate('Out of Stock')}
                                    </Button>
                                </div>
                            );
                        })}
                    </div>
                </div>
            </ModalWrapper>

            <div className={isFullscreen ? 'px-4 pb-24 lg:pb-0' : 'pb-24 lg:pb-0'}>
                {/* Desktop Header */}
                <div className="hidden lg:flex dashboard-nav pt-6 mb-8 flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div className="flex items-center">
                        <span className="text-xl mr-3 text-secondary">
                            <i className="fa-light fa-chart-mixed"></i>
                        </span>
                        <span className="text-sm sm:text-base font-bold">
                            {translate('POS (Point Of Sales)')}
                        </span>
                    </div>

                    <div className="hidden lg:flex w-full sm:w-auto sm:flex-wrap items-center justify-end gap-2">
                        <Button
                            type="button"
                            variant="primary"
                            className="!h-10 min-w-0 shrink-0 whitespace-nowrap !px-4 text-sm inline-flex items-center justify-center gap-2"
                            onClick={handleProductViewModeToggle}
                            title={
                                productViewMode === 'product-first'
                                    ? translate('Classic View')
                                    : translate('Product-First View')
                            }
                            aria-label={
                                productViewMode === 'product-first'
                                    ? translate('Classic View')
                                    : translate('Product-First View')
                            }
                        >
                            {productViewMode === 'product-first' ? (
                                <FaListUl className="shrink-0" />
                            ) : (
                                <FaThLarge className="shrink-0" />
                            )}
                            <span>
                                {productViewMode === 'product-first'
                                    ? translate('Classic View')
                                    : translate('Product-First View')}
                            </span>
                        </Button>

                        {canOverrideStock ? (
                            <label className="flex h-10 min-w-0 shrink-0 items-center justify-center gap-2 rounded-md border border-amber-200 bg-amber-50 px-3 text-sm text-amber-900 dark:border-amber-500/40 dark:bg-amber-500/10 dark:text-amber-200">
                                <input
                                    type="checkbox"
                                    checked={stockOverride}
                                    onChange={(event) =>
                                        setStockOverride(event.target.checked)
                                    }
                                />
                                <span>{translate('Manager override stock')}</span>
                            </label>
                        ) : null}

                        <div className="[&>button]:!h-10 [&>button]:!px-3 [&>button]:text-sm">
                            <OrderHistoryModal />
                        </div>
                        <div className="[&>button]:!h-10 [&>button]:w-10">
                            <HoldOrdersModal
                                onSelectOrder={(order) => {
                                    setPosCartGroup(order);
                                }}
                            />
                        </div>
                        <button
                            className="flex h-10 w-10 items-center justify-center rounded bg-primary text-2xl text-white hover:bg-primary/80"
                            onClick={handleFullScreenView}
                            type="button"
                            title={
                                isFullscreen
                                    ? translate('Exit fullscreen')
                                    : translate('Enter fullscreen')
                            }
                            aria-label={
                                isFullscreen
                                    ? translate('Exit fullscreen')
                                    : translate('Enter fullscreen')
                            }
                        >
                            {isFullscreen ? <BiExitFullscreen /> : <BiExpand />}
                        </button>
                    </div>
                </div>

                <div className="hidden lg:grid lg:grid-cols-2 xl:grid-cols-5 gap-5">
                    <div className="card xl:col-span-3">
                        <div className="card__content">
                            <div className="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-[minmax(0,1fr)_100px_108px_minmax(0,1fr)] xl:gap-5">
                                <SelectInput
                                    name="warehouseId"
                                    placeholder={translate('Warehouse')}
                                    value={filters.warehouseId}
                                    options={[
                                        {
                                            id: '',
                                            name: translate('Select Warehouse'),
                                        },
                                        ...filterData.warehouses,
                                    ]}
                                    getOptionLabel={(warehouse) => warehouse.name}
                                    getOptionValue={(warehouse) => warehouse.id}
                                    onChange={(newValue) => {
                                        handleFilter('warehouseId', newValue.id);
                                    }}
                                    groupClassName="w-full"
                                />

                                <SelectInput
                                    name="brandId"
                                    placeholder={translate('Brand')}
                                    value={filters.brandId}
                                    options={[
                                        { id: '', name: 'All brands' },
                                        ...filterData.brands,
                                    ]}
                                    getOptionLabel={(brand) => brand.name}
                                    getOptionValue={(brand) => brand.id}
                                    onChange={(newValue) => {
                                        handleFilter('brandId', newValue.id);
                                    }}
                                    groupClassName="w-full"
                                />

                                <SelectInput
                                    name="categoryId"
                                    placeholder={translate('Category')}
                                    value={filters.categoryId}
                                    options={[
                                        { id: '', name: 'All categories' },
                                        ...filterData.categories,
                                    ]}
                                    getOptionLabel={(category) => category.name}
                                    getOptionValue={(category) => category.id}
                                    onChange={(newValue) => {
                                        handleFilter('categoryId', newValue.id);
                                    }}
                                    groupClassName="w-full"
                                />

                                <SearchForm
                                    placeholder={translate('Search Products...')}
                                    value={searchDraft}
                                    onChange={handleSearch}
                                    classNames={{
                                        form: 'w-full',
                                        input: 'w-full',
                                    }}
                                />
                            </div>

                            <div className="mt-4 rounded-md border border-border bg-background-primary-light/50 p-3 sm:p-4">
                                <label className="mb-2 block text-xs font-semibold uppercase tracking-wide text-muted">
                                    {translate('SKU / Barcode Input')}
                                </label>
                                <div className="grid grid-cols-1 gap-2 sm:grid-cols-[minmax(0,1fr)_auto]">
                                    <input
                                        type="text"
                                        value={scanQuery}
                                        onChange={(event) =>
                                            setScanQuery(event.target.value)
                                        }
                                        onKeyDown={handleScanSubmit}
                                        placeholder={translate(
                                            'Type SKU / code and press Enter',
                                        )}
                                        className="h-10 w-full min-w-0 rounded-md border border-border bg-background px-3 focus:border-secondary-light focus:outline-none"
                                    />
                                    <Button
                                        variant="primary"
                                        className="!h-10 !px-3"
                                        onClick={() => void handleScanSubmit()}
                                        type="button"
                                    >
                                        {translate('Add')}
                                    </Button>
                                </div>

                                {scanFeedback ? (
                                    <div
                                        className={`mt-3 rounded-md border px-3 py-2 text-sm ${getScanFeedbackClassName()}`}
                                    >
                                        <div className="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                            <div>
                                                <div className="font-semibold">
                                                    {scanFeedback.product?.product.name ||
                                                        translate('No product found')}
                                                </div>
                                                <div>{scanFeedback.message}</div>
                                            </div>
                                            {scanFeedback.product ? (
                                                <span className="rounded-full px-2.5 py-1 text-xs font-semibold bg-white/70">
                                                    {scanFeedback.product.sku ||
                                                        scanFeedback.product.code}
                                                </span>
                                            ) : null}
                                        </div>
                                    </div>
                                ) : null}
                            </div>

                            <div className="grid grid-cols-2 sm:grid-cols-3 2xl:grid-cols-4 4xl:grid-cols-5 gap-3.5 mt-6 items-stretch">
                                {isLoading.loadingProduct ? (
                                    <div className="col-span-full flex justify-center pt-10">
                                        <Spinner className="w-[50px] h-[50px]" />
                                    </div>
                                ) : !filters.warehouseId ? (
                                    <div className="col-span-full">
                                        Please select a warehouse
                                    </div>
                                ) : !posProducts.length ? (
                                    <div className="col-span-full">
                                        No products Found
                                    </div>
                                ) : productViewMode === 'classic' ? (
                                    posProducts.map((posProduct) => (
                                        <ProductCard
                                            posCartGroup={posCartGroup}
                                            posProduct={posProduct}
                                            warehouseId={selectedWarehouseId}
                                            stockOverride={stockOverride}
                                            onSuccess={setPosCartGroup}
                                            key={posProduct.id}
                                        />
                                    ))
                                ) : (
                                    productGroups.map((productGroup) => {
                                        const stockQty =
                                            getProductGroupStock(productGroup);
                                        const stockBadge = getStockBadgeMeta(
                                            Math.max(stockQty, 0),
                                        );
                                        const canSelect =
                                            canSelectProductGroup(productGroup);

                                        return (
                                            <div
                                                key={productGroup.productId}
                                                className={`flex h-full flex-col overflow-hidden rounded-md border border-border bg-background transition-colors ${
                                                    canSelect
                                                        ? 'cursor-pointer hover:border-primary/40'
                                                        : 'cursor-not-allowed opacity-80'
                                                }`}
                                                onClick={() =>
                                                    handleProductGroupSelect(
                                                        productGroup,
                                                    )
                                                }
                                                onKeyDown={(event) => {
                                                    if (
                                                        event.key === 'Enter' ||
                                                        event.key === ' '
                                                    ) {
                                                        event.preventDefault();
                                                        handleProductGroupSelect(
                                                            productGroup,
                                                        );
                                                    }
                                                }}
                                                role="button"
                                                tabIndex={0}
                                                aria-disabled={!canSelect}
                                            >
                                                <div className="relative">
                                                    <Image
                                                        className="aspect-[4/3] bg-background-primary-light"
                                                        src={productGroup.image}
                                                        alt={
                                                            productGroup.productName
                                                        }
                                                    />
                                                    <span
                                                        className={`absolute right-2 top-2 rounded-full px-2.5 py-1 text-xs font-semibold ${stockBadge.className}`}
                                                    >
                                                        {stockOverride &&
                                                        stockQty <= 0
                                                            ? translate(
                                                                  'Override',
                                                              )
                                                            : stockBadge.label}
                                                    </span>
                                                    {hasSelectableVariations(
                                                        productGroup,
                                                    ) ? (
                                                        <span className="absolute bottom-2 left-2 rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700">
                                                            {translate(
                                                                'Has Variations',
                                                            )}
                                                        </span>
                                                    ) : null}
                                                </div>

                                                <div className="flex grow flex-col p-4">
                                                    <h3 className="line-clamp-2 min-h-[40px] text-sm font-medium">
                                                        {productGroup.productName}
                                                    </h3>
                                                    <div className="mt-2 flex items-center justify-between text-xs text-muted">
                                                        <span>
                                                            ID:{' '}
                                                            {
                                                                productGroup.productId
                                                            }
                                                        </span>
                                                        <span>
                                                            {
                                                                productGroup
                                                                    .variations
                                                                    .length
                                                            }{' '}
                                                            {translate(
                                                                'variant',
                                                            )}
                                                            {productGroup
                                                                .variations
                                                                .length > 1
                                                                ? 's'
                                                                : ''}
                                                        </span>
                                                    </div>
                                                    <Button
                                                        type="button"
                                                        variant="primary"
                                                        className="mt-4 w-full"
                                                        disabled={!canSelect}
                                                        onClick={(event) => {
                                                            event.stopPropagation();
                                                            handleProductGroupSelect(
                                                                productGroup,
                                                            );
                                                        }}
                                                    >
                                                        {canSelect
                                                            ? translate('Select')
                                                            : translate(
                                                                  'Out of Stock',
                                                              )}
                                                    </Button>
                                                </div>
                                            </div>
                                        );
                                    })
                                )}
                            </div>

                            {!isLoading.loadingProduct && filters.warehouseId ? (
                                <Pagination
                                    className="mt-8"
                                    pagination={paginationData}
                                    onPageChange={({ selected }) => {
                                        searchParams.set('page', String(selected));
                                        setSearchParams(searchParams);
                                    }}
                                />
                            ) : null}
                        </div>
                    </div>

                    <div className="hidden lg:block card xl:col-span-2">
                        <div className="card__content flex justify-between font-normal border-none pb-6">
                            <p className="text-muted">
                                {translate('Total Product')}
                                <span className="text-foreground ml-3">
                                    {paddedNumber(
                                        posCartGroup?.posCarts.length || 0,
                                    )}
                                </span>
                            </p>

                            <div className="flex justify-end gap-3">
                                <button
                                    type="button"
                                    onClick={() => {
                                        setIsAddingNewCustomer(false);
                                        setCustomerModalViewMode('manage');
                                        setIsCustomerModalOpen(true);
                                    }}
                                    className="flex items-center gap-2 rounded-md border border-border bg-background-primary-light px-3 py-2 text-sm hover:border-primary/40"
                                >
                                    <FiSettings />
                                    {customer ? (
                                        <span className="max-w-[150px] truncate">
                                            {customer.name}
                                        </span>
                                    ) : (
                                        <span>
                                            {translate('Walk In Customer')}
                                        </span>
                                    )}
                                </button>
                            </div>
                        </div>

                        <table className="theme-table">
                            <thead className="theme-table__head">
                                <tr>
                                    <th>{translate('Product')}</th>
                                    <th className="min-w-[120px]">
                                        {translate('Qty')}
                                    </th>
                                    <th className="min-w-[80px]">
                                        {translate('Price')}
                                    </th>
                                    <th className="min-w-[80px]">
                                        {translate('Sub Total')}
                                    </th>
                                    <th className="min-w-[50px]"></th>
                                </tr>
                            </thead>

                            <tbody className="theme-table__body">
                                {!posCartGroup?.posCarts.length ? (
                                    <tr>
                                        <td colSpan={5}>
                                            <p className="text-center">
                                                No product selected yet
                                            </p>
                                        </td>
                                    </tr>
                                ) : (
                                    posCartGroup?.posCarts.map((posCart) => (
                                        <tr
                                            className="theme-table__row"
                                            key={posCart.id}
                                        >
                                            <td>
                                                <span className="max-w-[300px] whitespace-normal break-words">
                                                    {posCart.product.name}
                                                </span>
                                            </td>
                                            <td>
                                                <Counter
                                                    value={posCart.qty}
                                                    max={
                                                        stockOverride
                                                            ? undefined
                                                            : getStockForWarehouse(
                                                                  posCart.variation,
                                                                  posCart.warehouseId,
                                                              )
                                                    }
                                                    orientation="horizontal"
                                                    onChange={(newValue) => {
                                                        if (newValue === posCart.qty) {
                                                            return;
                                                        }

                                                        handleQuantityChange(
                                                            posCart.id,
                                                            newValue > posCart.qty
                                                                ? 'increase'
                                                                : 'decrease',
                                                        );
                                                    }}
                                                />
                                                <p className="mt-2 text-[11px] text-muted">
                                                    Left: {getCartLineLeftStock(posCart)}
                                                </p>
                                            </td>
                                            <td>
                                                {currencyFormatter(
                                                    posCart.variation
                                                        .discountedBasePrice,
                                                )}
                                            </td>
                                            <td>
                                                {currencyFormatter(
                                                    posCart.variation
                                                        .discountedBasePrice *
                                                        posCart.qty,
                                                )}
                                            </td>
                                            <td>
                                                <button
                                                    className="text-theme-alert h-8 w-8 flex items-center justify-center"
                                                    onClick={() =>
                                                        handleQuantityChange(
                                                            posCart.id,
                                                            'delete',
                                                        )
                                                    }
                                                >
                                                    <FaTrashAlt />
                                                </button>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>

                        <div className="card__content">
                            <div className="grid grid-cols-3 gap-3.5">
                                <div>
                                    <label className="mb-2">
                                        {translate('Discount')}
                                    </label>
                                    <Input
                                        name="discount"
                                        placeholder={currencyFormatter(0)}
                                        value={discount}
                                        onChange={(e) =>
                                            setDiscount(
                                                Number(e.target.value) || '',
                                            )
                                        }
                                    />
                                </div>
                                <div>
                                    <label className="mb-2">
                                        {translate('Shipping')}
                                    </label>
                                    <Input
                                        name="shipping"
                                        placeholder={currencyFormatter(0)}
                                        value={shipping}
                                        onChange={(e) =>
                                            setShipping(
                                                Number(e.target.value) || '',
                                            )
                                        }
                                    />
                                </div>
                                <div>
                                    <label className="mb-2">
                                        {translate('Tax')}
                                    </label>
                                    <Input
                                        name="tax"
                                        placeholder={currencyFormatter(0)}
                                        value={tax}
                                        onChange={(e) =>
                                            setTax(Number(e.target.value) || '')
                                        }
                                    />
                                </div>
                            </div>

                            <div className="text-center py-5 text-primary bg-secondary-light/40 dark:text-foreground rounded-md text-xl font-bold mt-5">
                                TOTAL PAYABLE: {currencyFormatter(totalPrice)}
                            </div>
                        </div>

                        <div className="card__footer grid grid-cols-3 gap-3.5 border-t border-border pt-8">
                            <Button
                                variant="warning"
                                disabled={!posCartGroup}
                                onClick={handleHoldOrder}
                            >
                                <span className="text-2xl">
                                    <IoHandRightOutline />
                                </span>
                                {translate('Hold Order')}
                            </Button>
                            <Button variant="danger" onClick={handleResetOrder}>
                                {translate('Rest All')}
                            </Button>
                            <SubmitOrderModal
                                discount={discount || 0}
                                shippingCharge={shipping || 0}
                                stockOverride={stockOverride}
                                posCartGroup={posCartGroup}
                                customer={customer}
                                shippingAddress={address || undefined}
                                onSuccess={handleSubmitOrderSuccess}
                                isMobile={isMobile}
                            />
                        </div>
                    </div>
                </div>
            </div>

            {/* MOBILE LAYOUT */}
            <div className="lg:hidden fixed left-0 right-0 bottom-0 top-[75px] 2xl:top-[103px] z-30 flex flex-col bg-background-primary-light">
                {/* Mobile Header */}
                <div className="border-b border-border bg-background px-3 py-2">
                    <div className="flex items-center justify-between">
                        <div className="flex items-center gap-2">
                            <span className="text-lg text-secondary">
                                <i className="fa-light fa-chart-mixed"></i>
                            </span>
                            <span className="text-sm font-bold">
                                {translate('POS')}
                            </span>
                        </div>
                        <div className="flex items-center gap-2">
                            <OrderHistoryModal />
                            <HoldOrdersModal
                                onSelectOrder={(order) => {
                                    setPosCartGroup(order);
                                }}
                            />
                            {canOverrideStock && (
                                <label className="flex items-center gap-1 rounded-md border border-amber-200 bg-amber-50 px-2 py-1.5 text-xs text-amber-900">
                                    <input
                                        type="checkbox"
                                        checked={stockOverride}
                                        onChange={(event) =>
                                            setStockOverride(event.target.checked)
                                        }
                                    />
                                    <span>{translate('Override')}</span>
                                </label>
                            )}
                        </div>
                    </div>
                </div>

                {/* Customer Row */}
                <div className="border-b border-border bg-background px-3 py-2">
                    <div className="flex items-start gap-2">
                        <span className="text-lg text-muted shrink-0 mt-2">
                            <i className="fa-light fa-user"></i>
                        </span>
                        <div className="flex-1 min-w-0">
                            <SelectInput
                                name="customer-select"
                                placeholder={translate('Add Customer(Phone Number)...')}
                                value={customer?.id ?? 0}
                                options={[
                                    { id: 0, name: translate('Walk In Customer'), label: translate('Walk In Customer') },
                                    ...(customerFilters || []).map(c => ({
                                        ...c,
                                        label: c.phone ? `${c.name} (${c.phone})` : c.name,
                                    })),
                                ]}
                                getOptionLabel={(opt) => opt.label || opt.name}
                                getOptionValue={(opt) => String(opt.id)}
                                onChange={async (selected) => {
                                    if (selected?.id === 0) {
                                        handleCustomerSuccess({ id: 0, name: 'Walk In Customer' } as ICustomer);
                                    } else if (selected) {
                                        try {
                                            const addresses = await getAddresses(selected.id);
                                            if (addresses.length === 1) {
                                                handleCustomerSuccess(selected, addresses[0]);
                                            } else if (addresses.length > 1) {
                                                setModalCustomer(selected);
                                                setModalAddresses(addresses);
                                                setCustomerModalViewMode('address-picker');
                                                setIsCustomerModalOpen(true);
                                            } else {
                                                handleCustomerSuccess(selected);
                                            }
                                        } catch {
                                            handleCustomerSuccess(selected);
                                        }
                                    }
                                }}
                                classNames={{
                                    control: (state) =>
                                        `!min-h-[40px] !bg-background-primary-light !border-border ${state.isFocused ? '!border-primary/40' : ''}`,
                                    input: () => '!text-sm',
                                    placeholder: () => '!text-muted !text-sm',
                                    singleValue: () => '!text-sm !font-medium',
                                }}
                            />
                            {address && (
                                <p className="text-xs text-muted mt-1 truncate">
                                    {address.fullAddress || `${address.city?.name || address.state?.name}`}
                                </p>
                            )}
                        </div>
                        {address && (
                            <button
                                type="button"
                                onClick={() => {
                                    setIsAddingNewCustomer(false);
                                    setCustomerModalViewMode('manage');
                                    setIsCustomerModalOpen(true);
                                }}
                                className="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-muted hover:text-primary mt-1"
                                title={translate('Manage Customer')}
                            >
                                <FiEdit2 className="text-sm" />
                            </button>
                        )}
                        <button
                            type="button"
                            onClick={() => {
                                setIsAddingNewCustomer(true);
                                setCustomerModalViewMode('manage');
                                setIsCustomerModalOpen(true);
                            }}
                            className="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary text-white hover:bg-primary/90"
                            title={translate('Add New Customer')}
                        >
                            <AiOutlinePlus className="text-lg" />
                        </button>
                    </div>
                </div>

                {/* Product Filter Row */}
                <div className="border-b border-border bg-background px-3 py-2">
                    <div className="flex items-center justify-between">
                        <span className="text-sm font-semibold text-foreground">
                            {translate('Product Filter')}
                        </span>
                        <button
                            type="button"
                            onClick={() => setIsFilterDialogOpen(true)}
                            className="flex h-8 w-8 items-center justify-center rounded-lg text-muted hover:text-primary"
                            title={translate('Filters')}
                        >
                            <FiSettings className="text-lg" />
                        </button>
                    </div>
                </div>

                {/* Search Products Row */}
                <div className="border-b border-border bg-background px-3 py-2">
                    <SearchForm
                        placeholder={translate('Search Products')}
                        value={searchDraft}
                        onChange={handleSearch}
                        classNames={{
                            form: 'w-full',
                            input: 'w-full !h-10 !text-sm',
                        }}
                    />
                </div>

                {/* SKU / Barcode Input */}
                <div className="border-b border-border bg-background px-3 py-1.5">
                    <label className="mb-1 block text-[10px] font-semibold uppercase tracking-wide text-muted">
                        {translate('SKU / Barcode Input')}
                    </label>
                    <div className="flex items-center gap-2">
                        <input
                            type="text"
                            value={scanQuery}
                            onChange={(event) =>
                                setScanQuery(event.target.value)
                            }
                            onKeyDown={handleScanSubmit}
                            placeholder={translate(
                                'Type SKU / code and press Enter',
                            )}
                            className="h-9 flex-1 min-w-0 rounded-md border border-border bg-background-primary-light px-3 text-sm focus:border-secondary-light focus:outline-none"
                        />
                        <Button
                            variant="primary"
                            className="!h-9 shrink-0 !px-3 text-sm"
                            onClick={() => void handleScanSubmit()}
                            type="button"
                        >
                            {translate('Add')}
                        </Button>
                    </div>
                    {scanFeedback ? (
                        <div
                            className={`mt-1.5 rounded-md border px-2.5 py-1.5 text-xs ${getScanFeedbackClassName()}`}
                        >
                            <div className="flex items-center justify-between gap-2">
                                <div>
                                    <span className="font-semibold">
                                        {scanFeedback.product?.product.name ||
                                            translate('No product found')}
                                    </span>
                                    <span className="ml-1">
                                        {scanFeedback.message}
                                    </span>
                                </div>
                                {scanFeedback.product ? (
                                    <span className="shrink-0 rounded-full px-2 py-0.5 text-[10px] font-semibold bg-white/70">
                                        {scanFeedback.product.sku ||
                                            scanFeedback.product.code}
                                    </span>
                                ) : null}
                            </div>
                        </div>
                    ) : null}
                </div>

                {/* Product List */}
                <div className="flex-1 overflow-y-auto px-3 pb-28 pt-3">
                    <div className="grid grid-cols-2 sm:grid-cols-3 gap-3.5">
                        {isLoading.loadingProduct ? (
                            <div className="col-span-full flex justify-center pt-10">
                                <Spinner className="w-[50px] h-[50px]" />
                            </div>
                        ) : !filters.warehouseId ? (
                            <div className="col-span-full text-center text-muted">
                                {translate('Please select a warehouse')}
                            </div>
                        ) : !posProducts.length ? (
                            <div className="col-span-full text-center text-muted">
                                {translate('No products Found')}
                            </div>
                        ) : productViewMode === 'classic' ? (
                            posProducts.map((posProduct) => (
                                <ProductCard
                                    posCartGroup={posCartGroup}
                                    posProduct={posProduct}
                                    warehouseId={selectedWarehouseId}
                                    stockOverride={stockOverride}
                                    onSuccess={setPosCartGroup}
                                    key={posProduct.id}
                                />
                            ))
                        ) : (
                            productGroups.map((productGroup) => {
                                const stockQty =
                                    getProductGroupStock(productGroup);
                                const stockBadge = getStockBadgeMeta(
                                    Math.max(stockQty, 0),
                                );
                                const canSelect =
                                    canSelectProductGroup(productGroup);

                                return (
                                    <div
                                        key={productGroup.productId}
                                        className={`flex flex-col overflow-hidden rounded-md border border-border bg-background transition-colors ${
                                            canSelect
                                                ? 'cursor-pointer hover:border-primary/40'
                                                : 'cursor-not-allowed opacity-80'
                                        }`}
                                        onClick={() =>
                                            handleProductGroupSelect(
                                                productGroup,
                                            )
                                        }
                                        onKeyDown={(event) => {
                                            if (
                                                event.key === 'Enter' ||
                                                event.key === ' '
                                            ) {
                                                event.preventDefault();
                                                handleProductGroupSelect(
                                                    productGroup,
                                                );
                                            }
                                        }}
                                        role="button"
                                        tabIndex={0}
                                        aria-disabled={!canSelect}
                                    >
                                        <div className="relative">
                                            <Image
                                                className="aspect-[4/3] bg-background-primary-light"
                                                src={productGroup.image}
                                                alt={
                                                    productGroup.productName
                                                }
                                            />
                                            <span
                                                className={`absolute right-2 top-2 rounded-full px-2 py-1 text-xs font-semibold ${stockBadge.className}`}
                                            >
                                                {stockOverride &&
                                                stockQty <= 0
                                                    ? translate('Override')
                                                    : stockBadge.label}
                                            </span>
                                            {hasSelectableVariations(
                                                productGroup,
                                            ) ? (
                                                <span className="absolute bottom-2 left-2 rounded-full bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-700">
                                                    {translate('Has Variations')}
                                                </span>
                                            ) : null}
                                        </div>

                                        <div className="flex grow flex-col p-3">
                                            <h3 className="line-clamp-2 text-xs font-medium">
                                                {productGroup.productName}
                                            </h3>
                                            <Button
                                                type="button"
                                                variant="primary"
                                                className="mt-2 w-full !h-8 !px-2 !text-xs"
                                                disabled={!canSelect}
                                                onClick={(event) => {
                                                    event.stopPropagation();
                                                    handleProductGroupSelect(
                                                        productGroup,
                                                    );
                                                }}
                                            >
                                                {canSelect
                                                    ? translate('Select')
                                                    : translate('Out of Stock')}
                                            </Button>
                                        </div>
                                    </div>
                                );
                            })
                        )}
                    </div>

                    {!isLoading.loadingProduct && filters.warehouseId && (
                        <Pagination
                            className="mt-6"
                            pagination={paginationData}
                            onPageChange={({ selected }) => {
                                searchParams.set('page', String(selected));
                                setSearchParams(searchParams);
                            }}
                        />
                    )}
                </div>

                {/* Fixed Bottom Bar */}
                <div className="fixed bottom-0 left-0 right-0 z-40 border-t border-border bg-background px-3 py-2 shadow-lg">
                    <button
                        type="button"
                        onClick={() => setIsCartDrawerOpen(true)}
                        className="flex w-full items-center justify-between rounded-lg bg-primary px-4 py-3 text-white hover:bg-primary/90"
                    >
                        <div className="flex items-center gap-2">
                            <span className="flex h-8 w-8 items-center justify-center rounded-full bg-white/20 text-sm font-bold">
                                {posCartGroup?.posCarts.length || 0}
                            </span>
                            <span className="text-sm font-medium">
                                {translate('items')}
                            </span>
                        </div>
                        <span className="text-lg font-bold">
                            {currencyFormatter(totalPrice)}
                        </span>
                    </button>
                </div>

                {/* Cart Bottom Sheet (Vaul Drawer) */}
                <Drawer.Root open={isCartDrawerOpen} onOpenChange={setIsCartDrawerOpen}>
                    <Drawer.Portal>
                        <Drawer.Overlay className="fixed inset-0 z-40 bg-black/40" />
                        <Drawer.Content className="fixed bottom-0 left-0 right-0 z-50 flex max-h-[85vh] flex-col rounded-t-xl bg-white dark:bg-background-secondary outline-none">
                            <div className="mx-auto mt-3 h-1 w-12 shrink-0 rounded-full bg-gray-300 dark:bg-gray-600" />

                            {/* Cart Header */}
                            <div className="flex items-center justify-between border-b border-border px-4 py-3">
                                <h3 className="text-base font-semibold">
                                    {translate('Cart')}
                                </h3>
                                <button
                                    type="button"
                                    onClick={() => setIsCartDrawerOpen(false)}
                                    className="text-xl text-muted hover:text-foreground"
                                >
                                    <LiaTimesSolid />
                                </button>
                            </div>

                            {/* Cart Items */}
                            <div className="flex-1 overflow-y-auto px-4 py-3">
                                {!posCartGroup?.posCarts.length ? (
                                    <div className="rounded-md border border-dashed border-border px-4 py-8 text-center text-muted">
                                        {translate('No product selected yet')}
                                    </div>
                                ) : (
                                    <div className="space-y-3">
                                        {posCartGroup.posCarts.map((posCart) => (
                                            <div
                                                key={posCart.id}
                                                className="rounded-md border border-border p-3"
                                            >
                                                <div className="flex items-start justify-between gap-3">
                                                    <div className="min-w-0 flex-1">
                                                        <div className="font-medium">
                                                            {posCart.product.name}
                                                        </div>
                                                        <div className="mt-1 text-xs text-muted">
                                                            {currencyFormatter(
                                                                posCart.variation.discountedBasePrice,
                                                            )}{' '}
                                                            ·{' '}
                                                            <span className="font-bold text-foreground">
                                                                {translate('Left')}: {getCartLineLeftStock(posCart)}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <button
                                                        className="text-theme-alert h-8 w-8 flex shrink-0 items-center justify-center"
                                                        onClick={() =>
                                                            handleQuantityChange(
                                                                posCart.id,
                                                                'delete',
                                                            )
                                                        }
                                                    >
                                                        <FaTrashAlt />
                                                    </button>
                                                </div>
                                                <div className="mt-3 flex items-center justify-between gap-3">
                                                    <Counter
                                                        value={posCart.qty}
                                                        max={
                                                            stockOverride
                                                                ? undefined
                                                                : getStockForWarehouse(
                                                                      posCart.variation,
                                                                      posCart.warehouseId,
                                                                  )
                                                        }
                                                        orientation="horizontal"
                                                        onChange={(newValue) => {
                                                            if (newValue === posCart.qty) {
                                                                return;
                                                            }

                                                            handleQuantityChange(
                                                                posCart.id,
                                                                newValue > posCart.qty
                                                                    ? 'increase'
                                                                    : 'decrease',
                                                            );
                                                        }}
                                                    />
                                                    <div className="text-right">
                                                        <div className="text-xs text-muted">
                                                            {translate('Sub Total')}
                                                        </div>
                                                        <div className="font-semibold">
                                                            {currencyFormatter(
                                                                posCart.variation.discountedBasePrice *
                                                                    posCart.qty,
                                                            )}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                )}

                                {/* Discount/Shipping/Tax */}
                                {posCartGroup?.posCarts.length > 0 && (
                                    <div className="mt-4 space-y-3">
                                        <div className="grid grid-cols-3 gap-3">
                                            <div>
                                                <label className="mb-1 block text-xs font-medium">
                                                    {translate('Discount')}
                                                </label>
                                                <Input
                                                    name="discount-mobile"
                                                    placeholder={currencyFormatter(0)}
                                                    value={discount}
                                                    onChange={(e) =>
                                                        setDiscount(Number(e.target.value) || '')
                                                    }
                                                />
                                            </div>
                                            <div>
                                                <label className="mb-1 block text-xs font-medium">
                                                    {translate('Shipping')}
                                                </label>
                                                <Input
                                                    name="shipping-mobile"
                                                    placeholder={currencyFormatter(0)}
                                                    value={shipping}
                                                    onChange={(e) =>
                                                        setShipping(Number(e.target.value) || '')
                                                    }
                                                />
                                            </div>
                                            <div>
                                                <label className="mb-1 block text-xs font-medium">
                                                    {translate('Tax')}
                                                </label>
                                                <Input
                                                    name="tax-mobile"
                                                    placeholder={currencyFormatter(0)}
                                                    value={tax}
                                                    onChange={(e) =>
                                                        setTax(Number(e.target.value) || '')
                                                    }
                                                />
                                            </div>
                                        </div>

                                        <div className="rounded-md bg-secondary-light/40 px-4 py-3 text-center text-lg font-bold text-primary">
                                            {translate('TOTAL PAYABLE')}: {currencyFormatter(totalPrice)}
                                        </div>
                                    </div>
                                )}
                            </div>

                            {/* Cart Actions */}
                            {posCartGroup?.posCarts.length > 0 && (
                                <div className="border-t border-border px-4 py-3">
                                    <div className="grid grid-cols-3 gap-3">
                                        <Button
                                            variant="warning"
                                            disabled={!posCartGroup}
                                            onClick={() => {
                                                handleHoldOrder();
                                                setIsCartDrawerOpen(false);
                                            }}
                                        >
                                            <span className="text-xl">
                                                <IoHandRightOutline />
                                            </span>
                                            <span className="hidden sm:inline">
                                                {translate('Hold')}
                                            </span>
                                        </Button>
                                        <Button
                                            variant="danger"
                                            onClick={() => {
                                                handleResetOrder();
                                                setIsCartDrawerOpen(false);
                                            }}
                                        >
                                            {translate('Reset')}
                                        </Button>
                                        <SubmitOrderModal
                                            discount={discount || 0}
                                            shippingCharge={shipping || 0}
                                            stockOverride={stockOverride}
                                            posCartGroup={posCartGroup}
                                            customer={customer}
                                            shippingAddress={address || undefined}
                                            onSuccess={(invoiceData?: any) => {
                                                handleSubmitOrderSuccess(invoiceData);
                                                setIsCartDrawerOpen(false);
                                            }}
                                            isMobile={isMobile}
                                        />
                                    </div>
                                </div>
                            )}
                        </Drawer.Content>
                    </Drawer.Portal>
                </Drawer.Root>

                {/* Filter Dialog */}
                <ModalWrapper
                    isActive={isFilterDialogOpen}
                    onClose={() => setIsFilterDialogOpen(false)}
                    size="md"
                    className="max-sm:w-[calc(100%-16px)]"
                >
                    <div className="theme-modal__header">
                        <h5>{translate('Product Filters')}</h5>
                        <button
                            type="button"
                            className="text-xl text-white sm:text-muted"
                            onClick={() => setIsFilterDialogOpen(false)}
                        >
                            <LiaTimesSolid />
                        </button>
                    </div>

                    <div className="theme-modal__body space-y-4">
                        <div>
                            <label className="mb-2 block text-sm font-medium">
                                {translate('Warehouse')}
                                <span className="ml-1 text-red-500">*</span>
                            </label>
                            <SelectInput
                                name="filter-warehouse"
                                placeholder={translate('Select Warehouse')}
                                value={filters.warehouseId}
                                options={[
                                    { id: '', name: translate('Select Warehouse') },
                                    ...filterData.warehouses,
                                ]}
                                getOptionLabel={(warehouse) => warehouse.name}
                                getOptionValue={(warehouse) => warehouse.id}
                                onChange={(newValue) => {
                                    handleFilter('warehouseId', newValue.id);
                                }}
                            />
                        </div>

                        <div>
                            <label className="mb-2 block text-sm font-medium">
                                {translate('Brand')}
                            </label>
                            <SelectInput
                                name="filter-brand"
                                placeholder={translate('All brands')}
                                value={filters.brandId}
                                options={[
                                    { id: '', name: translate('All brands') },
                                    ...filterData.brands,
                                ]}
                                getOptionLabel={(brand) => brand.name}
                                getOptionValue={(brand) => brand.id}
                                onChange={(newValue) => {
                                    handleFilter('brandId', newValue.id);
                                }}
                            />
                        </div>

                        <div>
                            <label className="mb-2 block text-sm font-medium">
                                {translate('Category')}
                            </label>
                            <SelectInput
                                name="filter-category"
                                placeholder={translate('All categories')}
                                value={filters.categoryId}
                                options={[
                                    { id: '', name: translate('All categories') },
                                    ...filterData.categories,
                                ]}
                                getOptionLabel={(category) => category.name}
                                getOptionValue={(category) => category.id}
                                onChange={(newValue) => {
                                    handleFilter('categoryId', newValue.id);
                                }}
                            />
                        </div>

                        <div className="flex gap-2 pt-2">
                            <Button
                                type="button"
                                variant="light"
                                className="flex-1"
                                onClick={() => {
                                    handleFilter('brandId', '');
                                    handleFilter('categoryId', '');
                                }}
                            >
                                {translate('Clear')}
                            </Button>
                            <Button
                                type="button"
                                variant="primary"
                                className="flex-1"
                                onClick={() => setIsFilterDialogOpen(false)}
                            >
                                {translate('Apply')}
                            </Button>
                        </div>
                    </div>
                </ModalWrapper>
            </div>

            {/* Customer Modal (Desktop + Mobile) */}
            <CustomerModal
                isOpen={isCustomerModalOpen}
                onClose={() => {
                    setCustomerModalViewMode('select');
                    setModalAddresses([]);
                    setModalCustomer(null);
                    setIsAddingNewCustomer(false);
                    setIsCustomerModalOpen(false);
                }}
                initialCustomer={isAddingNewCustomer ? undefined : (customerModalViewMode === 'address-picker' ? (modalCustomer || customer) : customer)}
                initialAddress={address || undefined}
                initialAddresses={modalAddresses}
                onSuccess={handleCustomerSuccess}
                customerFilters={customerFilters || []}
                onCustomerFiltersUpdate={handleCustomerFiltersUpdate}
                isMobile={isMobile}
                defaultViewMode={customerModalViewMode}
            />

        </>
    );
};

export default App;
