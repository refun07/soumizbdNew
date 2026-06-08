import React, { useState } from 'react';

interface FormData {
    title: string;
    slug: string;
    status: 'draft' | 'published';
    meta_title: string;
    meta_description: string;
    meta_keywords: string;
    meta_image: string;
}

interface ApiResponse {
    message: string;
    errors?: Record<string, string[]>;
}

const LandingPageCreateApp: React.FC = () => {
    const [formData, setFormData] = useState<FormData>({
        title: '',
        slug: '',
        status: 'draft',
        meta_title: '',
        meta_description: '',
        meta_keywords: '',
        meta_image: '',
    });
    const [loading, setLoading] = useState(false);
    const [errors, setErrors] = useState<Record<string, string[]>>({});

    const getCsrfToken = (): string => {
        const token = document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement;
        return token?.content || '';
    };

    const handleInputChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) => {
        const { name, value } = e.target;
        setFormData(prev => ({
            ...prev,
            [name]: value
        }));

        if (errors[name]) {
            setErrors(prev => ({
                ...prev,
                [name]: []
            }));
        }
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();

        try {
            setLoading(true);
            setErrors({});

            const response = await fetch('/admin/landing-pages', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': getCsrfToken(),
                },
                body: JSON.stringify(formData),
            });

            const data: ApiResponse = await response.json();

            if (!response.ok) {
                if (data.errors) {
                    setErrors(data.errors);
                } else {
                    throw new Error(data.message || 'Failed to create landing page');
                }
                return;
            }

            window.location.href = '/admin/landing-pages';
        } catch (err) {
            console.error('Error creating landing page:', err);
            setErrors({
                general: [err instanceof Error ? err.message : 'An error occurred while creating the landing page']
            });
        } finally {
            setLoading(false);
        }
    };

    const generateSlug = (title: string) => {
        return title
            .toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim();
    };

    const handleTitleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const title = e.target.value;
        handleInputChange(e);

        if (!formData.slug || formData.slug === generateSlug(formData.title)) {
            setFormData(prev => ({
                ...prev,
                title: title,
                slug: generateSlug(title)
            }));
        }
    };

    return (
        <>
            {/* Header */}
            <div className="dashboard-nav pt-6 flex items-center justify-between mb-9">
                <div className="flex items-center">
                    <span className="text-xl mr-3 text-secondary">
                        <i className="fa-regular fa-file-lines"></i>
                    </span>
                    <span className="text-sm sm:text-base font-bold">
                        Create Landing Page
                    </span>
                </div>

                <div className="max-sm:hidden flex items-center gap-2">
                    <a href="/admin/dashboard" className="font-bold">Dashboard</a>
                    <span className="text-primary dark:text-muted">
                        <i className="fa-solid fa-chevron-right"></i>
                    </span>
                    <a href="/admin/landing-pages" className="font-bold">Landing Pages</a>
                    <span className="text-primary dark:text-muted">
                        <i className="fa-solid fa-chevron-right"></i>
                    </span>
                    <p className="text-muted">Create</p>
                </div>
            </div>

            {/* Form */}
            <div className="grid xl:grid-cols-12">
                <div className="xl:col-span-10">
                    <div className="card">
                        <div className="card__content">
                            <form onSubmit={handleSubmit}>
                                {errors.general && (
                                    <div className="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded mb-6">
                                        {errors.general[0]}
                                    </div>
                                )}

                                <div className="space-y-3">
                                    {/* Title */}
                                    <div className="w-full">
                                        <label htmlFor="title" className="theme-input-label pt-0 input-required">
                                            Title
                                        </label>
                                        <div className="theme-input-wrapper flex gap-3">
                                            <input
                                                type="text"
                                                id="title"
                                                name="title"
                                                value={formData.title}
                                                onChange={handleTitleChange}
                                                className="theme-input"
                                                required
                                                placeholder="Type landing page title"
                                            />
                                        </div>
                                        {errors.title && (
                                            <div className="text-red-500 text-sm mt-1">{errors.title[0]}</div>
                                        )}
                                    </div>

                                    {/* Slug */}
                                    <div className="theme-input-group w-full">
                                        <label htmlFor="slug" className="theme-input-label pt-0 input-required">
                                            Slug
                                        </label>
                                        <div className="theme-input-wrapper flex">
                                            <div className="border rounded-md !rounded-e-none !border-e-0 p-2 text-sm bg-gray-50 dark:bg-gray-800">
                                                {window.location.origin}/landing/
                                            </div>
                                            <input
                                                type="text"
                                                id="slug"
                                                name="slug"
                                                value={formData.slug}
                                                onChange={handleInputChange}
                                                className="theme-input !rounded-s-none"
                                                required
                                                placeholder="Type URL slug"
                                            />
                                        </div>
                                        {errors.slug && (
                                            <div className="text-red-500 text-sm mt-1">{errors.slug[0]}</div>
                                        )}
                                    </div>

                                    {/* Status */}
                                    <div className="w-full">
                                        <label htmlFor="status" className="theme-input-label pt-0 input-required">
                                            Status
                                        </label>
                                        <div className="theme-input-wrapper flex gap-3">
                                            <select
                                                id="status"
                                                name="status"
                                                value={formData.status}
                                                onChange={handleInputChange}
                                                className="theme-input"
                                                required
                                            >
                                                <option value="draft">Draft</option>
                                                <option value="published">Published</option>
                                            </select>
                                        </div>
                                        {errors.status && (
                                            <div className="text-red-500 text-sm mt-1">{errors.status[0]}</div>
                                        )}
                                    </div>

                                    {/* Meta Title */}
                                    <div className="w-full">
                                        <label htmlFor="meta_title" className="theme-input-label pt-0">
                                            Meta Title
                                        </label>
                                        <div className="theme-input-wrapper flex gap-3">
                                            <input
                                                type="text"
                                                id="meta_title"
                                                name="meta_title"
                                                value={formData.meta_title}
                                                onChange={handleInputChange}
                                                className="theme-input"
                                                placeholder="Type meta title"
                                            />
                                        </div>
                                        {errors.meta_title && (
                                            <div className="text-red-500 text-sm mt-1">{errors.meta_title[0]}</div>
                                        )}
                                    </div>

                                    {/* Meta Description */}
                                    <div className="w-full">
                                        <label htmlFor="meta_description" className="theme-input-label pt-0">
                                            Meta Description
                                        </label>
                                        <div className="theme-input-wrapper flex gap-3">
                                            <textarea
                                                id="meta_description"
                                                name="meta_description"
                                                value={formData.meta_description}
                                                onChange={handleInputChange}
                                                className="theme-input"
                                                rows={3}
                                                placeholder="Type meta description"
                                            />
                                        </div>
                                        {errors.meta_description && (
                                            <div className="text-red-500 text-sm mt-1">{errors.meta_description[0]}</div>
                                        )}
                                    </div>

                                    {/* Meta Keywords */}
                                    <div className="w-full">
                                        <label htmlFor="meta_keywords" className="theme-input-label pt-0">
                                            Meta Keywords
                                        </label>
                                        <div className="theme-input-wrapper flex gap-3">
                                            <input
                                                type="text"
                                                id="meta_keywords"
                                                name="meta_keywords"
                                                value={formData.meta_keywords}
                                                onChange={handleInputChange}
                                                className="theme-input"
                                                placeholder="Type comma separated keywords. e.g. keyword1, keyword2"
                                            />
                                        </div>
                                        {errors.meta_keywords && (
                                            <div className="text-red-500 text-sm mt-1">{errors.meta_keywords[0]}</div>
                                        )}
                                    </div>

                                    {/* Submit Button */}
                                    <div className="flex justify-end">
                                        <button
                                            type="submit"
                                            className="btn btn-primary"
                                            disabled={loading}
                                        >
                                            {loading ? (
                                                <>
                                                    <i className="fa-solid fa-spinner fa-spin mr-2"></i>
                                                    Creating...
                                                </>
                                            ) : (
                                                'Save Landing Page'
                                            )}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
};

export default LandingPageCreateApp;
