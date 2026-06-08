import React, { useEffect, useState } from 'react';
import { paddedNumber } from '../../../../frontend/utils/numberFormatter';
import Image from '../../../react/components/Image';
import { translate } from '../../../react/utils/translate';
import { IProduct } from '../../types/response';
import { getMostSellingProducts } from '../../utils';

const MostSellingProductsCard = () => {
    const [products, setProducts] = useState<IProduct[]>([]);

    useEffect(() => {
        getMostSellingProducts().then(setProducts);
    }, []);

    return (
        <div className="xl:col-span-4 card">
            <h5 className="card__title border-none">
                {translate('Most Selling Products')}
                <span className="text-muted ml-2">
                    ({paddedNumber(products.length)})
                </span>
            </h5>

            <div className="max-h-[450px] overflow-y-auto">
                <table className="w-full min-w-[320px]">
                    <thead className="theme-table__head">
                        <tr>
                            <th>{translate('Product Details')}</th>
                            <th className="min-w-[140px] text-end">
                                {translate('Sale Count')}
                            </th>
                        </tr>
                    </thead>

                    <tbody className="theme-table__body">
                        {products.map((product) => (
                            <tr key={product.slug}>
                                <td className="inline-flex items-center gap-2.5">
                                    <div className="inline-flex items-center gap-4">
                                        <div className="p-1 border border-muted rounded-md">
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
                                <td className="text-muted text-end">
                                    {paddedNumber(product.totalSaleCount)}
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    );
};

export default MostSellingProductsCard;
