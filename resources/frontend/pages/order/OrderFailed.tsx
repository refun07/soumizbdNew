import Button from '../../components/buttons/Button';
import SvgOrderSuccess from '../../components/icons/OrderSuccess';
import { translate } from '../../utils/translate';

const OrderFailed = () => {
    return (
        <div className="container">
            <div className="flex flex-col items-center text-center mx-auto py-10">
                <div className="w-full">
                    <SvgOrderSuccess className="w-full max-w-[470px] mx-auto" />
                </div>
                <h2 className="ek-h2">
                    {translate('Could not place your order')}
                </h2>
                <p className="mt-2">
                    {translate(
                        'We could not place your order. Please try again or contact support.',
                    )}
                </p>

                <div className="flex justify-center gap-3 mt-5">
                    <Button as="link" to={`/cart`} className="px-6" size="lg">
                        {translate('Go To Cart')}
                    </Button>
                </div>
            </div>
        </div>
    );
};

export default OrderFailed;
