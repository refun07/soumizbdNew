import { NavLink } from 'react-router-dom';
import { cn } from '../../../utils/cn';

interface SimpleMenuItem {
    id: string;
    name: string;
    slug: string;
}

interface Props {
    items: SimpleMenuItem[];
    classNames?: {
        subMenuContainer?: string;
        subMenuLinks?: string;
    };
    dropdownPosition?: 'left' | 'right' | 'center' | 'relative-left' | 'relative-right' | 'relative-center';
}

const SimpleDropdown = ({ items, classNames, dropdownPosition = 'relative-center' }: Props) => {
    return (
        <div
            className={cn(
                'absolute top-[calc(100%+12px)] min-w-[220px] z-10 bg-white rounded-md shadow-lg border translate-y-2 opacity-0 invisible group-hover:translate-y-0 group-hover:opacity-100 group-hover:visible transition-all duration-300',
                classNames?.subMenuContainer,
                {
                    'right-0': dropdownPosition === 'relative-left' || dropdownPosition === 'right',
                    'left-0': dropdownPosition === 'relative-right' || dropdownPosition === 'left',
                    'left-1/2 -translate-x-1/2': dropdownPosition === 'relative-center' || dropdownPosition === 'center',
                },
            )}
        >
            <ul className="py-2">
                {items.map((item) => (
                    <li key={item.id}>
                        <NavLink
                            to={item.slug}
                            className={cn(
                                'block px-4 py-2 text-black/70 hover:bg-gray-50 hover:text-primary transition-colors',
                                classNames?.subMenuLinks,
                            )}
                        >
                            {item.name}
                        </NavLink>
                    </li>
                ))}
            </ul>
        </div>
    );
};

export default SimpleDropdown;
