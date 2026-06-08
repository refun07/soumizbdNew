import React, { useState, useEffect } from 'react';
import Button from '../../../react/components/inputs/Button';

interface LandingPage {
    id: number;
    title: string;
    slug: string;
    status: 'draft' | 'published';
    sections_count: number;
    created_at: string;
    updated_at: string;
}

interface ApiResponse {
    data: LandingPage[];
    message: string;
}

interface FormData {
    title: string;
    slug: string;
    status: 'draft' | 'published';
    meta_title: string;
    meta_description: string;
    meta_keywords: string;
}

declare global {
    interface Window {
        flash?: (message: string, type: string) => void;
    }
}

const LandingPageListingApp: React.FC = () => {
    const [landingPages, setLandingPages] = useState<LandingPage[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const [showModal, setShowModal] = useState(false);
    const [formLoading, setFormLoading] = useState(false);
    const [formErrors, setFormErrors] = useState<Record<string, string[]>>({});
    const [formData, setFormData] = useState<FormData>({
        title: '',
        slug: '',
        status: 'draft',
        meta_title: '',
        meta_description: '',
        meta_keywords: '',
    });

    useEffect(() => {
        fetchLandingPages();
    }, []);

    const fetchLandingPages = async () => {
        try {
            setLoading(true);
            const response = await fetch('/admin/api/landing-pages', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error('Failed to fetch landing pages');
            }

            const data = await response.json();
            setLandingPages(data.data || []);
        } catch (err) {
            setError(err instanceof Error ? err.message : 'An error occurred');
        } finally {
            setLoading(false);
        }
    };

    const handleDelete = async (id: number) => {
        if (!confirm('Are you sure you want to delete this landing page?')) {
            return;
        }

        try {
            const response = await fetch(`/admin/api/landing-pages/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error('Failed to delete landing page');
            }

            setLandingPages(prev => prev.filter(page => page.id !== id));

            if (window.flash) {
                window.flash('Landing page has been deleted successfully', 'success');
            }
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Failed to delete landing page');
            if (window.flash) {
                window.flash('Failed to delete landing page', 'error');
            }
        }
    };

    const handleStatusChange = async (id: number, newStatus: 'draft' | 'published') => {
        try {
            const response = await fetch(`/admin/api/landing-pages/${id}/status`, {
                method: 'PUT',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ status: newStatus }),
            });

            if (!response.ok) {
                throw new Error('Failed to update status');
            }

            setLandingPages(prev =>
                prev.map(page => page.id === id ? { ...page, status: newStatus } : page)
            );

            if (window.flash) {
                window.flash('Status updated successfully', 'success');
            }
        } catch (err) {
            if (window.flash) {
                window.flash('Failed to update status', 'error');
            }
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

    const handleInputChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) => {
        const { name, value } = e.target;
        setFormData(prev => ({ ...prev, [name]: value }));

        if (formErrors[name]) {
            setFormErrors(prev => ({ ...prev, [name]: [] }));
        }
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

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();

        try {
            setFormLoading(true);
            setFormErrors({});

            const response = await fetch('/admin/api/landing-pages', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(formData),
            });

            const data = await response.json();

            if (!response.ok) {
                if (data.errors) {
                    setFormErrors(data.errors);
                } else {
                    throw new Error(data.message || 'Failed to create landing page');
                }
                return;
            }

            // Reset form and close modal
            setFormData({
                title: '',
                slug: '',
                status: 'draft',
                meta_title: '',
                meta_description: '',
                meta_keywords: '',
            });
            setShowModal(false);

            // Refresh the list
            fetchLandingPages();

            if (window.flash) {
                window.flash('Landing page has been created successfully', 'success');
            }
        } catch (err) {
            console.error('Error creating landing page:', err);
            setFormErrors({
                general: [err instanceof Error ? err.message : 'An error occurred while creating the landing page']
            });
        } finally {
            setFormLoading(false);
        }
    };

    const openModal = () => {
        setFormData({
            title: '',
            slug: '',
            status: 'draft',
            meta_title: '',
            meta_description: '',
            meta_keywords: '',
        });
        setFormErrors({});
        setShowModal(true);
    };

    const closeModal = () => {
        setShowModal(false);
        setFormErrors({});
    };

    const getStatusBadge = (status: string) => {
        const baseClasses = 'inline-block px-2 py-1 rounded text-xs font-medium';
        return status === 'published'
            ? `${baseClasses} bg-green-500 text-white`
            : `${baseClasses} bg-yellow-500 text-gray-900`;
    };

    if (loading) {
        return (
            <div className="flex justify-center items-center h-64">
                <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
            </div>
        );
    }

    if (error) {
        return (
            <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                {error}
            </div>
        );
    }

    return (
        <div className="landing-page-listing">
            {/* Header */}
            <div className="dashboard-nav pt-6 flex items-center justify-between mb-9">
                <div className="flex items-center">
                    <span className="text-xl mr-3 text-secondary">
                        <i className="fa-regular fa-file-lines"></i>
                    </span>
                    <span className="text-sm sm:text-base font-bold">
                        Landing Pages
                    </span>
                </div>

                <div className="max-sm:hidden flex items-center gap-2">
                    <a href="/admin/dashboard" className="font-bold">Dashboard</a>
                    <span className="text-primary dark:text-muted">
                        <i className="fa-solid fa-chevron-right"></i>
                    </span>
                    <p className="text-muted">Landing Pages</p>
                </div>
            </div>

            {/* Table */}
            <div className="grid">
                <div className="card">
                    <div className="theme-table">
                        <div className="card__title border-none theme-table__filter flex flex-col md:flex-row xl:items-center justify-between gap-3">
                            <button
                                type="button"
                                onClick={openModal}
                                className="button button--primary"
                            >
                                <span className="text-xl">
                                    <i className="fal fa-plus"></i>
                                </span>
                                Add New
                            </button>
                        </div>

                        <div>
                            <table className="product-list-table w-full">
                                <thead className="uppercase text-left bg-primary/10 px-3">
                                    <tr>
                                        <th className="py-4 px-3">#</th>
                                        <th className="py-4 px-3">Title</th>
                                        <th className="py-4 px-3">Slug</th>
                                        <th className="py-4 px-3">Status</th>
                                        <th className="py-4 px-3">Sections</th>
                                        <th className="py-4 px-3 w-[130px]">Options</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {landingPages.map((page, index) => (
                                        <tr key={page.id}>
                                            <td className="py-4 px-3">{index + 1}</td>
                                            <td className="py-4 px-3">{page.title}</td>
                                            <td className="py-4 px-3">
                                                <div className="line-clamp-2">
                                                    /landing/{page.slug}
                                                </div>
                                            </td>
                                            <td className="py-4 px-3">
                                                <span className={getStatusBadge(page.status)}>
                                                    {page.status.charAt(0).toUpperCase() + page.status.slice(1)}
                                                </span>
                                            </td>
                                            <td className="py-4 px-3">{page.sections_count}</td>
                                            <td className="py-4 px-3">
                                                <div className="option-dropdown" tabIndex={0}>
                                                    <div className="option-dropdown__toggler bg-secondary/10 text-secondary">
                                                        <span>Actions</span>
                                                    </div>

                                                    <div className="option-dropdown__options">
                                                    <ul>
                                                        <li>
                                                            <a
                                                                href={`/admin/landing-pages/configure/${page.id}?lang_key=en-US&translate`}
                                                                className="option-dropdown__option"
                                                            >
                                                                Configure
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a
                                                                href={`/landing/${page.slug}`}
                                                                target="_blank"
                                                                rel="noopener noreferrer"
                                                                className="option-dropdown__option"
                                                            >
                                                                View Page
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <button
                                                                type="button"
                                                                onClick={() => handleStatusChange(
                                                                    page.id,
                                                                    page.status === 'draft' ? 'published' : 'draft'
                                                                )}
                                                                className="option-dropdown__option w-full text-left"
                                                            >
                                                                {page.status === 'draft' ? 'Publish' : 'Unpublish'}
                                                            </button>
                                                        </li>
                                                        <li>
                                                            <button
                                                                type="button"
                                                                onClick={() => handleDelete(page.id)}
                                                                className="option-dropdown__option text-red-600 hover:text-red-800 w-full text-left"
                                                            >
                                                                Delete
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </div>
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>

                            {landingPages.length === 0 && (
                                <div className="text-center py-8 text-muted">
                                    No landing pages found.
                                    <button
                                        type="button"
                                        onClick={openModal}
                                        className="text-primary ml-2"
                                    >
                                        Create your first landing page
                                    </button>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>

            {/* Create Modal */}
            <div
                className={`theme-modal theme-modal--lg ${showModal ? 'active' : 'pointer-events-none'}`}
                aria-hidden={!showModal}
            >
                <div className="theme-modal__inner">
                    <div className="theme-modal__header">
                        <h3 className="theme-modal__title">Create Landing Page</h3>
                        <button
                            type="button"
                            className="theme-modal__close"
                            onClick={closeModal}
                        >
                            <i className="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <form onSubmit={handleSubmit}>
                        <div className="theme-modal__body">
                            {formErrors.general && (
                                <div className="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded mb-4">
                                    {formErrors.general[0]}
                                </div>
                            )}

                            <div className="space-y-4">
                                {/* Title */}
                                <div className="w-full">
                                    <label htmlFor="title" className="theme-input-label pt-0 input-required">
                                        Title
                                    </label>
                                    <div className="theme-input-wrapper">
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
                                    {formErrors.title && (
                                        <div className="text-red-500 text-sm mt-1">{formErrors.title[0]}</div>
                                    )}
                                </div>

                                {/* Slug */}
                                <div className="w-full">
                                    <label htmlFor="slug" className="theme-input-label pt-0 input-required">
                                        Slug
                                    </label>
                                    <div className="theme-input-wrapper flex">
                                        <div className="border rounded-md !rounded-e-none !border-e-0 p-2 text-sm bg-gray-50 dark:bg-gray-800 whitespace-nowrap">
                                            /landing/
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
                                    {formErrors.slug && (
                                        <div className="text-red-500 text-sm mt-1">{formErrors.slug[0]}</div>
                                    )}
                                </div>

                                {/* Status */}
                                <div className="w-full">
                                    <label htmlFor="status" className="theme-input-label pt-0 input-required">
                                        Status
                                    </label>
                                    <div className="theme-input-wrapper">
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
                                    {formErrors.status && (
                                        <div className="text-red-500 text-sm mt-1">{formErrors.status[0]}</div>
                                    )}
                                </div>

                                {/* Meta Title */}
                                <div className="w-full">
                                    <label htmlFor="meta_title" className="theme-input-label pt-0">
                                        Meta Title
                                    </label>
                                    <div className="theme-input-wrapper">
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
                                </div>

                                {/* Meta Description */}
                                <div className="w-full">
                                    <label htmlFor="meta_description" className="theme-input-label pt-0">
                                        Meta Description
                                    </label>
                                    <div className="theme-input-wrapper">
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
                                </div>

                                {/* Meta Keywords */}
                                <div className="w-full">
                                    <label htmlFor="meta_keywords" className="theme-input-label pt-0">
                                        Meta Keywords
                                    </label>
                                    <div className="theme-input-wrapper">
                                        <input
                                            type="text"
                                            id="meta_keywords"
                                            name="meta_keywords"
                                            value={formData.meta_keywords}
                                            onChange={handleInputChange}
                                            className="theme-input"
                                            placeholder="Type comma separated keywords"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="theme-modal__footer">
                            <Button variant="secondary" onClick={closeModal}>
                                Cancel
                            </Button>
                            <span className="w-4"></span>
                            <Button type="submit" variant="primary" isLoading={formLoading}>
                                Save Landing Page
                            </Button>
                        </div>
                    </form>
                </div>
            </div>

            {/* Overlay */}
            <div
                className={`fixed inset-0 bg-black/60 z-[4] transition-all duration-300 ${showModal ? 'opacity-100 visible' : 'opacity-0 invisible'}`}
                onClick={closeModal}
            />
        </div>
    );
};

export default LandingPageListingApp;
