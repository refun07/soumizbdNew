import LightGallery from 'lightgallery/react';
import { ReactNode } from 'react';
// import plugins if you need
import lgThumbnail from 'lightgallery/plugins/thumbnail';
import lgZoom from 'lightgallery/plugins/zoom';
// import styles
import 'lightgallery/css/lg-thumbnail.css';
import 'lightgallery/css/lg-zoom.css';
import 'lightgallery/css/lightgallery.css';

interface Props {
    children: ReactNode | ReactNode[];
}

const ImagePopupGallery = ({ children }: Props) => {
    return (
        <>
            <LightGallery closable speed={500} plugins={[lgThumbnail, lgZoom]}>
                {children}
            </LightGallery>
        </>
    );
};

export default ImagePopupGallery;
