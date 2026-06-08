import HeaderThemeOne from './theme-one';
import HeaderThemeThree from './theme-three';
import HeaderThemeTwo from './theme-two';

const Header = () => {
    if (window.config.defaultHeader === 'header-one') {
        return <HeaderThemeOne />;
    } else if (window.config.defaultHeader === 'header-two') {
        return <HeaderThemeTwo />;
    } else if (window.config.defaultHeader === 'header-three') {
        return <HeaderThemeThree />;
    }
};

export default Header;
