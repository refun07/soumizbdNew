import React, { useRef } from 'react';

interface Props extends React.ImgHTMLAttributes<HTMLImageElement> {}

const errorImgPath = '/images/image-error.png';

const Image = ({ ...rest }: Props) => {
    const imgRef = useRef<HTMLImageElement>(null);

    const onError = () => {
        if (imgRef.current) {
            imgRef.current.src = errorImgPath;
        }
    };

    return <img {...rest} onError={onError} loading="lazy" ref={imgRef} />;
};

export default Image;
