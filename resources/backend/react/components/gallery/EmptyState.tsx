import { FaUpload } from 'react-icons/fa';
import { translate } from '../../utils/translate';

interface EmptyStateProps {
    onUploadClick: () => void;
}

const EmptyState = ({ onUploadClick }: EmptyStateProps) => {
    return (
        <div className="grow">
            <div className="h-full flex flex-col items-center justify-center max-w-[290px] mx-auto mt-10">
                <img 
                    src="/images/no-media.png" 
                    alt="No media"
                    className="max-w-[150px] opacity-50"
                />
                <p className="text-lg font-semibold text-center mt-5 text-foreground">
                    {translate('No Media File Found!')}
                </p>
                <p className="mt-1 mb-4 text-center text-muted">
                    {translate("Seems like this field is empty. Let's upload some files!")}
                </p>

                <button
                    type="button"
                    onClick={onUploadClick}
                    className="text-primary text-sm mt-2 inline-flex items-center gap-2 hover:opacity-80 transition-opacity"
                >
                    <FaUpload />
                    {translate('Upload Files')}
                </button>
            </div>
        </div>
    );
};

export default EmptyState;
