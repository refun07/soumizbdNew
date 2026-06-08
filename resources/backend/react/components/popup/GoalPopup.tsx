import { useFormik } from 'formik';
import { useEffect } from 'react';
import { toast } from 'react-hot-toast';
import { number, object } from 'yup';
import Button from '../../../react/components/inputs/Button';
import Input from '../../../react/components/inputs/Input';
import { useAppContext } from '../../Context';
import { translate } from '../../utils/translate';
import PopupWrapper from './PopupWrapper';

const validationSchema = object().shape({
    monthlyGoalAmount: number().required('Monthly goal amount is required'),
});

const GoalPopup = () => {
    const { popup, updateGoal, closePopup } = useAppContext();

    const formik = useFormik({
        initialValues: {
            monthlyGoalAmount: '',
        },
        validationSchema,
        onSubmit: async (values) => {
            await updateGoal(+values.monthlyGoalAmount);

            toast.success('Goal set successfully');
            closePopup();
        },
    });

    useEffect(() => {
        if (!popup.props?.goal) return;

        formik.setValues({
            monthlyGoalAmount: popup.props.goal?.goalAmount || '',
        });
    }, [popup.props?.goal]);

    return (
        <PopupWrapper
            isOpen={popup.name === 'goal-form'}
            classNames={{
                wrapper: 'max-w-[500px]',
            }}
        >
            <div className="bg-sky-200 text-black p-10 rounded-t-md relative overflow-hidden">
                <div className="relative z-[1] max-w-[200px]">
                    <h4 className="text-2xl font-medium mb-1">
                        {translate('Set Your Goal')}
                    </h4>
                    <p className="text-[#4E72A4]">
                        {translate('Having a goal makes you grow faster')}
                    </p>
                </div>

                <img
                    src="/images/icons/goal.png"
                    alt=""
                    className="absolute -bottom-2 -right-2 w-1/2"
                ></img>
            </div>

            <form className="px-10 py-8" onSubmit={formik.handleSubmit}>
                <Input
                    label="Monthly Sales Target"
                    type="number"
                    placeholder='e.g. "1000"'
                    touched={formik.touched.monthlyGoalAmount}
                    error={translate(formik.errors.monthlyGoalAmount)}
                    {...formik.getFieldProps('monthlyGoalAmount')}
                />

                <Button as="button" className="mt-6 w-full font-medium">
                    {translate('Set Goal')}
                </Button>
            </form>
        </PopupWrapper>
    );
};

export default GoalPopup;
