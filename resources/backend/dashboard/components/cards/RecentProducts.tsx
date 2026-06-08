import React, { useEffect, useState } from 'react';
import {
    currencyFormatter,
    paddedNumber,
} from '../../../../frontend/utils/numberFormatter';
import Image from '../../../react/components/Image';
import { translate } from '../../../react/utils/translate';
import { IProduct } from '../../types/response';
import { getRecentProducts } from '../../utils';

const RecentProducts = () => {
    const [recentProducts, setRecentProducts] = useState<IProduct[]>([]);

    useEffect(() => {
        getRecentProducts().then((data) =>
            setRecentProducts(data.recentProducts),
        );
    }, []);

    return (
        <div className="xl:col-span-4 card">
            <h5 className="card__title border-none">
                {translate('Recent Products')}
                <span className="text-muted ml-2">
                    ({paddedNumber(recentProducts.length)})
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
                        {recentProducts.map((product) => (
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

                                        <div className="">
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
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    );
};

export default RecentProducts;
