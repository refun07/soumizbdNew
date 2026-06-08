import React from 'react';

const ProductHighlightsPreview: React.FC = () => {
    return (
        <section className="py-10 sm:py-12 lg:py-16">
            <div className="container mx-auto px-4">
                <h2 className="text-3xl md:text-4xl font-bold text-center mb-12 text-gray-800 font-serif">Product Highlights</h2>
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 max-w-6xl mx-auto">
                    <div className="bg-[#2D5A3D] rounded-xl p-6 text-center">
                        <div className="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center" style={{ backgroundColor: '#4A7C59' }}>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            </svg>
                        </div>
                        <h3 className="text-white text-lg sm:text-xl font-bold mb-2">Premium Quality</h3>
                        <p className="text-white/80 text-sm leading-relaxed">Built with the finest materials for unmatched durability and performance.</p>
                    </div>
                    <div className="bg-[#2D5A3D] rounded-xl p-6 text-center">
                        <div className="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center" style={{ backgroundColor: '#4A7C59' }}>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                        <h3 className="text-white text-lg sm:text-xl font-bold mb-2">Fast Performance</h3>
                        <p className="text-white/80 text-sm leading-relaxed">Optimized for speed and efficiency to meet all your needs quickly.</p>
                    </div>
                    <div className="bg-[#2D5A3D] rounded-xl p-6 text-center">
                        <div className="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center" style={{ backgroundColor: '#4A7C59' }}>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                        </div>
                        <h3 className="text-white text-lg sm:text-xl font-bold mb-2">Secure & Safe</h3>
                        <p className="text-white/80 text-sm leading-relaxed">Advanced security features to keep your data and usage protected.</p>
                    </div>
                    <div className="bg-[#2D5A3D] rounded-xl p-6 text-center">
                        <div className="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center" style={{ backgroundColor: '#4A7C59' }}>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                <line x1="12" y1="22.08" x2="12" y2="12"></line>
                            </svg>
                        </div>
                        <h3 className="text-white text-lg sm:text-xl font-bold mb-2">Easy Integration</h3>
                        <p className="text-white/80 text-sm leading-relaxed">Seamlessly works with your existing setup without any hassle.</p>
                    </div>
                    <div className="bg-[#2D5A3D] rounded-xl p-6 text-center">
                        <div className="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center" style={{ backgroundColor: '#4A7C59' }}>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                            </svg>
                        </div>
                        <h3 className="text-white text-lg sm:text-xl font-bold mb-2">User Manual</h3>
                        <p className="text-white/80 text-sm leading-relaxed">Comprehensive documentation to help you get started easily.</p>
                    </div>
                    <div className="bg-[#2D5A3D] rounded-xl p-6 text-center">
                        <div className="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center" style={{ backgroundColor: '#4A7C59' }}>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                        <h3 className="text-white text-lg sm:text-xl font-bold mb-2">24/7 Support</h3>
                        <p className="text-white/80 text-sm leading-relaxed">Round the clock assistance whenever you need it.</p>
                    </div>
                </div>
                <div className="flex justify-center mt-12">
                    <a href="#"
                       className="inline-flex items-center gap-2 px-8 py-4 font-bold uppercase tracking-wide hover:opacity-90 transition-opacity"
                       style={{ backgroundColor: '#E68A3D' }}
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="black" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                            <circle cx="9" cy="21" r="1"></circle>
                            <circle cx="20" cy="21" r="1"></circle>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                        </svg>
                        <span className="text-black">Order Now</span>
                    </a>
                </div>
            </div>
        </section>
    );
};

export default ProductHighlightsPreview;
