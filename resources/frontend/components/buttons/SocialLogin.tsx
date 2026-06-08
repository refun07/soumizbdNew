import { SiFacebook, SiGoogle } from 'react-icons/si';
import { translate } from '../../utils/translate';
import Button from './Button';

const socialMediaColors = {
    Facebook: {
        background: '#307BEE',
        color: '#fff',
        icon: <SiFacebook />,
    },
    Google: {
        background: '#DF4930',
        color: '#fff',
        icon: <SiGoogle />,
    },
};

const SocialLogin = () => {
    return (
        <div className="flex flex-wrap gap-2.5 capitalize">
            {window.config.socialLogins.map((item) => (
                <Button
                    as="link"
                    reloadDocument
                    to={item.url}
                    variant="no-color"
                    className="grow whitespace-nowrap"
                    style={{
                        backgroundColor:
                            socialMediaColors[item.name].background,
                        color: socialMediaColors[item.name].color,
                    }}
                    key={item.name}
                >
                    <span className="text-lg">
                        {socialMediaColors[item.name].icon}
                    </span>
                    <span>{translate(item.name)}</span>
                </Button>
            ))}
        </div>
    );
};

export default SocialLogin;
