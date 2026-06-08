import React from 'react';

const CenteredOfferHeroPreview: React.FC = () => {
    return (
        <section className="w-full min-h-[300px] sm:min-h-[400px] flex items-center justify-center" style={{ backgroundColor: '#1E4215' }}>
            <div className="w-full max-w-2xl mx-auto px-4 py-5 sm:py-8 text-center">
                <h1 className="text-white text-2xl sm:text-3xl lg:text-4xl font-bold text-center mb-4">Exclusive Deal Just For You!</h1>
                <p className="text-white/60 text-sm text-center mb-6 max-w-md mx-auto">
                    Don't miss out on this limited time offer on our premium products
                </p>
                <div className="mb-8">
                    <span className="inline-flex items-center gap-3 px-6 py-3 bg-red-600 text-white text-xl sm:text-2xl lg:text-3xl font-semibold rounded-md">
                        <span>FLASH SALE</span>
                        <span className="line-through opacity-70">1500Tk</span>
                        <span>750Tk</span>
                    </span>
                </div>
                <button
                    type="button"
                    className="inline-flex items-center gap-2 px-8 py-4 font-bold uppercase tracking-wide hover:opacity-90 transition-opacity"
                    style={{ backgroundColor: '#E68A3D' }}
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="black" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                    <span className="text-black">Buy Now</span>
                </button>
            </div>
        </section>
    );
};

export default CenteredOfferHeroPreview;
