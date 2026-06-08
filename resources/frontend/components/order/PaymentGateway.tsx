import { useEffect, useRef } from 'react';
import { useLocation } from 'react-router-dom';

export default function PaymentGateway() {
    const location = useLocation(); 

    const paymentForm = useRef<HTMLFormElement>(null);

    useEffect(() => {
        setTimeout(() => {
            paymentForm.current?.submit();
        }, 100);
    }, []);

    return (
        <div>
            <form
                action={window.config.generalSettings.rootUrl + '/payments'}
                method="POST"
                ref={paymentForm}
            >
                {/* orderGroupCode */}
                <input type="hidden" name="code" value={location.state} />
            </form>
        </div>
    );
}
