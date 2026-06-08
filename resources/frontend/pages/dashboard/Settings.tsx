import { useFormik } from 'formik';
import { useEffect } from 'react';
import toast from 'react-hot-toast';
import { mixed, object, string } from 'yup';
import Button from '../../components/buttons/Button';
import AddAddressCard from '../../components/card/AddAddressCard';
import AddressCard from '../../components/card/AddressCard';
import PasswordUpdateForm from '../../components/form/PasswordUpdateForm';
import Input from '../../components/inputs/Input';
import InputGroup from '../../components/inputs/InputGroup';
import Label from '../../components/inputs/Label';
import FileInput from '../../components/inputs/file/FileInput';
import SectionTitle from '../../components/titles/SectionTitle';
import { useUpdateProfileInfoMutation } from '../../store/features/api/userApi';
import { useAuth } from '../../store/features/auth/authSlice';
import { getUser } from '../../store/features/auth/authThunks';
import { useGetAddressesQuery } from '../../store/features/checkout/checkoutApi';
import { useAppDispatch } from '../../store/store';
import { translate } from '../../utils/translate';

const validationSchema = object().shape({
    name: string().required('Name is required'),
    phone: string().required('Phone is required'),
    avatar: mixed(),
});

interface IInitialValues {
    name: string;
    phone: string;
    avatar?: File;
}

const Settings = () => {
    const dispatch = useAppDispatch();
    const { user } = useAuth();
    const { data: addressRes } = useGetAddressesQuery();
    const [updateProfileInfo] = useUpdateProfileInfoMutation();

    const addresses = addressRes?.addresses;

    const initialValues: IInitialValues = {
        name: '',
        phone: '',
        avatar: undefined,
    };

    const formik = useFormik({
        initialValues,
        validationSchema,
        onSubmit: async (values) => {
            try {
                await updateProfileInfo(values).unwrap();
                toast.success(translate('Profile updated successfully'));
                await dispatch(getUser());
            } catch (error) {}
        },
    });

    useEffect(() => {
        if (user) {
            formik.setValues({
                name: user.name,
                phone: user.phone,
            });
        }
    }, [user]);

    return (
        <div className="container space-y-5 sm:space-y-10 p-3.5 sm:p-6 lg:p-10 border rounded-card">
            <SectionTitle
                title="[Settings]"
                classNames={{
                    title: 'ek-h3',
                }}
            />

            <form
                className="rounded-card border border-zinc-100"
                onSubmit={formik.handleSubmit}
            >
                <h3 className="py-3.5 px-7 bg-secondary/20 uppercase">
                    {translate('Personal Information')}
                </h3>

                <div className="py-4 sm:py-6 px-3 sm:px-5 md:px-9 grid sm:grid-cols-2 gap-4">
                    <InputGroup>
                        <Label>{translate('Name')}</Label>
                        <Input
                            type="text"
                            placeholder={translate('Your Name')}
                            error={formik.errors.name}
                            touched={formik.touched.name}
                            {...formik.getFieldProps('name')}
                        />
                    </InputGroup>

                    <InputGroup>
                        <Label>{translate('Phone')}</Label>
                        <Input
                            type="text"
                            placeholder={translate('Your Phone')}
                            error={formik.errors.phone}
                            touched={formik.touched.phone}
                            {...formik.getFieldProps('phone')}
                        />
                    </InputGroup>

                    <InputGroup>
                        <Label>{translate('Avatar')}</Label>
                        <FileInput
                            type="file"
                            accept="image/*"
                            multiple={false}
                            newFiles={
                                formik.values.avatar
                                    ? [formik.values.avatar]
                                    : []
                            }
                            placeholder={translate(
                                'Select File (jpg, png, webp)',
                            )}
                            onChange={(_oldFiles, newFiles) => {
                                formik.setFieldValue('avatar', newFiles[0]);
                            }}
                        />
                    </InputGroup>

                    <div className="max-sm:hidden"></div>

                    <div className="flex justify-end sm:col-span-2">
                        <Button
                            isLoading={formik.isSubmitting}
                            disabled={formik.isSubmitting}
                        >
                            {translate('Save Data')}
                        </Button>
                    </div>
                </div>
            </form>

            <div className="rounded-card border border-zinc-100">
                <h3 className="py-3.5 px-7 bg-secondary/20 uppercase">
                    {translate('Shipping Address')}
                </h3>

                <div className="py-4 sm:py-6 px-3 sm:px-5 md:px-9">
                    <div className="grid sm:grid-cols-2 gap-3">
                        {addresses?.map((address) => (
                            <AddressCard
                                address={address}
                                onClick={() =>
                                    formik.setFieldValue(
                                        'shippingAddressId',
                                        address.id,
                                    )
                                }
                                key={address.id}
                            />
                        ))}

                        <AddAddressCard />
                    </div>
                </div>
            </div>

            <PasswordUpdateForm />
        </div>
    );
};

export default Settings;
