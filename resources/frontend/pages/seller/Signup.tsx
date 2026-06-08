import { Accordion } from '@szhsin/react-accordion';
import { useFormik } from 'formik';
import toast from 'react-hot-toast';
import { useNavigate } from 'react-router-dom';
import { object, string } from 'yup';
import AccordionItem from '../../components/accordion/AccordionItem';
import Button from '../../components/buttons/Button';
import { Cards, Gift, PercentBadge, Shop } from '../../components/icons';
import Checkbox from '../../components/inputs/Checkbox';
import Input from '../../components/inputs/Input';
import InputGroup from '../../components/inputs/InputGroup';
import Label from '../../components/inputs/Label';
import SectionTitle from '../../components/titles/SectionTitle';
import { useSignupMutation } from '../../store/features/api/sellerApi';
import { translate } from '../../utils/translate';

const validationSchema = object().shape({
    name: string().required('Name is required'),
    email: string()
        .email('Provide a valid email')
        .required('Email is required'),
    phone: string().required('Phone is required'),
    shopName: string().required('Shop Name is required'),
    password: string().required('Password is required'),
    confirmPassword: string().required('Confirm Password is required'),
});

const SellerSignup = () => {
    const navigate = useNavigate();
    const [signup] = useSignupMutation();

    const formik = useFormik({
        initialValues: {
            name: '',
            email: '',
            phone: '',
            shopName: '',
            password: '',
            confirmPassword: '',
        },
        validationSchema,
        onSubmit: async (values) => {
            try {
                await signup(values);
                navigate('/seller/signup/success');
            } catch (err: any) {
                toast.error(err.data.message);
            }
        },
    });

    return (
        <>
            <section>
                <div
                    className="relative flex items-center h-[440px] bg-cover text-white"
                    style={{
                        backgroundImage: 'url(/images/seller-banner.png)',
                    }}
                >
                    <div className="container relative z-[1]">
                        <div className="max-w-[600px]">
                            <h1 className="ek-h1 font-medium">
                                {translate('Sell Your Products on EpikCart!')}
                            </h1>
                            <p className="mt-3">
                                {translate(
                                    'Epikcart is a prominent online shopping site headquartered in Bangladesh with a presence across various Asian countries',
                                )}
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <section className="container grid grid-cols-12 gap-6 items-center py-[94px]">
                <div className="col-span-7">
                    <h2 className="ek-h2">
                        {translate('Why Sell on EpikCart?')}
                    </h2>
                    <p className=" text-black/70 mt-1 hidden">
                        {translate(
                            'ARM Ecommerce Bangladesh is a prominent online shopping site headquartered in Bangladesh with a across various Asian countries',
                        )}
                    </p>

                    <div className="grid grid-cols-2 gap-7 mt-11">
                        <div className="">
                            <span className="w-16 inline-block">
                                <Cards />
                            </span>
                            <h4 className="ek-h3 mt-4 mb-2">
                                {translate('Timely Payments')}
                            </h4>
                            <p className="text-black/70">
                                {translate(
                                    "Millions of customers on EpikCart, Bangladesh's most visited shopping destination",
                                )}
                            </p>
                        </div>
                        <div className="">
                            <span className="w-16 inline-block">
                                <Gift />
                            </span>
                            <h4 className="ek-h3 mt-4 mb-2">
                                {translate('Marketing Tools')}
                            </h4>
                            <p className="text-black/70">
                                {translate(
                                    "Millions of customers on EpikCart, Bangladesh's most visited shopping destination",
                                )}
                            </p>
                        </div>
                        <div className="">
                            <span className="w-16 inline-block">
                                <Shop />
                            </span>
                            <h4 className="ek-h3 mt-4 mb-2">
                                {translate('Timely Payments')}
                            </h4>
                            <p className="text-black/70">
                                {translate(
                                    "Millions of customers on EpikCart, Bangladesh's most visited shopping destination",
                                )}
                            </p>
                        </div>
                        <div className="">
                            <span className="w-16 inline-block">
                                <PercentBadge />
                            </span>
                            <h4 className="ek-h3 mt-4 mb-2">
                                {translate('Marketing Tools')}
                            </h4>
                            <p className="text-black/70">
                                {translate(
                                    "Millions of customers on EpikCart, Bangladesh's most visited shopping destination",
                                )}
                            </p>
                        </div>
                    </div>
                </div>

                <form
                    className="col-span-5 border border-zinc-100 bg-white rounded-card py-8 md:py-12 xl:py-[53px] px-4 sm:px-8 md:px-12 xl:px-[96px]"
                    onSubmit={formik.handleSubmit}
                >
                    <SectionTitle title={translate('[Register] as Seller')} />

                    <div className="space-y-4">
                        <InputGroup>
                            <Label>{translate('Full Name')}</Label>
                            <Input
                                placeholder={translate('Type Your Full Name')}
                                {...formik.getFieldProps('name')}
                            />
                        </InputGroup>

                        <InputGroup>
                            <Label>{translate('Email')}</Label>
                            <Input
                                placeholder={translate('Type Your Email')}
                                {...formik.getFieldProps('email')}
                            />
                        </InputGroup>

                        <InputGroup>
                            <Label>{translate('Phone')}</Label>
                            <Input
                                placeholder={translate('Valid Phone No.')}
                                {...formik.getFieldProps('phone')}
                            />
                        </InputGroup>

                        <InputGroup>
                            <Label>{translate('Shop Name')}</Label>
                            <Input
                                placeholder={translate('Type Your Shop Name')}
                                {...formik.getFieldProps('shopName')}
                            />
                        </InputGroup>

                        <InputGroup>
                            <Label>{translate('Password')}</Label>
                            <Input
                                type="password"
                                placeholder={translate('Type Your Password')}
                                {...formik.getFieldProps('password')}
                            />
                        </InputGroup>

                        <InputGroup>
                            <Label>{translate('Confirm Password')}</Label>
                            <Input
                                type="password"
                                placeholder={translate(
                                    'Type Your Password Again',
                                )}
                                {...formik.getFieldProps('confirmPassword')}
                            />
                        </InputGroup>

                        <Button
                            variant="primary"
                            className="mt-5 font-bold w-full text-center"
                            isLoading={formik.isSubmitting}
                        >
                            {translate('Register')}
                        </Button>

                        <Checkbox
                            className="!mt-5"
                            label={
                                <span className="text-black/70">
                                    {translate('By registering i agree to the')}
                                    <a href="#" className="!text-primary ml-1">
                                        {translate('Terms and Conditions')}
                                    </a>
                                </span>
                            }
                        />

                        <div className="text-center mt-6">
                            {translate('Already Have an Account?')}
                            <a href="#" className="text-primary ml-1">
                                {translate('Login Now')}
                            </a>
                        </div>
                    </div>
                </form>
            </section>

            <section className="relative pb-11 text-white hidden">
                <div
                    className="absolute top-[100px] left-0 right-0 bottom-0"
                    style={{
                        backgroundImage:
                            'linear-gradient(rgb(var(--theme-orange) / 0.95), rgb(var(--theme-orange) / 0.95)), url("/images/banner-desk.png")',
                    }}
                ></div>

                <div className="relative max-w-[678px] px-[15px] mx-auto text-center">
                    <img src="/images/tutorial-thumbnail.png" alt="" />
                    <h4 className="mt-4 text-[40px]">
                        {translate('How to Open a Shop')}
                    </h4>
                    <p>
                        {translate(
                            'ARM Ecommerce Bangladesh is a prominent online shopping site headquartered in Bangladesh with a presence across various Asian countries',
                        )}
                    </p>
                </div>
            </section>

            <section className="pt-[100px] pb-[50px] hidden">
                <div className="container grid grid-cols-12 items-center">
                    <div className="col-span-5">
                        <h3 className="text-[40px] leading-none mb-3">
                            {translate('Frequently Asked Questions')}
                        </h3>
                        <p className=" text-black/70 ">
                            {translate(
                                'ARM Ecommerce Bangladesh is a prominent online shopping site and for headquartered in Bangladesh a presence across various Asian countries commerce Bangladesh is a prominent',
                            )}
                        </p>
                    </div>

                    <div className="col-span-7">
                        <Accordion
                            allowMultiple
                            transition
                            transitionTimeout={200}
                            className="space-y-2.5"
                        >
                            {Array.from({ length: 4 }).map((_, i) => (
                                <AccordionItem
                                    key={i}
                                    initialEntered={i === 0}
                                    buttonProps={{
                                        className: ({ isEnter }) =>
                                            isEnter
                                                ? 'bg-theme-orange'
                                                : 'bg-theme-orange/10 text-theme-orange',
                                    }}
                                    header={
                                        <p className="font-medium">
                                            {translate(
                                                'What product I can sell on this platform?',
                                            )}
                                        </p>
                                    }
                                    className="text-sm"
                                >
                                    {translate(
                                        'ARM Ecommerce Bangladesh is a prominent online shopping site headquartered in Bangladesh, with a presence across various Asian countries, including Singapore, Thailand, Indonesia, Vietnam, Philippines, and Taiwan. We bring you irresistible deals, offering an extensive range of products at pocket-friendly prices ',
                                    )}
                                </AccordionItem>
                            ))}
                        </Accordion>
                    </div>
                </div>
            </section>
        </>
    );
};

export default SellerSignup;
