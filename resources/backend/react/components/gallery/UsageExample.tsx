import { useState } from 'react';
import { MediaManagerModal, MediaFile } from '@/components/gallery';

// Example 1: Single Image Selection
const SingleImageExample = () => {
    const [isOpen, setIsOpen] = useState(false);
    const [selectedImage, setSelectedImage] = useState<number | null>(null);

    const handleSelect = (files: MediaFile[]) => {
        if (files.length > 0) {
            setSelectedImage(files[0].id);
        }
    };

    return (
        <div>
            <label>Product Image</label>
            <button
                type="button"
                onClick={() => setIsOpen(true)}
                className="btn-primary"
            >
                {selectedImage ? 'Change Image' : 'Select Image'}
            </button>

            {selectedImage && (
                <span>1 media selected</span>
            )}

            <MediaManagerModal
                isOpen={isOpen}
                onClose={() => setIsOpen(false)}
                onSelect={handleSelect}
                multiple={false}
                accept="image/*"
                initialValue={selectedImage ? [selectedImage] : []}
            />
        </div>
    );
};

// Example 2: Multiple Image Selection (Gallery)
const GalleryExample = () => {
    const [isOpen, setIsOpen] = useState(false);
    const [selectedImages, setSelectedImages] = useState<number[]>([]);

    const handleSelect = (files: MediaFile[]) => {
        setSelectedImages(files.map(f => f.id));
    };

    return (
        <div>
            <label>Product Gallery</label>
            <button
                type="button"
                onClick={() => setIsOpen(true)}
                className="btn-primary"
            >
                Browse Images
            </button>

            {selectedImages.length > 0 && (
                <span>{selectedImages.length} media selected</span>
            )}

            <MediaManagerModal
                isOpen={isOpen}
                onClose={() => setIsOpen(false)}
                onSelect={handleSelect}
                multiple={true}
                accept="image/*"
                initialValue={selectedImages}
            />
        </div>
    );
};

// Example 3: All File Types
const AllFilesExample = () => {
    const [isOpen, setIsOpen] = useState(false);
    const [selectedFiles, setSelectedFiles] = useState<number[]>([]);

    const handleSelect = (files: MediaFile[]) => {
        setSelectedFiles(files.map(f => f.id));
    };

    return (
        <div>
            <button
                type="button"
                onClick={() => setIsOpen(true)}
                className="btn-primary"
            >
                Browse Files
            </button>

            <MediaManagerModal
                isOpen={isOpen}
                onClose={() => setIsOpen(false)}
                onSelect={handleSelect}
                multiple={true}
                accept="*"
                initialValue={selectedFiles}
            />
        </div>
    );
};

export { SingleImageExample, GalleryExample, AllFilesExample };
