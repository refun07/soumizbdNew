import { useFormik } from 'formik';
import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import toast from 'react-hot-toast';
import { array, mixed, object, string } from 'yup';
import AudioRecorder from '../../components/support/AudioRecorder';
import FileInput from '../../components/inputs/file/FileInput';
import Button from '../../components/buttons/Button';
import Input from '../../components/inputs/Input';
import InputGroup from '../../components/inputs/InputGroup';
import Label from '../../components/inputs/Label';
import SectionTitle from '../../components/titles/SectionTitle';
import { useCreateTicketMutation } from '../../store/features/api/ticketApi';
import { translate } from '../../utils/translate';

interface TicketFormValues {
    order_id: string;
    phone: string;
    subject: string;
    description: string;
    priority: 'low' | 'medium' | 'high';
    files: File[];
    audio: Blob | null;
}

const validationSchema = object().shape({
    order_id: string()
        .required(translate('Order ID is required'))
        .min(5, translate('Order ID must be at least 5 characters')),
    phone: string()
        .required(translate('Phone number is required'))
        .min(10, translate('Phone number must be at least 10 characters'))
        .max(20, translate('Phone number must not exceed 20 characters')),
    subject: string()
        .required(translate('Subject is required'))
        .min(5, translate('Subject must be at least 5 characters'))
        .max(255, translate('Subject must not exceed 255 characters')),
    description: string()
        .required(translate('Description is required'))
        .min(20, translate('Description must be at least 20 characters'))
        .max(5000, translate('Description must not exceed 5000 characters')),
    priority: string().oneOf(['low', 'medium', 'high']).default('medium'),
    files: array().max(10, translate('Maximum 10 files allowed')),
    audio: mixed().nullable(),
});

const TicketCreate = () => {
    const navigate = useNavigate();
    const [createTicket, { isLoading }] = useCreateTicketMutation();
    const [showSuccess, setShowSuccess] = useState(false);
    const [createdTicketUrl, setCreatedTicketUrl] = useState('');
    const [audioFile, setAudioFile] = useState<File | null>(null);

    const initialValues: TicketFormValues = {
        order_id: '',
        phone: '',
        subject: '',
        description: '',
        priority: 'medium',
        files: [],
        audio: null,
    };

    const formik = useFormik<TicketFormValues>({
        initialValues,
        validationSchema,
        onSubmit: async (values) => {
            try {
                const result = await createTicket({
                    order_id: values.order_id,
                    phone: values.phone,
                    subject: values.subject,
                    description: values.description,
                    priority: values.priority,
                    files: values.files,
                    audio: audioFile,
                }).unwrap();

                if (result.success) {
                    setCreatedTicketUrl(result.data.public_url);
                    setShowSuccess(true);
                    toast.success(translate('Ticket created successfully'));
                }
            } catch (error: any) {
                const errorMessage = error?.data?.message || translate('Failed to create ticket');
                toast.error(errorMessage);
            }
        },
    });

    const handleAudioRecorded = (audioBlob: Blob | null) => {
        formik.setFieldValue('audio', audioBlob);
        if (audioBlob) {
            // Convert Blob to File for upload
            const file = new File([audioBlob], `recording_${Date.now()}.webm`, {
                type: 'audio/webm',
            });
            setAudioFile(file);
        } else {
            setAudioFile(null);
        }
    };

    const handleFilesChange = (_oldFiles: unknown[], newFiles: File[]) => {
        formik.setFieldValue('files', newFiles);
    };

    const copyToClipboard = (text: string) => {
        navigator.clipboard.writeText(text);
        toast.success(translate('Copied to clipboard'));
    };

    if (showSuccess) {
        return (
            <div className="container max-w-2xl mx-auto p-4 sm:p-6">
                <div className="bg-white rounded-lg shadow-lg p-6 sm:p-8 text-center">
                    <div className="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg className="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    
                    <h2 className="text-2xl font-bold mb-4">{translate('Ticket Created Successfully!')}</h2>
                    
                    <p className="text-gray-600 mb-6">
                        {translate('Your support ticket has been submitted. You can track the status using the link below.')}
                    </p>
                    
                    <div className="bg-gray-50 rounded-lg p-4 mb-6">
                        <p className="text-sm text-gray-500 mb-2">{translate('Your ticket URL:')}</p>
                        <div className="flex gap-2">
                            <input
                                type="text"
                                value={createdTicketUrl}
                                readOnly
                                className="flex-1 px-3 py-2 border rounded bg-white text-sm"
                            />
                            <button
                                onClick={() => copyToClipboard(createdTicketUrl)}
                                className="px-4 py-2 bg-primary text-white rounded hover:bg-primary/90 transition-colors"
                            >
                                {translate('Copy')}
                            </button>
                        </div>
                    </div>
                    
                    <div className="flex flex-col sm:flex-row gap-3 justify-center">
                        <Button
                            onClick={() => navigate(createdTicketUrl.replace(window.location.origin, ''))}
                            variant="primary"
                        >
                            {translate('View Ticket')}
                        </Button>
                        
                        <Button
                            onClick={() => {
                                setShowSuccess(false);
                                formik.resetForm();
                                setAudioFile(null);
                            }}
                            variant="outline"
                        >
                            {translate('Create Another Ticket')}
                        </Button>
                    </div>
                </div>
            </div>
        );
    }

    return (
        <div className="container max-w-3xl mx-auto p-3 sm:p-6">
            <SectionTitle
                title={translate('Create Support Ticket')}
                classNames={{
                    title: 'ek-h3 text-center mb-6',
                }}
            />

            <form onSubmit={formik.handleSubmit} className="space-y-6">
                {/* Order Information */}
                <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
                    <h3 className="text-lg font-semibold mb-4 pb-2 border-b">
                        {translate('Order Information')}
                    </h3>
                    
                    <div className="grid sm:grid-cols-2 gap-4">
                        <InputGroup>
                            <Label>{translate('Order ID')} *</Label>
                            <Input
                                type="text"
                                placeholder={translate('e.g., ORD123456')}
                                error={formik.errors.order_id}
                                touched={formik.touched.order_id}
                                {...formik.getFieldProps('order_id')}
                            />
                        </InputGroup>

                        <InputGroup>
                            <Label>{translate('Phone Number')} *</Label>
                            <Input
                                type="tel"
                                placeholder={translate('Phone number used for order')}
                                error={formik.errors.phone}
                                touched={formik.touched.phone}
                                {...formik.getFieldProps('phone')}
                            />
                        </InputGroup>
                    </div>
                    
                    <p className="text-sm text-gray-500 mt-2">
                        {translate('Order ID and phone number must match your order details.')}
                    </p>
                </div>

                {/* Issue Details */}
                <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
                    <h3 className="text-lg font-semibold mb-4 pb-2 border-b">
                        {translate('Issue Details')}
                    </h3>

                    <div className="space-y-4">
                        <InputGroup>
                            <Label>{translate('Subject')} *</Label>
                            <Input
                                type="text"
                                placeholder={translate('Brief description of your issue')}
                                error={formik.errors.subject}
                                touched={formik.touched.subject}
                                {...formik.getFieldProps('subject')}
                            />
                        </InputGroup>

                        <InputGroup>
                            <Label>{translate('Priority')}</Label>
                            <select
                                className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                {...formik.getFieldProps('priority')}
                            >
                                <option value="low">{translate('Low - General inquiry')}</option>
                                <option value="medium">{translate('Medium - Issue affecting usage')}</option>
                                <option value="high">{translate('High - Urgent issue')}</option>
                            </select>
                        </InputGroup>

                        <InputGroup>
                            <Label>{translate('Description')} *</Label>
                            <textarea
                                rows={5}
                                placeholder={translate('Please describe your issue in detail...')}
                                className={`w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary resize-none ${
                                    formik.errors.description && formik.touched.description
                                        ? 'border-red-500'
                                        : ''
                                }`}
                                {...formik.getFieldProps('description')}
                            />
                            {formik.errors.description && formik.touched.description && (
                                <p className="text-red-500 text-sm mt-1">{formik.errors.description}</p>
                            )}
                            <p className="text-xs text-gray-500 mt-1">
                                {translate('Minimum 20 characters')}
                            </p>
                        </InputGroup>
                    </div>
                </div>

                {/* Audio Recording */}
                <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
                    <h3 className="text-lg font-semibold mb-4 pb-2 border-b">
                        {translate('Voice Message (Optional)')}
                    </h3>
                    
                    <p className="text-sm text-gray-600 mb-4">
                        {translate('Record a voice message to explain your issue. Maximum 5 minutes.')}
                    </p>
                    
                    <AudioRecorder
                        onAudioRecorded={handleAudioRecorded}
                        maxDuration={300}
                    />
                </div>

                {/* File Attachments */}
                <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
                    <h3 className="text-lg font-semibold mb-4 pb-2 border-b">
                        {translate('Attachments (Optional)')}
                    </h3>
                    
                    <p className="text-sm text-gray-600 mb-4">
                        {translate('Upload images or videos related to your issue. Maximum 10 files (50MB each).')}
                    </p>
                    
                    <FileInput
                        multiple
                        accept="image/*,video/*"
                        newFiles={formik.values.files}
                        onChange={handleFilesChange}
                        error={formik.errors.files as string}
                        touched={!!formik.touched.files && formik.touched.files.length > 0}
                        placeholder={translate('Select files to upload')}
                    />
                    
                    {formik.values.files.length > 0 && (
                        <p className="text-sm text-gray-600 mt-2">
                            {translate(`${formik.values.files.length} file(s) selected`)}
                        </p>
                    )}
                </div>

                {/* Submit Button */}
                <div className="flex justify-end pt-4">
                    <Button
                        type="submit"
                        variant="primary"
                        size="lg"
                        isLoading={isLoading}
                        disabled={isLoading || !formik.isValid}
                    >
                        {translate('Submit Ticket')}
                    </Button>
                </div>
            </form>
        </div>
    );
};

export default TicketCreate;