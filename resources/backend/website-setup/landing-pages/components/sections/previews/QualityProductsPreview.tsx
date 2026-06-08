import React from 'react';

const QualityProductsPreview: React.FC = () => {
    return (
        <section className="py-10 sm:py-12 lg:py-16 bg-gray-50">
            <div className="container mx-auto px-4">
                <div className="grid md:grid-cols-2 gap-12 items-center">
                    <div className="order-2 md:order-1">
                        <img
                            src="https://placehold.co/600x500/e8e8e8/333333?text=Product+Image"
                            alt="Product"
                            className="w-full h-auto rounded-lg shadow-lg"
                        />
                    </div>
                    <div className="order-1 md:order-2">
                        <h2 className="text-3xl md:text-4xl font-bold text-gray-900 mb-8">Why Choose Our Products</h2>
                        <div className="space-y-4 mb-8">
                            <div className="flex items-center gap-4 bg-[#F0F0E6] rounded-lg p-4">
                                <div className="flex-shrink-0 w-6 h-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#E68A3D" strokeWidth="2">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <span className="text-gray-700 font-medium">Premium Quality Materials</span>
                            </div>
                            <div className="flex items-center gap-4 bg-[#F0F0E6] rounded-lg p-4">
                                <div className="flex-shrink-0 w-6 h-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#E68A3D" strokeWidth="2">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <span className="text-gray-700 font-medium">100% Satisfaction Guaranteed</span>
                            </div>
                            <div className="flex items-center gap-4 bg-[#F0F0E6] rounded-lg p-4">
                                <div className="flex-shrink-0 w-6 h-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#E68A3D" strokeWidth="2">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <span className="text-gray-700 font-medium">Fast & Free Shipping</span>
                            </div>
                            <div className="flex items-center gap-4 bg-[#F0F0E6] rounded-lg p-4">
                                <div className="flex-shrink-0 w-6 h-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#E68A3D" strokeWidth="2">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <span className="text-gray-700 font-medium">24/7 Customer Support</span>
                            </div>
                        </div>
                        <a href="#"
                           className="inline-flex items-center gap-2 bg-[#E68A36] text-white px-6 py-3 font-semibold hover:bg-[#d67a2f] transition-colors"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                            </svg>
                            Shop Now
                        </a>
                    </div>
                </div>
            </div>
        </section>
    );
};

export default QualityProductsPreview;
