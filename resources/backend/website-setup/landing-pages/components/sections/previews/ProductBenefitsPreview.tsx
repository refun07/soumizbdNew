import React from 'react';

const ProductBenefitsPreview: React.FC = () => {
    return (
        <section className="py-10 sm:py-12 lg:py-16">
            <div className="container mx-auto px-4">
                <div className="max-w-4xl mx-auto p-5 sm:p-8 rounded-lg" style={{ backgroundColor: '#F5F5EC' }}>
                    <h2 className="text-3xl md:text-4xl font-bold text-center mb-8 text-gray-800 font-serif">Product Benefits</h2>
                    <div className="space-y-4 mb-8">
                        <div className="flex items-start gap-4">
                            <div className="flex-shrink-0 w-8 h-8 rounded-full flex items-start justify-center pt-2" style={{ backgroundColor: '#E68A3D' }}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </div>
                            <p className="text-gray-700 text-base sm:text-lg leading-relaxed">Premium quality materials that ensure long-lasting durability and performance.</p>
                        </div>
                        <div className="flex items-start gap-4">
                            <div className="flex-shrink-0 w-8 h-8 rounded-full flex items-start justify-center pt-2" style={{ backgroundColor: '#E68A3D' }}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </div>
                            <p className="text-gray-700 text-base sm:text-lg leading-relaxed">Innovative design that combines style with functionality for everyday use.</p>
                        </div>
                        <div className="flex items-start gap-4">
                            <div className="flex-shrink-0 w-8 h-8 rounded-full flex items-start justify-center pt-2" style={{ backgroundColor: '#E68A3D' }}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </div>
                            <p className="text-gray-700 text-base sm:text-lg leading-relaxed">Eco-friendly materials that are safe for you and the environment.</p>
                        </div>
                        <div className="flex items-start gap-4">
                            <div className="flex-shrink-0 w-8 h-8 rounded-full flex items-start justify-center pt-2" style={{ backgroundColor: '#E68A3D' }}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </div>
                            <p className="text-gray-700 text-base sm:text-lg leading-relaxed">Excellent customer support and warranty coverage for peace of mind.</p>
                        </div>
                        <div className="flex items-start gap-4">
                            <div className="flex-shrink-0 w-8 h-8 rounded-full flex items-start justify-center pt-2" style={{ backgroundColor: '#E68A3D' }}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </div>
                            <p className="text-gray-700 text-base sm:text-lg leading-relaxed">Fast and reliable shipping with easy returns policy.</p>
                        </div>
                        <div className="flex items-start gap-4">
                            <div className="flex-shrink-0 w-8 h-8 rounded-full flex items-start justify-center pt-2" style={{ backgroundColor: '#E68A3D' }}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </div>
                            <p className="text-gray-700 text-base sm:text-lg leading-relaxed">Competitive pricing that offers great value for money.</p>
                        </div>
                    </div>
                    <div className="flex justify-center">
                        <a href="#"
                           className="inline-flex items-center gap-2 px-8 py-4 font-bold uppercase tracking-wide hover:opacity-90 transition-opacity"
                           style={{ backgroundColor: '#E68A3D' }}
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="black" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                            </svg>
                            <span className="text-black">Shop Now</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    );
};

export default ProductBenefitsPreview;
