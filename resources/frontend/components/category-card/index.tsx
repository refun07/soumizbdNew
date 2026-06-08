import { ICategoryCardProps } from '../../types/props';
import CategoryCardThemeOne from './CategoryCardThemeOne';
import CategoryCardThemeThree from './CategoryCardThemeThree';
import CategoryCardThemeTwo from './CategoryCardThemeTwo';

const CategoryCard = (props: ICategoryCardProps) => {
    if (window.config.defaultCategoryCard === 'category-card-one') {
        return <CategoryCardThemeOne {...props} />;
    } else if (window.config.defaultCategoryCard === 'category-card-two') {
        return <CategoryCardThemeTwo {...props} />;
    } else if (window.config.defaultCategoryCard === 'category-card-three') {
        return <CategoryCardThemeThree {...props} />;
    } else {
        return <CategoryCardThemeOne {...props} />;
    }
};

export default CategoryCard;
