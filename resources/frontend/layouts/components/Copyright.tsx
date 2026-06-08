import { useGetFooterQuery } from '../../store/features/api/homeApis/commonHomeApi';

const Copyright = () => {
    const { data: footerRes } = useGetFooterQuery();
    const isModernFooter = footerRes?.defaultFooter === 'footer-two';
    const fallbackText = `All Rights Reserved By ${window.config.generalSettings.appName}`;

    return (
        <div
            className={`text-[12px] ${
                isModernFooter ? 'py-6 text-center' : 'py-[28px]'
            }`}
            style={{
                backgroundColor:
                    footerRes?.copyright_bg_color ||
                    (isModernFooter ? '#8b5747' : '#b3052a'),
                color: footerRes?.copyright_text_color || '#ffffff',
            }}
        >
            <div className="container">
                {footerRes?.homeFooterCopyrightText || fallbackText}
            </div>
        </div>
    );
};

export default Copyright;
