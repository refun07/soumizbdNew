import { Link } from 'react-router-dom';
import { IProductShort } from '../../types/product';
import { cn } from '../../utils/cn';
import { currencyFormatter } from '../../utils/numberFormatter';
import Image from '../Image';

interface Props {
    product: IProductShort;
    containerClassName?: string;
}

const ProductCardSmall = ({ product, containerClassName }: Props) => {
    return (
        <div
            className={cn(
                'group flex items-center gap-2 sm:gap-5 bg-white',
                containerClassName,
            )}
        >
            <div>
                <Link
                    to={`/products/${product.slug}`}
                    className="w-full overflow-hidden"
                >
                    <Image
                        src={product.thumbnailImg}
                        alt={product.name}
                        className={`max-w-[100px] rounded-card aspect-square group-hover:scale-110 transition-all duration-300`}
                    />
                </Link>
            </div>

            <div className="grow">
                <h3 className="font-medium mb-1">
                    <Link
                        to={`/products/${product.slug}`}
                        className="line-clamp-2"
                    >
                        {product.name}
                    </Link>
                </h3>
                {product.unit ? (
                    <p className="mb-1 text-xs font-bold text-black/60 line-clamp-1">
                        {product.unit}
                    </p>
                ) : null}

                <div className="flex flex-row gap-1.5 items-center" dir="ltr">
                    {product.basePrice !== product.discountedBasePrice ? (
                        <>
                            <span className="text-primary text-xl font-semibold">
                                {currencyFormatter(product.discountedBasePrice)}
                            </span>
                            <span className="text-black/70 text-lg font-medium line-through">
                                {currencyFormatter(product.basePrice)}
                            </span>
                        </>
                    ) : (
                        <span className="text-primary text-xl font-semibold">
                            {currencyFormatter(product.basePrice)}
                        </span>
                    )}
                </div>
            </div>
        </div>
    );
};

export default ProductCardSmall;
