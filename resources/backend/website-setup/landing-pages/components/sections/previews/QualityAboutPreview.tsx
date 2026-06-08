import React from 'react';

const QualityAboutPreview: React.FC = () => {
    return (
        <section className="py-10 sm:py-12 lg:py-16" style={{ backgroundColor: '#F9F9F9' }}>
            <div className="container mx-auto px-4">
                <div className="grid md:grid-cols-2 gap-12 items-center">
                    <div className="order-2 md:order-1">
                        <img
                            src="https://placehold.co/600x500/2E4518/ffffff?text=About+Image"
                            alt="About Us"
                            className="w-full h-auto rounded-lg shadow-lg"
                        />
                    </div>
                    <div className="order-1 md:order-2">
                        <h2 className="text-3xl md:text-4xl font-bold text-gray-900 mb-6">About Our Company</h2>
                        <p className="text-gray-600 text-base sm:text-lg leading-relaxed mb-8">
                            We are dedicated to providing the highest quality products and services to our customers.
                            With years of experience and a passion for excellence, we continue to innovate and
                            deliver outstanding solutions that make a real difference in people's lives.
                        </p>
                        <a href="#"
                           className="inline-flex items-center gap-2 px-6 py-3 font-semibold hover:opacity-90 transition-opacity"
                           style={{ backgroundColor: '#E68A36' }}
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                            </svg>
                            <span className="text-black">Learn More</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    );
};

export default QualityAboutPreview;
