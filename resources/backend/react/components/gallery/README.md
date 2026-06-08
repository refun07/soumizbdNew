# React Media Manager

A complete React implementation of the Media Manager/Gallery system for the admin dashboard. This is a native React clone of the existing jQuery-based media manager.

## Features

- **Browse Files**: Responsive grid view (3-9 columns based on viewport)
- **Upload Files**: Drag-and-drop file upload with native browser API
- **Search**: Real-time search with debouncing (500ms)
- **Sort**: Newest/Oldest/Smallest/Largest
- **Select**: Single or multiple file selection
- **Actions per file**:
  - View details and edit alt text
  - AI background removal (for images)
  - Copy direct link
  - Download file
  - Delete file
- **Bulk Actions**: Delete multiple selected files at once
- **Pagination**: Prev/Next navigation
- **Responsive**: Mobile-friendly with collapsible filters
- **TypeScript**: Full type safety
- **Reusable Hook**: `useMediaManager` for custom implementations

## File Structure

```
resources/backend/react/
├── components/gallery/
│   ├── MediaManagerModal.tsx    # Main modal wrapper
│   ├── MediaBrowser.tsx          # File browser view
│   ├── MediaUploader.tsx         # Upload zone
│   ├── MediaToolbar.tsx          # Search and sort controls
│   ├── MediaGrid.tsx             # Responsive file grid
│   ├── MediaCard.tsx             # Individual file card
│   ├── MediaInfoModal.tsx        # File details modal
│   ├── EmptyState.tsx            # No files placeholder
│   ├── DeleteConfirmModal.tsx    # Bulk delete confirmation
│   ├── UsageExample.tsx          # Usage examples
│   └── index.ts                  # Barrel export
├── hooks/
│   └── useMediaManager.ts        # Core state management hook
├── services/
│   └── mediaManagerApi.ts        # API service layer
├── types/
│   └── media.ts                  # TypeScript interfaces
└── utils/
    └── formatters.ts             # File size/date formatters
```

## Installation

No additional dependencies required! The component uses:
- React Icons (already in project)
- Existing axios instance with caching
- Existing UI components (Button, Input, SelectInput, Spinner)
- Native file API for uploads (no react-dropzone needed)

## Usage

### Basic Example

```typescript
import { useState } from 'react';
import { MediaManagerModal, MediaFile } from '@/components/gallery';

const ProductForm = () => {
  const [isOpen, setIsOpen] = useState(false);
  const [selectedImages, setSelectedImages] = useState<number[]>([]);

  const handleSelect = (files: MediaFile[]) => {
    setSelectedImages(files.map(f => f.id));
    setIsOpen(false);
  };

  return (
    <div>
      <button onClick={() => setIsOpen(true)}>
        Browse Images
      </button>
      
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
```

### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `isOpen` | `boolean` | required | Controls modal visibility |
| `onClose` | `() => void` | required | Called when modal closes |
| `onSelect` | `(files: MediaFile[]) => void` | required | Called when user confirms selection |
| `multiple` | `boolean` | `false` | Allow multiple file selection |
| `accept` | `string` | `'all'` | Filter by file type: `'image/*'`, `'video/*'`, `'audio/*'`, or `'*'` |
| `initialValue` | `number[]` | `[]` | Array of media IDs to pre-select |

### MediaFile Interface

```typescript
interface MediaFile {
  id: number;
  user_id: number;
  media_file: string;        // File path: "uploads/all/filename.webp"
  media_name: string;        // Original filename (no extension)
  media_extension: string;   // webp, jpg, pdf, etc.
  media_type: 'image' | 'video' | 'audio' | 'document' | 'archive' | 'others';
  media_size: number;        // Size in bytes
  alt_text?: string;         // Optional alt text for SEO
  created_at: string;
  updated_at: string;
}
```

## Using the Hook Directly

For more control, use the `useMediaManager` hook:

```typescript
import { useMediaManager } from '@/components/gallery';

const CustomGallery = () => {
  const {
    files,
    selectedIds,
    loading,
    filter,
    meta,
    selectFile,
    deselectFile,
    deleteFile,
    uploadFiles,
    nextPage,
    prevPage,
    setFilter,
  } = useMediaManager({
    multiple: true,
    accept: 'image/*',
  });

  // Build your own UI
  return (
    <div>
      {/* Custom implementation */}
    </div>
  );
};
```

### Hook Return Values

```typescript
{
  // State
  files: MediaFile[];              // Current page files
  selectedIds: number[];           // Selected file IDs
  loading: boolean;                // Loading state
  uploading: boolean;              // Upload in progress
  view: 'browser' | 'uploader';    // Current view
  filter: MediaFilter;             // Current filter state
  meta: MediaMeta;                 // Pagination metadata
  hasSelected: boolean;            // Any files selected?
  selectedFiles: MediaFile[];      // Full selected file objects

  // Actions
  fetchFiles: (skipLoading?: boolean) => Promise<void>;
  selectFile: (id: number) => void;
  deselectFile: (id: number) => void;
  selectAll: () => void;
  deselectAll: () => void;
  deleteSelected: () => Promise<void>;
  deleteFile: (id: number) => Promise<void>;
  uploadFiles: (files: File[]) => Promise<void>;
  removeBackground: (id: number) => Promise<void>;
  updateAltText: (id: number, altText: string) => Promise<void>;
  nextPage: () => void;
  prevPage: () => void;
  setView: (view: MediaView) => void;
  setFilter: (filter: Partial<MediaFilter>) => void;
  reset: () => void;
}
```

## API Endpoints Used

The component uses these existing API endpoints:

- `GET /file-manager/get-uploaded-files` - List files with pagination
- `POST /file-manager/upload` - Upload files
- `DELETE /file-manager/destroy/{id}` - Delete file(s)
- `POST /file-manager/get-file-by-ids` - Get files by IDs
- `POST /file-manager/bg-remove/{id}` - AI background removal
- `POST /file-manager/alt-text/{id}` - Update alt text
- `GET /file-manager/download/{id}` - Download file

## Responsive Grid

The grid automatically adjusts columns based on viewport:

| Viewport | Columns |
|----------|---------|
| < 490px | 3 |
| 490-639px | 4 |
| 640-767px | 5 |
| 768-1279px | 7 |
| ≥ 1280px | 9 |

## Styling

The component uses Tailwind CSS classes matching the existing admin theme:
- `bg-background` - Dark background
- `border-border` - Border color
- `text-foreground` - Primary text
- `text-muted` - Secondary text
- `text-primary` - Accent color
- `text-theme-alert` - Danger/error color

All existing UI components are reused (Button, Input, SelectInput, Spinner).

## Keyboard Shortcuts

- `Escape` - Close modal or current sub-modal
- `Click` - Select/deselect file
- `Click + Dropdown` - Access file actions

## Browser Compatibility

Works in all modern browsers that support:
- React 18
- ES6+
- File API (drag & drop)
- Clipboard API (copy link)

## Migration from jQuery Media Manager

This React implementation replaces the jQuery-based `window.mediaManager` with a cleaner React approach. Benefits:

1. **No Global State**: Uses React hooks instead of `window.mediaManager`
2. **Type Safety**: Full TypeScript support
3. **Better Performance**: No DOM manipulation, React virtual DOM
4. **Modern API**: Uses existing axios instance with caching
5. **Reusable**: Hook-based architecture for custom implementations
6. **Cleaner Code**: ~1500 lines → Modular components

To migrate existing components:

```typescript
// Old way (jQuery)
const openMediaManager = () => {
  (window as any).currentFieldId = 'my-field';
  (window as any).MicroModal.show('media-manager-modal');
};

// New way (React)
const [isOpen, setIsOpen] = useState(false);

<MediaManagerModal
  isOpen={isOpen}
  onClose={() => setIsOpen(false)}
  onSelect={(files) => setSelectedIds(files.map(f => f.id))}
  multiple={true}
/>
```

## Future Enhancements

Possible future improvements:
- [ ] Infinite scroll (instead of pagination)
- [ ] File type filter (currently hidden)
- [ ] Folder/organization support
- [ ] Image editing (crop, rotate)
- [ ] Multi-select with Shift+Click
- [ ] Drag-to-reorder selection

## License

Part of the EpikCart project. See main LICENSE file.
