import React, { useEffect, useState } from 'react';
import {
    currencyFormatter,
    paddedNumber,
} from '../../../../frontend/utils/numberFormatter';
import Image from '../../../react/components/Image';
import { translate } from '../../../react/utils/translate';
import { IProduct } from '../../types/response';
import { getTopSellingProducts } from '../../utils';

const TopSellingProducts = () => {
    const [products, setProducts] = useState<IProduct[]>();

    useEffect(() => {
        getTopSellingProducts().then((data) => {
            setProducts(data.mostSellingProducts);
        });
    }, []);

    return (
        <div className="xl:col-span-4 bg-background rounded-md pt-4 lg:pt-6 xl:pt-8">
            <h5 className="text-sm sm:text-base font-medium px-4 lg:px-6 xl:px-9 mb-5">
                {translate('Top Selling Products')}
                <span className="text-muted ml-2">
                    ({paddedNumber(products?.length)})
                </span>
            </h5>

            <div className="max-h-[450px] overflow-y-auto">
                <table className="w-full min-w-[400px]">
                    <thead className="theme-table__head">
                        <tr>
                            <th>{translate('Product Details')}</th>
                            <th className="min-w-[80px]">
                                {translate('Unit')}
                            </th>
                            <th className="min-w-[80px]">
                                {translate('Price')}
                            </th>
                        </tr>
                    </thead>

                    <tbody className="theme-table__body">
                        {!products?.length ? (
                            <tr>
                                <td colSpan={3} className="text-center">
                                    {translate('No products found')}
                                </td>
                            </tr>
                        ) : (
                            products?.map((product) => (
                                <tr key={product.slug}>
                                    <td className="inline-flex items-center gap-2.5">
                                        <div className="inline-flex items-center gap-4">
                                            <div className="p-1 border border-border rounded-md">
                                                <Image
                                                    src={product.thumbnailImg}
                                                    alt=""
                                                    className="w-[60px] aspect-square rounded-md"
                                                />
                                            </div>

                                            <div className="text-foreground">
                                                <span className="max-w-[230px] line-clamp-2">
                                                    {product.name}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td className="text-muted">
                                        {paddedNumber(product.totalSaleCount)}
                                    </td>
                                    <td className="text-muted">
                                        {currencyFormatter(product.basePrice)}
                                    </td>
                                </tr>
                            ))
                        )}
                    </tbody>
                </table>
            </div>
        </div>
    );
};

export default TopSellingProducts;
