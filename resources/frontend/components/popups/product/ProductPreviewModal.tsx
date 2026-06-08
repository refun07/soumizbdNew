import { LiaTimesSolid } from 'react-icons/lia';
import { closePopup, usePopup } from '../../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../../store/store';
import ModalWrapper from '../ModalWrapper';
import ProductPreviewContent from './ProductPreviewContent';

const ProductPreviewModal = () => {
    const dispatch = useAppDispatch();
    const { popup, popupProps } = usePopup();
    const isActive = popup === 'product-preview';

    return (
        <ModalWrapper isActive={isActive}>
            <button
                className="absolute right-5 top-5 text-xl text-primary z-[2]"
                onClick={() => dispatch(closePopup())}
            >
                <LiaTimesSolid />
            </button>
            <div className="theme-modal__body min-h-[595px] w-full bg-white p-0">
                {isActive && popupProps.slug && (
                    <ProductPreviewContent
                        slug={popupProps.slug}
                        stickyButtons={false}
                        className="p-4 sm:p-7 md:p-10"
                    />
                )}
            </div>
        </ModalWrapper>
    );
};

export default ProductPreviewModal;
