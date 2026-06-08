import { FaSearch } from 'react-icons/fa';
import { useNavigate, useParams } from 'react-router-dom';
import IconButton from '../../components/buttons/IconButton';
import Input from '../../components/inputs/Input';
import OrderDetailsView from '../../components/order/OrderDetailsView';
import { translate } from '../../utils/translate';

const OrderTracking = () => {
    const params = useParams<{ orderCode: string }>();

    const navigate = useNavigate();

    const handleSearch = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        navigate(`/orders/track/${e.currentTarget.code.value}`);
    };

    return (
        <div className="container no-style">
            <form
                onSubmit={handleSearch}
                className="py-10 text-center bg-white border-b border-primary-14 flex justify-center"
            >
                <label htmlFor="value" className="sr-only">
                    {translate('Search Order by Code')}
                </label>
                <Input
                    type="text"
                    name="code"
                    placeholder={translate('Enter Order Code')}
                    className="max-w-[200px] text-center rounded-r-none"
                />
                <IconButton className="h-8 w-8 sm:h-10 sm:w-10 border-l-0 rounded-card rounded-l-none hover:border-secondary">
                    <FaSearch />
                </IconButton>
            </form>

            {params.orderCode ? (
                <OrderDetailsView reviewActive={false} />
            ) : null}
        </div>
    );
};

export default OrderTracking;
