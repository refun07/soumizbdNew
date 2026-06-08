import { useFormik } from 'formik';
import { useEffect } from 'react';
import toast from 'react-hot-toast';
import { LiaTimesSolid } from 'react-icons/lia';
import { PiSmiley, PiSmileyBlank, PiSmileySad } from 'react-icons/pi';
import { Link } from 'react-router-dom';
import { mixed, number, object, string } from 'yup';
import {
    useLazyGetUserReviewOfProductQuery,
    useLazyGetUserReviewOfShopQuery,
    usePostShopImpressionMutation,
    useUpsertProductReviewMutation,
} from '../../store/features/api/reviewApi';
import { closePopup, usePopup } from '../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../store/store';
import { Impressions } from '../../types';
import { IProductShort, IReview } from '../../types/product';
import { cn } from '../../utils/cn';
import { translate } from '../../utils/translate';
import Image from '../Image';
import Button from '../buttons/Button';
import InputGroup from '../inputs/InputGroup';
import Label from '../inputs/Label';
import Textarea from '../inputs/Textarea';
import FileDropbox from '../inputs/file/FileDropbox';
import ThemeRating from '../reviews/ThemeRating';
import ModalWrapper from './ModalWrapper';

export interface IReviewState {
    orderId: number;
    productId: number;
    rating: number;
    description: string;
    newImages: File[];
    oldImages: IReview['images'];
}

export const impressions: {
    name: string;
    icon: React.ReactNode;
    value: Impressions;
}[] = [
    { name: 'Positive', icon: <PiSmiley />, value: 'positive' },
    { name: 'Negative', icon: <PiSmileySad />, value: 'negative' },
    { name: 'Neutral', icon: <PiSmileyBlank />, value: 'neutral' },
];

const validationSchema = object().shape({
    orderId: number().min(1).required(translate('Order ID is missing')),
    productId: number().min(1).required(translate('Product ID is missing')),
    rating: number()
        .min(1, translate('Give a rating'))
        .required(translate('Please rate the product')),
    description: string().required(translate('Please write the review')),
    newImages: mixed().required(translate('Please upload the images')),
});

const initialValues: IReviewState = {
    orderId: 0,
    productId: 0,
    rating: 0,
    description: '',
    newImages: [],
    oldImages: [],
};

const ReviewModal = () => {
    const dispatch = useAppDispatch();
    const { popup, popupProps } = usePopup();
    const [upsertProductReview] = useUpsertProductReviewMutation();
    const [postShopImpression] = usePostShopImpressionMutation();

    const [getUserReviewOfProduct] = useLazyGetUserReviewOfProductQuery();

    const [getUserReviewOfShop, { data: shopImpressionResponse }] =
        useLazyGetUserReviewOfShopQuery();

    const product = popupProps?.product as IProductShort | undefined;
    const review = popupProps?.review as IReview | undefined;
    const orderId = popupProps?.orderId as number | undefined;
    const shopId = popupProps?.shopId as number | undefined;
    const isActive = popup === 'product-review' && Boolean(product?.id);

    const formik = useFormik({
        validateOnChange: false,
        initialValues,
        validationSchema,
        onSubmit: async (values, helpers) => {
            try {
                const payload = {
                    ...values,
                    oldImages: values.oldImages
                        .map((image) => image.id)
                        .join(','),
                };

                await upsertProductReview(payload).unwrap();
                toast.success(translate('Review submitted successfully'));
                helpers.resetForm();
                dispatch(closePopup());
            } catch (err: any) {
                toast.error(err.data.message);
            }
        },
    });

    useEffect(() => {
        if (!isActive) {
            return;
        }

        if (!product || !orderId || !shopId) {
            return;
        }

        formik.setValues({
            orderId: orderId || 0,
            productId: product?.id || 0,
            rating: review?.rating || 0,
            description: review?.description || '',
            newImages: [],
            oldImages: review?.images || [],
        });

        getUserReviewOfProduct(product.id, true);
        getUserReviewOfShop(shopId);
    }, [product?.id, review?.id, orderId, shopId]);

    const handleSubmitImpression = async (impression: Impressions) => {
        if (!shopId) {
            toast.error(translate('Shop ID is missing'));
            return;
        }

        try {
            await postShopImpression({
                impression,
                shopId: shopId,
            }).unwrap();

            getUserReviewOfProduct(product!.id);

            toast.success(translate('Impression updated'));
        } catch (err: any) {
            toast.error(err.data.message);
        }
    };

    if (popup === 'product-review' && !product?.id) {
        toast.error(translate('Product ID is missing'));
    }

    return (
        <ModalWrapper isActive={isActive}>
            <button
                className="absolute right-5 top-5 text-xl text-primary z-[1]"
                onClick={() => dispatch(closePopup())}
            >
                <LiaTimesSolid />
            </button>
            <form
                className="theme-modal__body min-h-[400px] w-full bg-white px-0 py-4 sm:py-9"
                onSubmit={formik.handleSubmit}
            >
                <div
                    className={'px-4 sm:px-9 md:px-11 flex items-center gap-5'}
                >
                    <Link
                        to={`/products/${product?.slug}`}
                        className="rounded-card border border-primary-14 p-1"
                    >
                        <Image
                            src={product?.thumbnailImg}
                            alt={product?.name}
                            className={`max-w-[52px] aspect-square`}
                        />
                    </Link>

                    <h3 className="text-black sm:text-sm mb-2 max-w-[220px]">
                        <Link
                            to={`/products/${product?.slug}`}
                            className="hover:text-primary max-w-[300px] line-clamp-2"
                        >
                            {product?.name}
                        </Link>
                    </h3>

                    <div className="ml-auto flex flex-col items-end">
                        <p className="text-sm uppercase">
                            {translate('Rate the product')}
                        </p>
                        <ThemeRating
                            rating={formik.values.rating}
                            readOnly={false}
                            onChange={(value) =>
                                formik.setFieldValue('rating', value)
                            }
                            style={{ maxWidth: 140 }}
                        />
                        {formik.touched.rating && formik.errors.rating ? (
                            <p className="text-sm text-theme-orange mt-1">
                                {formik.errors.rating}
                            </p>
                        ) : null}
                    </div>
                </div>

                <hr className="border border-zinc-100 mt-8 mb-7" />

                <div className="px-4 sm:px-9 md:px-11 space-y-5">
                    <h4 className="text-black text-sm mb-6 uppercase">
                        {translate('Write Review')}
                    </h4>

                    <InputGroup>
                        <Label htmlFor="description">
                            {translate('Description')}
                        </Label>
                        <Textarea
                            id="description"
                            placeholder={translate(
                                'Write the details experience...',
                            )}
                            rows={4}
                            {...formik.getFieldProps('description')}
                        />
                    </InputGroup>

                    <InputGroup>
                        <Label htmlFor="images">{translate('Images')}</Label>
                        <FileDropbox
                            multiple
                            id="images"
                            accept="image/*"
                            name="newImages"
                            newFiles={formik.values.newImages}
                            oldFiles={formik.values.oldImages}
                            onChange={(oldFiles, newFiles) => {
                                formik.setFieldValue('oldImages', oldFiles);
                                formik.setFieldValue('newImages', newFiles);
                            }}
                        />
                    </InputGroup>

                    <div>
                        <Button
                            type="submit"
                            variant="primary"
                            className="w-full font-bold"
                            isLoading={formik.isSubmitting}
                        >
                            {translate('Submit Review')}
                        </Button>
                    </div>
                </div>

                <hr className="border border-zinc-100 mt-8 mb-7" />

                <div className="px-4 sm:px-9 md:px-11 flex justify-between items-center">
                    <div>
                        <p className="mb-1">
                            <span className="uppercase">
                                {translate('Rate the seller?')}
                            </span>
                            <Link
                                to={`/shops/${review?.shop.slug}`}
                                className="text-primary ml-2"
                            >
                                {review?.shop.name}
                            </Link>
                        </p>
                        <p className="text-black/70">
                            {shopImpressionResponse?.impression ? (
                                <>
                                    {translate('You have rated the seller')} “
                                    {shopImpressionResponse?.impression}”{' '}
                                    {translate('previously')}
                                </>
                            ) : (
                                translate("You didn't rated this seller yet!")
                            )}
                        </p>
                    </div>

                    <div className="flex items-center gap-5">
                        {impressions.map((impression) => (
                            <button
                                type="button"
                                className={cn(
                                    `flex flex-col items-center gap-1 group hover:text-theme-orange`,
                                    {
                                        'text-theme-orange':
                                            shopImpressionResponse?.impression ===
                                            impression.value,
                                    },
                                )}
                                onClick={() =>
                                    handleSubmitImpression(impression.value)
                                }
                                key={impression.value}
                            >
                                <span
                                    className={cn(
                                        'text-3xl leading-none group-hover:text-theme-orange',
                                        {
                                            'text-black/70':
                                                shopImpressionResponse?.impression !==
                                                impression.value,
                                            'text-theme-orange':
                                                shopImpressionResponse?.impression ===
                                                impression.value,
                                        },
                                    )}
                                >
                                    {impression.icon}
                                </span>
                                <span>{impression.name}</span>
                            </button>
                        ))}
                    </div>
                </div>
            </form>
        </ModalWrapper>
    );
};

export default ReviewModal;
