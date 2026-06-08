import React from 'react';

const CarouselImagesPreview: React.FC = () => {
    return (
        <section className="py-10 sm:py-12 lg:py-16" style={{ backgroundColor: '#F8F9FA' }}>
            <div className="container mx-auto px-4">
                <h2 className="text-3xl md:text-4xl font-bold text-center mb-12 text-gray-800 font-serif">Our Gallery</h2>
                <div className="flex justify-center gap-4 flex-nowrap overflow-x-auto pb-4" style={{ scrollbarWidth: 'none', msOverflowStyle: 'none' }}>
                    <img src="https://placehold.co/300x200/1E3F1C/ffffff?text=Image+1" alt="Gallery 1" className="rounded-lg shadow-md flex-shrink-0" style={{ width: '300px', height: '200px' }} />
                    <img src="https://placehold.co/300x200/1E3F1C/ffffff?text=Image+2" alt="Gallery 2" className="rounded-lg shadow-md flex-shrink-0" style={{ width: '300px', height: '200px' }} />
                    <img src="https://placehold.co/300x200/1E3F1C/ffffff?text=Image+3" alt="Gallery 3" className="rounded-lg shadow-md flex-shrink-0" style={{ width: '300px', height: '200px' }} />
                    <img src="https://placehold.co/300x200/1E3F1C/ffffff?text=Image+4" alt="Gallery 4" className="rounded-lg shadow-md flex-shrink-0" style={{ width: '300px', height: '200px' }} />
                    <img src="https://placehold.co/300x200/1E3F1C/ffffff?text=Image+5" alt="Gallery 5" className="rounded-lg shadow-md flex-shrink-0" style={{ width: '300px', height: '200px' }} />
                    <img src="https://placehold.co/300x200/1E3F1C/ffffff?text=Image+6" alt="Gallery 6" className="rounded-lg shadow-md flex-shrink-0" style={{ width: '300px', height: '200px' }} />
                </div>
                <style>{`.carousel-scroll::-webkit-scrollbar { display: none; }`}</style>
                <div className="flex justify-center gap-2 mt-6">
                    <span className="w-3 h-3 rounded-full" style={{ backgroundColor: '#1E3F1C' }}></span>
                    <span className="w-3 h-3 rounded-full" style={{ backgroundColor: '#d1d5db' }}></span>
                    <span className="w-3 h-3 rounded-full" style={{ backgroundColor: '#d1d5db' }}></span>
                    <span className="w-3 h-3 rounded-full" style={{ backgroundColor: '#d1d5db' }}></span>
                    <span className="w-3 h-3 rounded-full" style={{ backgroundColor: '#d1d5db' }}></span>
                </div>
            </div>
        </section>
    );
};

export default CarouselImagesPreview;
