import React, { useEffect, useMemo, useState } from 'react';
import toast from 'react-hot-toast';
import { debounce } from '../../frontend/utils/debounce';
import SelectInput from '../react/components/inputs/SelectInput';
import Pagination from '../react/components/pagination/Pagination';
import { IPaginatedResponse } from '../react/types';
import { translate } from '../react/utils/translate';
import CampaignProduct from './components/CampaignProduct';
import Checkbox from './components/inputs/Checkbox';
import ProductSelect from './components/ProductSelect';
import SearchForm from './components/SearchForm';
import {
    IBrands,
    ICampaignProduct,
    ICategory,
    IProductFilter,
    IProductShort,
} from './types';
import {
    addProductToCampaign,
    getCampaignProducts,
    getFilterData,
    getProducts,
} from './utils';

const initialCampaignData: IPaginatedResponse<any> = {
    data: [],
    meta: {
        current_page: 1,
        from: 1,
        last_page: 1,
        per_page: 20,
        to: 1,
        total: 1,
    },
};
const debouncedSearch = debounce((fn: Function) => fn(), 500);

declare global {
    interface Window {
        urlType: string;
    }
}

const App = () => {
    const campaignMatches = window.location.pathname.match(
        /\/(admin|seller)\/campaigns\/(\d+)/,
    );
    const campaignId = campaignMatches ? campaignMatches[2] : null;

    const [loading, setLoading] = useState({
        filteredProducts: false,
        selectedProducts: false,
        filterData: true,
        addingProducts: false,
    });

    // category and brand data for filtering
    const [filterData, setFilterData] = useState<{
        brands: IBrands[];
        categories: ICategory[];
    }>({
        brands: [],
        categories: [],
    });

    const [filters, setFilters] = useState<IProductFilter>({
        page: 1,
        limit: 50,

        searchKey: '',

        brandId: null,
        categoryId: null,
    });

    const [campaignProductFilters, setCampaignProductFilters] =
        useState<IProductFilter>({
            page: 1,
            limit: 50,

            searchKey: '',

            brandId: null,
            categoryId: null,
        });

    // selected products and pagination data
    const [campaignProducts, setCampaignProducts] =
        useState<IPaginatedResponse<ICampaignProduct[]>>(initialCampaignData);

    // filtered products and pagination data
    const [filteredProducts, setFilteredProducts] =
        useState<IPaginatedResponse<IProductShort[]>>(initialCampaignData);

    // selected products to be added to campaign
    // example: { productId: variationId[] }
    const [selectedProducts, setSelectedProducts] = useState<
        Record<string, number[]>
    >({});

    const disabledVariationIdsByProduct = useMemo(() => {
        const campaignVariationIds = new Set(
            campaignProducts.data.map((item) => item.variation.id),
        );

        return filteredProducts.data.reduce<Record<number, number[]>>(
            (acc, product) => {
                const productData = product as any;
                const isProductMarkedAsCampaignItem = Boolean(
                    productData.isCampaignProduct ||
                        productData.isAddedToCampaign ||
                        productData.inCampaign ||
                        productData.campaignId ||
                        productData.addedCampaignId ||
                        productData.campaignProductId,
                );

                const disabledIds = product.variations
                    .filter((variation) => {
                        const variationData = variation as any;
                        const isVariationMarkedAsCampaignItem = Boolean(
                            variationData.isCampaignProduct ||
                                variationData.isAddedToCampaign ||
                                variationData.inCampaign ||
                                variationData.campaignId ||
                                variationData.addedCampaignId ||
                                variationData.campaignProductId,
                        );

                        return (
                            isProductMarkedAsCampaignItem ||
                            isVariationMarkedAsCampaignItem ||
                            campaignVariationIds.has(variation.id)
                        );
                    })
                    .map((variation) => variation.id);

                acc[product.id] = disabledIds;
                return acc;
            },
            {},
        );
    }, [filteredProducts.data, campaignProducts.data]);

    const disabledProductIds = useMemo(() => {
        return filteredProducts.data.reduce<Record<number, boolean>>(
            (acc, product) => {
                acc[product.id] =
                    (disabledVariationIdsByProduct[product.id] || []).length >
                    0;
                return acc;
            },
            {},
        );
    }, [disabledVariationIdsByProduct, filteredProducts.data]);

    const blockedVariationIds = useMemo(() => {
        return new Set(
            Object.values(disabledVariationIdsByProduct).flat(),
        );
    }, [disabledVariationIdsByProduct]);

    useEffect(() => {
        setSelectedProducts((prev) => {
            let hasChanges = false;
            const nextSelectedProducts = { ...prev };

            Object.keys(nextSelectedProducts).forEach((productId) => {
                const variationIds = nextSelectedProducts[productId] || [];
                const filteredVariationIds = variationIds.filter(
                    (variationId) => !blockedVariationIds.has(variationId),
                );

                if (filteredVariationIds.length !== variationIds.length) {
                    nextSelectedProducts[productId] = filteredVariationIds;
                    hasChanges = true;
                }
            });

            return hasChanges ? nextSelectedProducts : prev;
        });
    }, [blockedVariationIds]);

    // update filter when url changes
    useEffect(() => {
        const urlParams = new URLSearchParams(window.location.search);

        const page = Number(urlParams.get('page')) || 1;
        const limit = Number(urlParams.get('limit')) || 50;

        const searchKey = urlParams.get('searchKey') || '';

        const brandId = Number(urlParams.get('brandId')) || null;
        const categoryId = Number(urlParams.get('categoryId')) || null;

        setFilters({
            ...filters,
            page,
            limit,
            searchKey,
            brandId,
            categoryId,
        });
    }, [window.location.search]);

    useEffect(() => {
        getFilterData()
            .then((filterData) => {
                setFilterData(filterData);
            })
            .catch((error) => {
                toast.error(error.response.data.message);
            })
            .finally(() => {
                setLoading((prev) => ({ ...prev, filterData: false }));
            });
    }, []);

    // fetch products when filters change
    useEffect(() => {
        setLoading((prev) => ({ ...prev, filteredProducts: true }));

        // wrap getProducts in debouncedSearch to prevent
        // multiple api calls when user types
        // debouncedSearch(() => {
        getProducts({
            ...filters,
            campaignId: Number(campaignId),
        })
            .then((data) => {
                setFilteredProducts(data);
            })
            .finally(() => {
                setLoading((prev) => ({
                    ...prev,
                    filteredProducts: false,
                }));
            });
        // });
    }, [filters]);

    // fetch products when campaignProductFilters change
    useEffect(() => {
        setLoading((prev) => ({ ...prev, selectedProducts: true }));

        // wrap getCampaignProducts in debouncedSearch to prevent
        // multiple api calls when user types
        debouncedSearch(() =>
            getCampaignProducts({
                ...campaignProductFilters,
                campaignId: Number(campaignId),
            })
                .then((data) => {
                    setCampaignProducts(data);
                })
                .finally(() => {
                    setLoading((prev) => ({
                        ...prev,
                        selectedProducts: false,
                    }));
                }),
        );
    }, [campaignProductFilters]);

    const handleSelectVariation = ({
        productId,
        variationIds,
    }: {
        productId: number;
        variationIds: number[];
    }) => {
        setSelectedProducts((prev) => {
            return {
                ...prev,
                [productId]: variationIds,
            };
        });
    };

    const handleAddToCampaign = async () => {
        try {
            const variationIds = Object.values(selectedProducts)
                .flat()
                .filter((id) => !blockedVariationIds.has(id));

            if (!variationIds.length) {
                toast.error(translate('Select at least one product'));
                return;
            }

            setLoading((prev) => ({ ...prev, addingProducts: true }));
            const newCampaignProducts = await addProductToCampaign(
                Number(campaignId),
                variationIds,
            );

            if (newCampaignProducts?.length) {
                setCampaignProducts((prev) => {
                    return {
                        ...prev,
                        data: [...newCampaignProducts, ...prev!.data],
                    };
                });
            }

            setSelectedProducts({});
            toast.success(translate('Products added to campaign'));
        } catch (error) {
            toast.error(
                error.response.data.message ||
                    translate('Something went wrong!'),
            );
        } finally {
            setLoading((prev) => ({ ...prev, addingProducts: false }));
        }
    };

    const handleRemoveFromCampaign = async (id: number) => {
        const updatedProducts = campaignProducts.data.filter(
            (p) => p.id !== id,
        );
        setCampaignProducts({
            ...campaignProducts,
            data: updatedProducts,
        });
    };

    const allVisibleProductsSelected =
        filteredProducts?.data?.some(
            (product) => !disabledProductIds[product.id],
        ) &&
        filteredProducts.data
            .filter((product) => !disabledProductIds[product.id])
            .every((product) => {
            const selectedVariationIds = selectedProducts[product.id] || [];
            return (
                selectedVariationIds.length > 0 &&
                selectedVariationIds.length === product.variations.length
            );
        });

    const handleSelectAllVisibleProducts = (
        e: React.ChangeEvent<HTMLInputElement>,
    ) => {
        const checked = e.target.checked;

        setSelectedProducts((prev) => {
            const nextSelectedProducts = { ...prev };

            filteredProducts?.data?.forEach((product) => {
                if (disabledProductIds[product.id]) {
                    nextSelectedProducts[product.id] = [];
                    return;
                }

                nextSelectedProducts[product.id] = checked
                    ? product.variations.map((variation) => variation.id)
                    : [];
            });

            return nextSelectedProducts;
        });
    };

    const compactSelectClassNames = {
        control: () => '!min-h-[34px]',
        singleValue: () => '!text-xs',
        placeholder: () => '!text-xs',
        input: () => '!text-xs',
    };

    return (
        <div className="card grid grid-cols-12 mt-10 !px-0">
            <div className="col-span-12 xl:col-span-7 pb-4 flex flex-col min-h-[700px] lg:max-h-[calc(100vh-150px)]">
                <h4 className="card__title">
                    <span className="px-3">{translate('Select Products')}</span>
                </h4>

                <div className="card__content flex gap-2.5 lg:!py-2 lg:!px-4">
                    <SelectInput
                        groupClassName="w-full min-w-[220px]"
                        classNames={compactSelectClassNames}
                        name="brandId"
                        placeholder={translate('Brand')}
                        value={filters.brandId}
                        options={[
                            { id: null, name: 'All brands' },
                            ...filterData.brands,
                        ]}
                        getOptionLabel={(brand) => brand.name}
                        getOptionValue={(brand) => brand.id}
                        onChange={(newValue) => {
                            setFilters({
                                ...filters,
                                brandId: newValue.id,
                            });
                        }}
                    />

                    <SelectInput
                        groupClassName="w-full min-w-[220px]"
                        classNames={compactSelectClassNames}
                        name="categoryId"
                        placeholder={translate('Category')}
                        value={filters.categoryId}
                        options={[
                            { id: null, name: 'All categories' },
                            ...filterData.categories,
                        ]}
                        getOptionLabel={(category) => category.name}
                        getOptionValue={(category) => category.id}
                        onChange={(newValue) => {
                            setFilters({
                                ...filters,
                                categoryId: newValue.id,
                            });
                        }}
                    />

                    <div className="w-full min-w-[240px]">
                        <SearchForm
                            className="!text-xs"
                            placeholder={translate('Search Products...')}
                            value={filters.searchKey}
                            onChange={(e) =>
                                setFilters((prev) => ({
                                    ...prev,
                                    searchKey: e.target.value,
                                }))
                            }
                        />
                    </div>
                </div>

                <div className="uppercase text-left bg-primary/5 flex items-center justify-between text-muted px-2 md:px-10 py-3">
                    <div className="font-bold flex items-center gap-3">
                        <span>{translate('Products')}</span>
                        <label className="normal-case text-xs inline-flex items-center gap-2">
                            <Checkbox
                                name="markAllProducts"
                                checked={allVisibleProductsSelected}
                                disabled={loading.filteredProducts}
                                onChange={handleSelectAllVisibleProducts}
                            />
                            <span>{translate('Mark all')}</span>
                        </label>
                    </div>

                    <div className="flex items-center gap-3">
                        <p className="md:hidden xl:block">
                            {translate('Selected')}:
                        </p>
                        <p className="md:hidden xl:block">
                            {Object.values(selectedProducts).flat().length}
                        </p>

                        <button
                            className="button button--primary"
                            onClick={handleAddToCampaign}
                            disabled={loading.addingProducts}
                        >
                            <span>
                                <i
                                    className={`${
                                        loading.addingProducts
                                            ? 'fas fa-spinner fa-spin'
                                            : 'fal fa-plus'
                                    }`}
                                ></i>
                            </span>
                            {loading.addingProducts
                                ? translate('Adding...')
                                : translate('Add Product')}
                        </button>
                    </div>
                </div>

                <div className="grow overflow-y-auto divide-y divide-border">
                    {loading.filteredProducts ? (
                        <p className="py-5 text-center">
                            {translate('Loading products...')}
                        </p>
                    ) : !filteredProducts?.data.length ? (
                        <p className="py-5 text-center">
                            {translate('No products found')}
                        </p>
                    ) : (
                        filteredProducts?.data.map((product) => (
                            <ProductSelect
                                product={product}
                                selectedVariations={
                                    selectedProducts[product.id] || []
                                }
                                disabled={disabledProductIds[product.id]}
                                onVariationSelect={handleSelectVariation}
                                key={product.id}
                            />
                        ))
                    )}
                </div>

                <Pagination
                    pagination={filteredProducts?.meta}
                    className="px-10"
                    onPageChange={({ selected }) =>
                        setFilters({ ...filters, page: selected })
                    }
                />
            </div>

            <div className="col-span-12 xl:col-span-5 pb-4 max-2xl:border-t 2xl:border-l border-border flex flex-col min-h-[700px] lg:max-h-[calc(100vh-150px)]">
                <h4 className="card__title">
                    <span className="lg:px-3">
                        {translate('Added Products')}{' '}
                        <span className="text-muted font-normal">
                            ({campaignProducts?.data.length || '0'})
                        </span>
                    </span>
                </h4>

                <div className="card__content !pb-2 !pt-2 !px-2 flex justify-between gap-2.5">
                    <div className="flex gap-2.5">
                        <SelectInput
                            groupClassName="w-full min-w-[180px]"
                            classNames={compactSelectClassNames}
                            name="brandId"
                            placeholder={translate('Brand')}
                            value={campaignProductFilters.brandId}
                            options={[
                                { id: null, name: 'All brands' },
                                ...filterData.brands,
                            ]}
                            getOptionLabel={(brand) => brand.name}
                            getOptionValue={(brand) => brand.id}
                            onChange={(newValue) => {
                                setCampaignProductFilters({
                                    ...campaignProductFilters,
                                    brandId: newValue.id,
                                });
                            }}
                        />

                        <SelectInput
                            groupClassName="w-full min-w-[180px]"
                            classNames={compactSelectClassNames}
                            name="categoryId"
                            placeholder={translate('Category')}
                            value={campaignProductFilters.categoryId}
                            options={[
                                { id: null, name: 'All categories' },
                                ...filterData.categories,
                            ]}
                            getOptionLabel={(category) => category.name}
                            getOptionValue={(category) => category.id}
                            onChange={(newValue) => {
                                setCampaignProductFilters({
                                    ...campaignProductFilters,
                                    categoryId: newValue.id,
                                });
                            }}
                        />
                    </div>

                    <div className="max-w-[280px] w-full">
                        <SearchForm
                            className="!text-xs"
                            placeholder={translate('Search Products...')}
                            value={campaignProductFilters.searchKey}
                            onChange={(e) =>
                                setCampaignProductFilters((prev) => ({
                                    ...prev,
                                    searchKey: e.target.value,
                                }))
                            }
                        />
                    </div>
                </div>

                <div className="overflow-y-auto grow">
                    <div>
                        <table className="w-full min-w-[600px]">
                            {/* footable */}
                            <thead className="theme-table__head text-muted">
                                <tr>
                                    <th className="">
                                        {translate('Product Details')}
                                    </th>
                                    <th>{translate('Price')} </th>
                                    <th>{translate('Discount')}</th>
                                    <th>{translate('Discount Type')}</th>
                                    <th className="w-[60px]"></th>
                                </tr>
                            </thead>
                            <tbody className="[&_td]:py-3 divide-y divide-border">
                                {loading.selectedProducts ? (
                                    <tr>
                                        <td className="" colSpan={5}>
                                            <p className="text-center py-10">
                                                {translate('Loading...')}
                                            </p>
                                        </td>
                                    </tr>
                                ) : !campaignProducts?.data.length ? (
                                    <tr>
                                        <td className="" colSpan={5}>
                                            <p className="text-center py-10">
                                                {translate('No Product Found')}
                                            </p>
                                        </td>
                                    </tr>
                                ) : (
                                    campaignProducts?.data.map((item) => (
                                        <CampaignProduct
                                            campaignProduct={item}
                                            onDelete={() =>
                                                handleRemoveFromCampaign(
                                                    item.id,
                                                )
                                            }
                                            key={item.id}
                                        />
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>

                <Pagination
                    pagination={campaignProducts?.meta}
                    className="px-10"
                    onPageChange={({ selected }) =>
                        setCampaignProductFilters({
                            ...campaignProductFilters,
                            page: selected,
                        })
                    }
                />
            </div>
        </div>
    );
};

export default App;
