import { Link } from 'react-router-dom';
import { IShop } from '../../types/shop';
import Image from '../Image';
import ThemeRating from '../reviews/ThemeRating';

interface Props {
    shop: IShop;
}

const ShopCard = ({ shop }: Props) => {
    return (
        <div className="group">
            <Link to={`/shops/${shop.slug}`} className="w-full">
                <Image
                    src={shop.logo}
                    alt={shop.name}
                    className="bg-white border w-full group-hover:border-secondary rounded-card aspect-square"
                />
            </Link>
            <div className="flex flex-col items-center mt-3">
                <h4 className="mb-1">
                    <Link to={`/shops/${shop.slug}`}>{shop.name}</Link>
                </h4>
                <ThemeRating
                    rating={shop.rating.average}
                    totalReviews={shop.rating.total}
                />
            </div>
        </div>
    );
};

export default ShopCard;
