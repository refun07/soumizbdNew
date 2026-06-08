import Button from '../components/buttons/Button';
import SvgNotFound from '../components/icons/NotFound';
import { translate } from '../utils/translate';

interface NotFoundProps {
    title?: string;
}

const NotFound = () => {
    return (
        <div className="container">
            <div className="flex flex-col items-center max-w-[450px] text-center mx-auto py-20">
                <div>
                    <SvgNotFound className="w-full max-w-[370px]" />
                </div>
                <h2 className="ek-h2">{translate('Oops! Page Not Found')}</h2>
                <p className="mt-2 mb-5">
                    {translate(
                        'Looks like you are looking for something that doesn’t exist! Let’s get you back on track!',
                    )}
                </p>

                <div>
                    <Button
                        as="link"
                        to={`/`}
                        size="lg"
                        className="w-full max-w-[370px]"
                    >
                        {translate('Back to Home')}
                    </Button>
                </div>
            </div>
        </div>
    );
};

export default NotFound;
