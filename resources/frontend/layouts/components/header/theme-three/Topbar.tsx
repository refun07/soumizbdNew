import { FaAngleDown } from 'react-icons/fa';
import { Link } from 'react-router-dom';
import { translate } from '../../../../utils/translate';
import AvatarDropdownMenu from '../AvatarDropdownMenu';

interface TopbarConfig {
    enabled: boolean;
    bg_color: string;
    text_color: string;
}

const Topbar = ({ config }: { config?: TopbarConfig }) => {
    return (
        <div
            className="max-md:hidden"
            style={{
                backgroundColor: config?.bg_color || '#000000',
                color: config?.text_color || 'rgba(255, 255, 255, 0.7)'
            }}
        >
            <div className="container flex items-center justify-between h-9">
                {/* Left spacer for alignment */}

                <div className="flex items-center justify-center gap-5">
                    <Link to={'/orders/track'} className="">
                        {translate('Track Order')}
                    </Link>

                    <div
                        className="option-dropdown flex items-center"
                        tabIndex={0}
                    >
                        <div className="option-dropdown__toggler no-style flex gap-1 items-center">
                            {translate('My Account')}

                            <span className="text-white">
                                <FaAngleDown />
                            </span>
                        </div>

                        <div className="option-dropdown__options shadow-md w-[170px] top-[calc(100%+20px)] left-auto right-0 rounded-none p-0">
                            <AvatarDropdownMenu />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default Topbar;
