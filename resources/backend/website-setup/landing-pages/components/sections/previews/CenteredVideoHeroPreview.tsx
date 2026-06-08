import React from 'react';

const CenteredVideoHeroPreview: React.FC = () => {
    return (
        <section className="w-full min-h-[300px] sm:min-h-[400px] flex flex-col items-center justify-center">
            <div className="w-full max-w-2xl mx-auto px-4 py-5 sm:py-8 flex flex-col items-center">
                <div className="w-full mb-8">
                    <div className="aspect-video rounded-xl overflow-hidden bg-[#2d5a3d] flex items-center justify-center">
                        <div className="text-center">
                            <i className="fa-brands fa-youtube text-6xl text-white/50 mb-2"></i>
                            <p className="text-white/50 text-sm">YouTube Video Preview</p>
                        </div>
                    </div>
                </div>
                <h1 className="text-gray-900 text-2xl sm:text-3xl lg:text-4xl font-bold text-center mb-3">Premium Product Showcase</h1>
                <p className="text-gray-600 text-center text-base sm:text-lg mb-8 max-w-2xl">
                    Discover our amazing product with this exclusive video presentation
                </p>
                <div className="mb-6">
                    <span className="inline-flex items-center gap-2 px-4 py-1.5 bg-red-600 text-white text-lg sm:text-2xl lg:text-3xl font-semibold rounded-md">
                        <span>LIMITED OFFER</span>
                        <span className="line-through opacity-70">2000Tk</span>
                        <span>999Tk</span>
                    </span>
                </div>
                <button
                    type="button"
                    className="py-3 px-8 bg-[#f97316] hover:bg-[#ea580c] text-black font-bold text-xs rounded-sm flex items-center justify-center gap-2 transition-colors duration-200"
                >
                    <i className="fa-solid fa-cart-shopping"></i>
                    Shop Now
                </button>
            </div>
        </section>
    );
};

export default CenteredVideoHeroPreview;
