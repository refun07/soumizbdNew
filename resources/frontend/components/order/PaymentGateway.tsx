import { useEffect, useRef } from 'react';
import { Link, useLocation } from 'react-router-dom';

export default function PaymentGateway() {
    const location = useLocation(); 
    const orderCode = typeof location.state === 'string' || typeof location.state === 'number'
        ? String(location.state) : '';
    const hasOrder = /^\d+$/.test(orderCode) && Number(orderCode) > 0;

    const paymentForm = useRef<HTMLFormElement>(null);

    useEffect(() => {
        if (!hasOrder) return;
        const timer = setTimeout(() => {
            paymentForm.current?.submit();
        }, 100);
        return () => clearTimeout(timer);
    }, [hasOrder, orderCode]);

    if (!hasOrder) {
        return <section className="container py-16 text-center">
            <h1 className="text-2xl font-semibold">No order selected for payment</h1>
            <p className="mt-3">Complete checkout to choose a payment method.</p>
            <Link className="mt-6 inline-flex min-h-11 items-center rounded-full bg-[#087c3e] px-6 text-white" to="/cart">Return to cart</Link>
        </section>;
    }

    return (
        <div className="container py-16 text-center">
            <p role="status">Connecting to payment…</p>
            <form
                action={window.config.generalSettings.rootUrl + '/payments'}
                method="POST"
                ref={paymentForm}
            >
                {/* orderGroupCode */}
                <input type="hidden" name="code" value={orderCode} />
                <button type="submit" className="mt-6 min-h-11 rounded-full bg-[#087c3e] px-6 text-white">Continue to payment</button>
            </form>
        </div>
    );
}
