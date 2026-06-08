import { IoMdClose } from 'react-icons/io';
import { IoCloudUploadOutline } from 'react-icons/io5';
import { IReview } from '../../../types/product';
import { cn } from '../../../utils/cn';
import { translate } from '../../../utils/translate';

interface Props
    extends Omit<React.InputHTMLAttributes<HTMLInputElement>, 'onChange'> {
    oldFiles?: IReview['images'];
    newFiles?: File[];
    error?: string;
    touched?: boolean;
    onChange?: (oldFiles: IReview['images'], newFiles: File[]) => void;
}

const FileDropbox = ({
    className,
    newFiles = [],
    oldFiles = [],
    value,
    error,
    touched,
    onChange,
    ...rest
}: Props) => {
    // ! handleChange and handleDelete are same as FileInput.tsx
    const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const files = e.target.files;

        if (files) {
            // check if file already exists
            const newImages = Array.from(files).filter(
                (file) => !newFiles.some((image) => image.name === file.name),
            );

            const updatedImages = [...newFiles, ...newImages];

            onChange?.(oldFiles, updatedImages);
        }
    };

    const handleDelete = (index: number, isNew: boolean) => {
        if (isNew) {
            const updatedImages = newFiles.filter((_, i) => i !== index);
            onChange?.(oldFiles, updatedImages);
        } else {
            const updatedImages = oldFiles.filter((_, i) => i !== index);
            onChange?.(updatedImages, newFiles);
        }
    };

    return (
        <>
            <label
                className={cn(
                    'flex flex-col items-center cursor-pointer py-7 bg-secondary/10 border border-secondary border-dashed rounded-card',
                    className,
                )}
            >
                <input
                    type="file"
                    onChange={handleChange}
                    {...rest}
                    className="hidden"
                />

                <span className="text-4xl text-black/70">
                    <IoCloudUploadOutline />
                </span>
                <p className="text-primary mt-1.5">
                    {translate('Drag and drop or upload images')}
                </p>
            </label>

            {/* previews */}
            {oldFiles.length || newFiles.length ? (
                <div className="flex gap-4 mt-4">
                    {oldFiles.map((image, i) => (
                        <div
                            key={image.id}
                            className="relative w-16 h-16 bg-secondary/5 rounded-card"
                        >
                            <img
                                src={image.image}
                                alt=""
                                className="w-full object-cover aspect-square rounded-card"
                            />

                            <button
                                type="button"
                                className="absolute right-0 top-0 translate-x-1/2 -translate-y-1/4 bg-theme-alert text-white w-4 h-4 rounded-full"
                                onClick={() => handleDelete(i, false)}
                            >
                                <IoMdClose />
                            </button>
                        </div>
                    ))}

                    {newFiles.map((image, i) => {
                        const url = URL.createObjectURL(image);
                        return (
                            <div
                                key={i}
                                className="relative w-16 h-16 bg-secondary/5 rounded-card"
                            >
                                <img
                                    src={url}
                                    alt=""
                                    className="w-full object-cover aspect-square rounded-card"
                                />

                                <button
                                    type="button"
                                    className="absolute right-0 top-0 translate-x-1/2 -translate-y-1/4 bg-theme-alert text-white w-4 h-4 rounded-full"
                                    onClick={() => handleDelete(i, true)}
                                >
                                    <IoMdClose />
                                </button>
                            </div>
                        );
                    })}
                </div>
            ) : null}
        </>
    );
};

export default FileDropbox;
