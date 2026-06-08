import { ChangeEvent } from 'react';
import { IoMdClose } from 'react-icons/io';
import { translate } from '../../../utils/translate';

interface Props
    extends Omit<React.InputHTMLAttributes<HTMLInputElement>, 'onChange'> {
    oldFiles?: {
        id: number;
        image: string;
    }[];
    newFiles?: File[];
    error?: string;
    touched?: boolean;
    onChange?: (
        oldFiles: {
            id: number;
            image: string;
        }[],
        newFiles: File[],
    ) => void;
}

const FileInput = ({
    type,
    className,
    newFiles = [],
    oldFiles = [],
    value,
    error,
    touched,
    onChange,
    ...rest
}: Props) => {
    // ! handleChange and handleDelete are same as FileDropbox.tsx
    const handleChange = (e: ChangeEvent<HTMLInputElement>) => {
        const files = e.target.files;

        if (files?.length) {
            // check if file already exists

            if (!rest.multiple) {
                onChange?.(oldFiles, Array.from(files));
                return;
            }

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

    const totalFiles = oldFiles.length + newFiles.length;

    const inputValue = rest.multiple
        ? `${totalFiles} ${translate('Files Selected')}`
        : newFiles[0]?.name;

    return (
        <>
            <label className="flex h-10 cursor-pointer">
                <span className="bg-primary text-white py-1 px-5 inline-flex items-center justify-center rounded-l-md">
                    {translate('browse')}
                </span>
                <span className="grow inline-flex items-center px-3 xs:px-5 border border-primary-14 rounded-r-md text-black/70">
                    {inputValue ||
                        rest.placeholder ||
                        translate('Select a File')}
                </span>
                <input
                    type="file"
                    {...rest}
                    onChange={handleChange}
                    className="hidden"
                />
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

export default FileInput;
