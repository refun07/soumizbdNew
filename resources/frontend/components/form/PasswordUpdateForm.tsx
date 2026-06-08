import { useFormik } from 'formik';
import toast from 'react-hot-toast';
import { useUpdatePasswordMutation } from '../../store/features/api/userApi';
import { translate } from '../../utils/translate';
import Button from '../buttons/Button';
import Input from '../inputs/Input';
import InputGroup from '../inputs/InputGroup';
import Label from '../inputs/Label';

const PasswordUpdateForm = () => {
    const [updatePassword] = useUpdatePasswordMutation();

    const formik = useFormik({
        initialValues: {
            password: '',
            passwordConfirmation: '',
        },
        onSubmit: async (values) => {
            try {
                await updatePassword(values).unwrap();
                toast.success(translate('Password updated successfully'));
            } catch (error) {}
        },
    });

    return (
        <form
            className="rounded-card border border-zinc-100"
            onSubmit={formik.handleSubmit}
        >
            <h3 className="py-3.5 px-7 bg-secondary/20 uppercase">
                {translate('Password')}
            </h3>
            <div className="py-4 sm:py-6 px-3 sm:px-5 md:px-9 grid md:grid-cols-5">
                <div className="col-span-2">
                    <InputGroup>
                        <Label>{translate('Password')}</Label>
                        <Input
                            type="password"
                            {...formik.getFieldProps('password')}
                        />
                    </InputGroup>
                </div>
                <div className="col-span-2">
                    <InputGroup>
                        <Label>{translate('Confirm Password')}</Label>
                        <Input
                            type="password"
                            {...formik.getFieldProps('passwordConfirmation')}
                        />
                    </InputGroup>
                </div>
                <div className="col-span-1 md:mt-7">
                    <Button
                        as="button"
                        type="submit"
                        isLoading={formik.isSubmitting}
                        disabled={formik.isSubmitting}
                    >
                        {translate('save')}
                    </Button>
                </div>
            </div>
        </form>
    );
};

export default PasswordUpdateForm;
