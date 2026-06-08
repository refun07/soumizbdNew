import React from 'react';

const FaqSectionPreview: React.FC = () => {
    return (
        <section className="py-10 sm:py-12 lg:py-16" style={{ backgroundColor: '#F5F2E8' }}>
            <div className="container mx-auto px-4 max-w-4xl">
                <h2 className="text-3xl md:text-4xl font-bold text-center mb-12 font-serif" style={{ color: '#2D5A3D' }}>Frequently Asked Questions</h2>
                <div className="space-y-4">
                    <div className="faq-item rounded-lg overflow-hidden">
                        <button
                            type="button"
                            className="w-full text-left p-4 flex items-center gap-4"
                            style={{ backgroundColor: '#2D5A3D' }}
                        >
                            <span className="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center" style={{ backgroundColor: '#E68A3D' }}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </span>
                            <span className="flex-1 font-semibold text-base sm:text-lg text-white">What is your return policy?</span>
                            <span className="text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className="transform rotate-180">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </span>
                        </button>
                        <div className="faq-content overflow-hidden" style={{ maxHeight: '200px' }}>
                            <div className="p-6" style={{ backgroundColor: 'white' }}>
                                <p className="leading-relaxed text-gray-700">
                                    We offer a 30-day return policy for all unused items in their original packaging.
                                    Simply contact our support team to initiate a return, and we'll provide you with
                                    a prepaid shipping label.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div className="faq-item rounded-lg overflow-hidden">
                        <button
                            type="button"
                            className="w-full text-left p-4 flex items-center gap-4"
                            style={{ backgroundColor: '#E8F0D8' }}
                        >
                            <span className="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center" style={{ backgroundColor: '#E68A3D' }}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </span>
                            <span className="flex-1 font-semibold text-base sm:text-lg" style={{ color: '#3D4A3A' }}>How long does shipping take?</span>
                            <span style={{ color: '#3D4A3A' }}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </span>
                        </button>
                    </div>
                    <div className="faq-item rounded-lg overflow-hidden">
                        <button
                            type="button"
                            className="w-full text-left p-4 flex items-center gap-4"
                            style={{ backgroundColor: '#E8F0D8' }}
                        >
                            <span className="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center" style={{ backgroundColor: '#E68A3D' }}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </span>
                            <span className="flex-1 font-semibold text-base sm:text-lg" style={{ color: '#3D4A3A' }}>Do you offer international shipping?</span>
                            <span style={{ color: '#3D4A3A' }}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </span>
                        </button>
                    </div>
                    <div className="faq-item rounded-lg overflow-hidden">
                        <button
                            type="button"
                            className="w-full text-left p-4 flex items-center gap-4"
                            style={{ backgroundColor: '#E8F0D8' }}
                        >
                            <span className="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center" style={{ backgroundColor: '#E68A3D' }}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </span>
                            <span className="flex-1 font-semibold text-base sm:text-lg" style={{ color: '#3D4A3A' }}>What payment methods do you accept?</span>
                            <span style={{ color: '#3D4A3A' }}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </span>
                        </button>
                    </div>
                    <div className="faq-item rounded-lg overflow-hidden">
                        <button
                            type="button"
                            className="w-full text-left p-4 flex items-center gap-4"
                            style={{ backgroundColor: '#E8F0D8' }}
                        >
                            <span className="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center" style={{ backgroundColor: '#E68A3D' }}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </span>
                            <span className="flex-1 font-semibold text-base sm:text-lg" style={{ color: '#3D4A3A' }}>How can I track my order?</span>
                            <span style={{ color: '#3D4A3A' }}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </span>
                        </button>
                    </div>
                    <div className="faq-item rounded-lg overflow-hidden">
                        <button
                            type="button"
                            className="w-full text-left p-4 flex items-center gap-4"
                            style={{ backgroundColor: '#E8F0D8' }}
                        >
                            <span className="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center" style={{ backgroundColor: '#E68A3D' }}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </span>
                            <span className="flex-1 font-semibold text-base sm:text-lg" style={{ color: '#3D4A3A' }}>Is there a warranty on your products?</span>
                            <span style={{ color: '#3D4A3A' }}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </span>
                        </button>
                    </div>
                </div>
                <div className="flex justify-center mt-12">
                    <a href="#"
                       className="inline-flex items-center gap-2 px-8 py-4 font-bold uppercase tracking-wide hover:opacity-90 transition-opacity rounded-lg"
                       style={{ backgroundColor: '#E68A3D' }}
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="black" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                            <circle cx="9" cy="21" r="1"></circle>
                            <circle cx="20" cy="21" r="1"></circle>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                        </svg>
                        <span className="text-black">Contact Us</span>
                    </a>
                </div>
            </div>
        </section>
    );
};

export default FaqSectionPreview;
