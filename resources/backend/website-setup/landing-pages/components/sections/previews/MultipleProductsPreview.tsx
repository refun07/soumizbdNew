import React from 'react';

const MultipleProductsPreview: React.FC = () => {
    return (
        <section className="py-10 sm:py-12 lg:py-16">
            <div className="container mx-auto px-4">
                <div className="grid md:grid-cols-3 gap-8">
                    <div className="group">
                        <div className="relative mb-4 overflow-hidden rounded-lg">
                            <img
                                src="https://placehold.co/400x400/4A5D23/ffffff?text=Product+1"
                                alt="Product 1"
                                className="w-full h-auto object-cover transition-transform duration-300 group-hover:scale-105"
                            />
                        </div>
                        <h3 className="text-xl font-semibold text-center mb-2" style={{ color: '#4A5D23' }}>Premium Product One</h3>
                        <p className="text-gray-500 text-center text-sm mb-4">High quality item with amazing features</p>
                        <div className="flex justify-center">
                            <a href="#"
                               className="inline-flex items-center gap-2 px-6 py-3 font-semibold hover:opacity-90 transition-opacity rounded-md"
                               style={{ backgroundColor: '#E68A36' }}
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                    <circle cx="9" cy="21" r="1"></circle>
                                    <circle cx="20" cy="21" r="1"></circle>
                                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                </svg>
                                <span className="text-black">Add to Cart</span>
                            </a>
                        </div>
                    </div>
                    <div className="group">
                        <div className="relative mb-4 overflow-hidden rounded-lg">
                            <img
                                src="https://placehold.co/400x400/4A5D23/ffffff?text=Product+2"
                                alt="Product 2"
                                className="w-full h-auto object-cover transition-transform duration-300 group-hover:scale-105"
                            />
                        </div>
                        <h3 className="text-xl font-semibold text-center mb-2" style={{ color: '#4A5D23' }}>Premium Product Two</h3>
                        <p className="text-gray-500 text-center text-sm mb-4">Another fantastic product choice</p>
                        <div className="flex justify-center">
                            <a href="#"
                               className="inline-flex items-center gap-2 px-6 py-3 font-semibold hover:opacity-90 transition-opacity rounded-md"
                               style={{ backgroundColor: '#E68A36' }}
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                    <circle cx="9" cy="21" r="1"></circle>
                                    <circle cx="20" cy="21" r="1"></circle>
                                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                </svg>
                                <span className="text-black">Add to Cart</span>
                            </a>
                        </div>
                    </div>
                    <div className="group">
                        <div className="relative mb-4 overflow-hidden rounded-lg">
                            <img
                                src="https://placehold.co/400x400/4A5D23/ffffff?text=Product+3"
                                alt="Product 3"
                                className="w-full h-auto object-cover transition-transform duration-300 group-hover:scale-105"
                            />
                        </div>
                        <h3 className="text-xl font-semibold text-center mb-2" style={{ color: '#4A5D23' }}>Premium Product Three</h3>
                        <p className="text-gray-500 text-center text-sm mb-4">The best choice for you</p>
                        <div className="flex justify-center">
                            <a href="#"
                               className="inline-flex items-center gap-2 px-6 py-3 font-semibold hover:opacity-90 transition-opacity rounded-md"
                               style={{ backgroundColor: '#E68A36' }}
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                    <circle cx="9" cy="21" r="1"></circle>
                                    <circle cx="20" cy="21" r="1"></circle>
                                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                </svg>
                                <span className="text-black">Add to Cart</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
};

export default MultipleProductsPreview;
