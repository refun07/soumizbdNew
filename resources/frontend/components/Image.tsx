import { useRef } from 'react';

interface Props extends React.ImgHTMLAttributes<HTMLImageElement> {}

const errorImgPath = '/images/image-error.png';

const Image = ({ ...rest }: Props) => {
    const imgRef = useRef<HTMLImageElement>(null);

    const onError = () => {
        // Prevent infinite loop
        // If the image is already the error image, don't set it again
        if (imgRef.current && imgRef.current.src.indexOf(errorImgPath) === -1) {
            imgRef.current.src = errorImgPath;
        }
    };

    return <img {...rest} onError={onError} loading="lazy" ref={imgRef} />;
};

export default Image;
