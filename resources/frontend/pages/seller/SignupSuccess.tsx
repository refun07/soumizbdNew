import Button from '../../components/buttons/Button';
import { translate } from '../../utils/translate';

const SellerSignupSuccess = () => {
    return (
        <div className="container mt-8 flex flex-col items-center justify-center text-center">
            <div className="mt-[60px]">
                <img src="/images/registration-complete.png" alt="" />
            </div>
            <h1 className="mt-6 mb-3 ek-h2">
                {translate('Your Registration is completed!')}
            </h1>
            <p>
                {translate(
                    'Congratulations! Now you can visit your seller dashboard to configure settings.',
                )}
            </p>
            <Button
                as="link"
                reloadDocument={true}
                to={'/login'}
                variant="primary"
                size="lg"
                className="px-8 mt-8 mb-[80px]"
            >
                {translate('Go To Dashboard')}
            </Button>
        </div>
    );
};

export default SellerSignupSuccess;
