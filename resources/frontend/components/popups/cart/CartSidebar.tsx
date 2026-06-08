import { LiaTimesSolid } from 'react-icons/lia';
import { PiShoppingCartSimpleLight } from 'react-icons/pi';
import { Link } from 'react-router-dom';
import { useAuth } from '../../../store/features/auth/authSlice';
import {
    closePopup,
    togglePopup,
    usePopup,
} from '../../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../../store/store';
import { groupBy } from '../../../utils/groupBy';
import { translate } from '../../../utils/translate';
import Button from '../../buttons/Button';
import ProductCardWide from '../../card/ProductCardWide';

const CartSidebar = () => {
    const { popup } = usePopup();
    const { user, carts } = useAuth();
    const dispatch = useAppDispatch();
    const isActive = popup === 'cart';

    const groupedByShop = groupBy(carts, ({ shop }) => shop.name);

    return (
        <aside
            className={`fixed top-0 right-0 bottom-0 w-[calc(100%-50px)] max-w-[450px] flex flex-col bg-white z-[5] overflow-y-auto transition-all duration-150 ease-in-out ${
                isActive ? 'translate-x-0 delay-150' : 'translate-x-full'
            }`}
            aria-hidden={!isActive}
        >
            <div className="grow">
                <div className="bg-primary text-white flex items-center justify-between h-12 px-4">
                    <h4 className="uppercase">{translate('Your Cart')}</h4>

                    <button
                        className="text-xl"
                        onClick={() => dispatch(closePopup())}
                    >
                        <LiaTimesSolid />
                    </button>
                </div>

                {!Object.entries(groupedByShop).length ? (
                    <div className="text-center pt-10">
                        <span className="inline-block text-center text-[70px] text-black/70 mb-3">
                            <PiShoppingCartSimpleLight />
                        </span>
                        <p className="black/70">
                            {translate('Your cart is empty')}
                        </p>
                    </div>
                ) : (
                    Object.entries(groupedByShop).map(([shopName, carts]) => (
                        <div key={shopName}>
                            <div className="flex items-center justify-between text-sm border-b border-primary-14 h-12 px-4 bg-stone-50">
                                <h5 className="text-black/70">
                                    {translate('seller')}
                                </h5>
                                <Link
                                    to={`/shops/${carts[0].shop.slug}`}
                                    className="text-primary"
                                >
                                    {shopName}
                                </Link>
                            </div>

                            {carts.map((item) => (
                                <div
                                    className="px-4 border-b border-primary-14"
                                    key={item.id}
                                >
                                    <ProductCardWide cartProduct={item} />
                                </div>
                            ))}
                        </div>
                    ))
                )}
            </div>

            <div className="flex px-2.5 pb-3 pt-10 gap-2">
                <Button as="link" to="/cart" size="lg" className="grow">
                    {translate('View Cart')}
                </Button>
                {/* {user ? ( */}
                    <Button
                        as="link"
                        to="/checkout"
                        size="lg"
                        className="grow"
                        variant="outline"
                    >
                        {translate('Checkout')}
                    </Button>
                {/* ) : (
                    <Button
                        as="button"
                        onClick={() => dispatch(togglePopup('signin'))}
                        className="grow"
                        size="lg"
                        variant="outline"
                    >
                        {translate('checkout')}
                    </Button>
                )} */}
            </div>
        </aside>
    );
};

export default CartSidebar;
