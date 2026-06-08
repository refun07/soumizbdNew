import React from 'react';

const FeaturedProductsPreview: React.FC = () => {
    return (
        <section className="py-10 sm:py-12 lg:py-16">
            <div className="container mx-auto px-4">
                <h2 className="text-3xl md:text-4xl font-bold text-center mb-12 text-gray-800 font-serif">Featured Products</h2>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl mx-auto">
                    <div className="group bg-white rounded-lg overflow-hidden hover:ring-2 hover:ring-blue-500 transition-all duration-300">
                        <div className="aspect-[4/3] lg:aspect-[16/10] relative overflow-hidden">
                            <img
                                src="https://placehold.co/600x400/2D5A3D/ffffff?text=Featured+Product+1"
                                alt="Product 1"
                                className="w-full h-full object-cover"
                            />
                        </div>
                        <div className="p-6 text-center">
                            <h3 className="text-xl sm:text-2xl font-bold text-gray-800 mb-3">Premium Product One</h3>
                            <p className="text-gray-600 mb-6">
                                Experience excellence with our top-rated product featuring innovative design and superior quality.
                            </p>
                            <button
                                className="inline-flex items-center gap-2 px-6 py-3 font-bold uppercase tracking-wide hover:opacity-90 transition-opacity text-xs"
                                style={{ backgroundColor: '#E68A3D' }}
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="black" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                    <circle cx="9" cy="21" r="1"></circle>
                                    <circle cx="20" cy="21" r="1"></circle>
                                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                </svg>
                                <span className="text-black">Add to Cart</span>
                            </button>
                        </div>
                    </div>
                    <div className="group bg-white rounded-lg overflow-hidden hover:ring-2 hover:ring-blue-500 transition-all duration-300">
                        <div className="aspect-[4/3] lg:aspect-[16/10] relative overflow-hidden">
                            <img
                                src="https://placehold.co/600x400/2D5A3D/ffffff?text=Featured+Product+2"
                                alt="Product 2"
                                className="w-full h-full object-cover"
                            />
                        </div>
                        <div className="p-6 text-center">
                            <h3 className="text-xl sm:text-2xl font-bold text-gray-800 mb-3">Premium Product Two</h3>
                            <p className="text-gray-600 mb-6">
                                Discover the difference with our exceptional product designed to exceed your expectations.
                            </p>
                            <button
                                className="inline-flex items-center gap-2 px-6 py-3 font-bold uppercase tracking-wide hover:opacity-90 transition-opacity text-xs"
                                style={{ backgroundColor: '#E68A3D' }}
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="black" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                    <circle cx="9" cy="21" r="1"></circle>
                                    <circle cx="20" cy="21" r="1"></circle>
                                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                </svg>
                                <span className="text-black">Add to Cart</span>
                            </button>
                        </div>
                    </div>
                    <div className="group bg-white rounded-lg overflow-hidden hover:ring-2 hover:ring-blue-500 transition-all duration-300">
                        <div className="aspect-[4/3] lg:aspect-[16/10] relative overflow-hidden">
                            <img
                                src="https://placehold.co/600x400/2D5A3D/ffffff?text=Featured+Product+3"
                                alt="Product 3"
                                className="w-full h-full object-cover"
                            />
                        </div>
                        <div className="p-6 text-center">
                            <h3 className="text-xl sm:text-2xl font-bold text-gray-800 mb-3">Premium Product Three</h3>
                            <p className="text-gray-600 mb-6">
                                Unmatched performance and style in one amazing package. Perfect for everyday use.
                            </p>
                            <button
                                className="inline-flex items-center gap-2 px-6 py-3 font-bold uppercase tracking-wide hover:opacity-90 transition-opacity text-xs"
                                style={{ backgroundColor: '#E68A3D' }}
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="black" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                    <circle cx="9" cy="21" r="1"></circle>
                                    <circle cx="20" cy="21" r="1"></circle>
                                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                </svg>
                                <span className="text-black">Add to Cart</span>
                            </button>
                        </div>
                    </div>
                    <div className="group bg-white rounded-lg overflow-hidden hover:ring-2 hover:ring-blue-500 transition-all duration-300">
                        <div className="aspect-[4/3] lg:aspect-[16/10] relative overflow-hidden">
                            <img
                                src="https://placehold.co/600x400/2D5A3D/ffffff?text=Featured+Product+4"
                                alt="Product 4"
                                className="w-full h-full object-cover"
                            />
                        </div>
                        <div className="p-6 text-center">
                            <h3 className="text-xl sm:text-2xl font-bold text-gray-800 mb-3">Premium Product Four</h3>
                            <p className="text-gray-600 mb-6">
                                The ultimate choice for those who demand the best. Quality you can trust.
                            </p>
                            <button
                                className="inline-flex items-center gap-2 px-6 py-3 font-bold uppercase tracking-wide hover:opacity-90 transition-opacity text-xs"
                                style={{ backgroundColor: '#E68A3D' }}
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="black" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                    <circle cx="9" cy="21" r="1"></circle>
                                    <circle cx="20" cy="21" r="1"></circle>
                                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                </svg>
                                <span className="text-black">Add to Cart</span>
                            </button>
                        </div>
                    </div>
                    <div className="group bg-white rounded-lg overflow-hidden hover:ring-2 hover:ring-blue-500 transition-all duration-300">
                        <div className="aspect-[4/3] lg:aspect-[16/10] relative overflow-hidden">
                            <img
                                src="https://placehold.co/600x400/2D5A3D/ffffff?text=Featured+Product+5"
                                alt="Product 5"
                                className="w-full h-full object-cover"
                            />
                        </div>
                        <div className="p-6 text-center">
                            <h3 className="text-xl sm:text-2xl font-bold text-gray-800 mb-3">Premium Product Five</h3>
                            <p className="text-gray-600 mb-6">
                                Innovative features meets modern design. A perfect addition to your collection.
                            </p>
                            <button
                                className="inline-flex items-center gap-2 px-6 py-3 font-bold uppercase tracking-wide hover:opacity-90 transition-opacity text-xs"
                                style={{ backgroundColor: '#E68A3D' }}
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="black" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                    <circle cx="9" cy="21" r="1"></circle>
                                    <circle cx="20" cy="21" r="1"></circle>
                                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                </svg>
                                <span className="text-black">Add to Cart</span>
                            </button>
                        </div>
                    </div>
                    <div className="group bg-white rounded-lg overflow-hidden hover:ring-2 hover:ring-blue-500 transition-all duration-300">
                        <div className="aspect-[4/3] lg:aspect-[16/10] relative overflow-hidden">
                            <img
                                src="https://placehold.co/600x400/2D5A3D/ffffff?text=Featured+Product+6"
                                alt="Product 6"
                                className="w-full h-full object-cover"
                            />
                        </div>
                        <div className="p-6 text-center">
                            <h3 className="text-xl sm:text-2xl font-bold text-gray-800 mb-3">Premium Product Six</h3>
                            <p className="text-gray-600 mb-6">
                                Stand out from the crowd with this uniquely designed product. Limited edition.
                            </p>
                            <button
                                className="inline-flex items-center gap-2 px-6 py-3 font-bold uppercase tracking-wide hover:opacity-90 transition-opacity text-xs"
                                style={{ backgroundColor: '#E68A3D' }}
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="black" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                    <circle cx="9" cy="21" r="1"></circle>
                                    <circle cx="20" cy="21" r="1"></circle>
                                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                </svg>
                                <span className="text-black">Add to Cart</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
};

export default FeaturedProductsPreview;
