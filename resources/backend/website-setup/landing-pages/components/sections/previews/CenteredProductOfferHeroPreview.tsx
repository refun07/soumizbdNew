import React from 'react';

const CenteredProductOfferHeroPreview: React.FC = () => {
    return (
        <section
            className="w-full min-h-[400px] md:min-h-[500px] lg:min-h-screen flex flex-col items-center justify-center py-8 md:py-12 lg:py-16"
            style={{ backgroundColor: '#1a472a' }}
        >
            <div className="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col items-center">
                <div className="mb-4 md:mb-6">
                    <img
                        src="https://placehold.co/100x50/ffffff/1a472a?text=LOGO"
                        alt="Brand Logo"
                        className="h-8 md:h-10 lg:h-12 w-auto object-contain"
                    />
                </div>
                <h1 className="text-white text-3xl md:text-4xl lg:text-5xl font-bold text-center mb-2 md:mb-4">
                    Premium Product Name
                </h1>
                <p className="text-white/60 text-base md:text-lg text-center mb-4 md:mb-6 max-w-md mx-auto">
                    Best quality guaranteed with premium materials and
                    exceptional craftsmanship
                </p>
                <div className="w-full mb-4 md:mb-6">
                    <div className="aspect-video rounded-xl md:rounded-2xl overflow-hidden bg-[#2d5a3d]">
                        <img
                            src="https://placehold.co/600x400/3d7a4d/ffffff?text=Product+Image"
                            alt="Product"
                            className="w-full h-full object-cover"
                        />
                    </div>
                </div>
                <div className="mb-4 md:mb-6">
                    <span className="inline-flex items-center gap-2 px-4 md:px-6 py-2 bg-red-600 text-white text-xl md:text-2xl lg:text-3xl font-semibold rounded-md">
                        <span>50% OFF</span>
                        <span className="line-through opacity-70">1000Tk</span>
                        <span>500Tk</span>
                    </span>
                </div>
                <button
                    type="button"
                    className="w-full sm:w-auto py-3 px-4 text-black font-bold text-xs sm:text-sm md:text-base rounded-sm flex items-center justify-center gap-2 transition-colors duration-200 whitespace-nowrap"
                    style={{ backgroundColor: '#f97316' }}
                >
                    <i className="fa-solid fa-cart-shopping"></i>
                    Add to Cart
                </button>
            </div>
        </section>
    );
};

export default CenteredProductOfferHeroPreview;
