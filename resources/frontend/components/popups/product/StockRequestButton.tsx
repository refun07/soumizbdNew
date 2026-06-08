import { useSelector } from 'react-redux';
import { useCheckStockRequestQuery } from '../../../store/features/api/stockRequestApi';
import { togglePopup } from '../../../store/features/popup/popupSlice';
import { translate } from '../../../utils/translate';
import { useAppDispatch } from '../../../store/store';

interface StockRequestButtonProps {
    product: {
        id: number;
        name: string;
        slug: string;
    };
    selectedVariation: {
        id: number;
        name: string;
    } | null | undefined;
}

const StockRequestButton = ({ product, selectedVariation }: StockRequestButtonProps) => {
    const dispatch = useAppDispatch();
    const { user, guestUserId } = useSelector((state: any) => state.auth);

    const { data: checkData, isLoading: checking } = useCheckStockRequestQuery({
        productId: product.id.toString(),
        variationId: selectedVariation?.id?.toString(),
        email: user?.email,
        guestUserId: guestUserId,
    });

    const handleRequestStock = () => {
        dispatch(
            togglePopup({
                popup: 'stock-request',
                popupProps: {
                    productId: product.id,
                    variationId: selectedVariation?.id,
                    product: {
                        id: product.id,
                        name: product.name,
                        slug: product.slug,
                    },
                    variation: selectedVariation
                        ? {
                              id: selectedVariation.id,
                              name: selectedVariation.name,
                          }
                        : undefined,
                },
            })
        );
    };

    if (checking || !product) {
        return null;
    }

    if (checkData?.requested) {
        return (
            <span className="badge bg-success text-white badge-lg">
                <i className="fa-solid fa-check mr-1"></i>
                {translate('Already Requested')}
            </span>
        );
    }

    return (
        <button
            type="button"
            className="btn btn-outline-primary btn-sm border border-dashed"
            onClick={handleRequestStock}
        >
            <i className="fa-regular fa-bell mr-1"></i>
            {translate('Request for Stock')}
        </button>
    );
};

export default StockRequestButton;
