import { motion } from 'framer-motion';
import {
    useGetBestSellerProductsQuery,
    useGetHighestRatedProductsQuery,
    useGetNewArrivalsQuery,
} from '../store/features/api/homeApis/commonHomeApi';
import { containerAnimation, itemAnimation } from '../utils/animation';
import { translate } from '../utils/translate';
import Button from './buttons/Button';
import ProductCardSmall from './card/ProductCardSmall';
import SectionTitle from './titles/SectionTitle';

const SmallProductsColumn = () => {
    const newArrivalsRes = useGetNewArrivalsQuery();
    const bestSellerProducts = useGetBestSellerProductsQuery();
    const highestRatedProducts = useGetHighestRatedProductsQuery();

    // Check if all product sections are disabled (empty arrays from backend when toggle is off)
    const allSectionsDisabled =
        (!newArrivalsRes.data || newArrivalsRes.data.length === 0) &&
        (!bestSellerProducts.data || bestSellerProducts.data.length === 0) &&
        (!highestRatedProducts.data || highestRatedProducts.data.length === 0);

    // Don't render the component if all sections are disabled
    if (allSectionsDisabled) {
        return null;
    }

    return (
        <section>
            <div className="container grid lg:grid-cols-3 gap-4">
                <div>
                    <SectionTitle
                        title="[New] Arrivals"
                        classNames={{
                            title: 'ek-h3',
                        }}
                    />

                    <motion.div
                        variants={containerAnimation}
                        initial="initial"
                        whileInView="whileInView"
                        viewport={{ once: true }}
                        className="space-y-5"
                    >
                        {newArrivalsRes.data?.map((product) => (
                            <motion.div
                                variants={itemAnimation}
                                key={product.id}
                            >
                                <ProductCardSmall product={product} />
                            </motion.div>
                        ))}
                    </motion.div>
                    <div className="flex items-center justify-end mt-5 gap-4">
                        <hr className="grow" />
                        <Button
                            as="link"
                            to={'/products?sortBy=newest'}
                            variant="outline"
                            className=""
                        >
                            {translate('View More')}
                        </Button>
                    </div>
                </div>

                <div>
                    <SectionTitle
                        title="[Best] Sellers"
                        classNames={{
                            title: 'ek-h3',
                        }}
                    />

                    <motion.div
                        variants={containerAnimation}
                        initial="initial"
                        whileInView="whileInView"
                        viewport={{ once: true }}
                        className="space-y-5"
                    >
                        {bestSellerProducts.data?.map((product) => (
                            <motion.div
                                variants={itemAnimation}
                                key={product.id}
                            >
                                <ProductCardSmall product={product} />
                            </motion.div>
                        ))}
                    </motion.div>
                    <div className="flex items-center justify-end mt-5 gap-4">
                        <hr className="grow" />
                        <Button
                            as="link"
                            to={'/products'}
                            variant="outline"
                            className=""
                        >
                            {translate('View More')}
                        </Button>
                    </div>
                </div>

                <div>
                    <SectionTitle
                        title="[Highest] Rated"
                        classNames={{
                            title: 'ek-h3',
                        }}
                    />

                    <motion.div
                        variants={containerAnimation}
                        initial="initial"
                        whileInView="whileInView"
                        viewport={{ once: true }}
                        className="space-y-5"
                    >
                        {highestRatedProducts.data?.map((product) => (
                            <motion.div
                                variants={itemAnimation}
                                key={product.id}
                            >
                                <ProductCardSmall product={product} />
                            </motion.div>
                        ))}
                    </motion.div>
                    <div className="flex items-center justify-end mt-5 gap-4">
                        <hr className="grow" />
                        <Button
                            as="link"
                            to={'/products'}
                            variant="outline"
                            className=""
                        >
                            {translate('View More')}
                        </Button>
                    </div>
                </div>
            </div>
        </section>
    );
};

export default SmallProductsColumn;
