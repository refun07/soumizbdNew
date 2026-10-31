import { IProductShort } from '../../types/product';
import { cn } from '../../utils/cn';
import BotanicalProductCard from '../storefront/BotanicalProductCard';

interface Props {
    product: IProductShort;
    containerClassName?: string;
}

// Recommendation sections use the same product card as catalog grids.
const ProductCardSmall = ({ product, containerClassName }: Props) => (
    <div className={cn('min-w-0', containerClassName)}>
        <BotanicalProductCard product={product} />
    </div>
);

export default ProductCardSmall;
