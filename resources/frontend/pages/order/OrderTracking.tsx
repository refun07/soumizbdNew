import { FaSearch } from 'react-icons/fa';
import { useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import IconButton from '../../components/buttons/IconButton';
import Input from '../../components/inputs/Input';
import OrderDetailsView from '../../components/order/OrderDetailsView';
import { translate } from '../../utils/translate';

const OrderTracking = () => {
    const params = useParams<{ orderCode: string }>();

    const navigate = useNavigate();
    const [error, setError] = useState('');

    const handleSearch = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        const code = e.currentTarget.code.value.trim().replace(/^#/, '');
        if (!code || !/^\d+$/.test(code)) {
            setError('Enter the order number from your confirmation.');
            return;
        }
        setError('');
        navigate(`/orders/track/${encodeURIComponent(code)}`);
    };

    return (
        <div className="container no-style">
            <h1 className="pt-8 text-center text-2xl font-semibold">Track your order</h1>
            <form
                onSubmit={handleSearch}
                className="py-10 text-center bg-white border-b border-primary-14 flex justify-center"
            >
                <label htmlFor="tracking-order-code" className="sr-only">
                    {translate('Search Order by Code')}
                </label>
                <Input
                    type="text"
                    id="tracking-order-code"
                    inputMode="numeric"
                    aria-invalid={!!error}
                    aria-describedby={error ? 'tracking-error' : undefined}
                    name="code"
                    placeholder={translate('Enter Order Code')}
                    className="max-w-[200px] text-center rounded-r-none"
                />
                <IconButton aria-label="Track order" className="!h-11 !w-11 max-sm:!size-11 border-l-0 rounded-card rounded-l-none hover:border-secondary">
                    <FaSearch />
                </IconButton>
            </form>
            {error && <p id="tracking-error" role="alert" className="pb-4 text-center text-sm text-red-700">{error}</p>}

            {params.orderCode ? (
                <OrderDetailsView reviewActive={false} />
            ) : null}
        </div>
    );
};

export default OrderTracking;
