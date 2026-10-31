import { Link } from 'react-router-dom';
import { LuArrowRight } from 'react-icons/lu';
import { ICategoryShort } from '../../types';

export default function BotanicalCategoryGrid({ categories }: { categories: ICategoryShort[] }) {
    return (
        <div className="bot-categories">
                        {categories.map((category) => (
                                <Link
                                    key={category.id}
                                    to={`/categories/${category.slug}`}
                                    className="bot-category"
                                >
                                    <div className="bot-category-image">
                                        <img
                                            className={
                                                category.slug.startsWith(
                                                    'hana-beauty',
                                                )
                                                    ? 'bot-category-landscape'
                                                    : undefined
                                            }
                                            src={
                                                category.thumbnailImage ||
                                                '/images/no-media.png'
                                            }
                                            alt=""
                                            width="180"
                                            height="180"
                                            loading="lazy"
                                        />
                                    </div>
                                    <div className="bot-category-caption">
                                        <div>
                                            {category.parent_category && (
                                                <p>{category.parent_category.name}</p>
                                            )}
                                            <h3>
                                                {(category.slug.startsWith('combos-') ? 'Combo Pack' : category.name)
                                                    .toLowerCase()
                                                    .replace(
                                                        /\b\w/g,
                                                        (letter) =>
                                                            letter.toUpperCase(),
                                                    )}
                                            </h3>
                                            <p>
                                                {category.productsCount}{' '}
                                                products
                                            </p>
                                        </div>
                                        <LuArrowRight aria-hidden="true" />
                                    </div>
                                </Link>
                            ))}
                    </div>
    );
}
