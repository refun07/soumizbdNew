import { IoArrowForward } from 'react-icons/io5';
import { NavLink } from 'react-router-dom';
import { ICategoryShort } from '../../../types';
import { cn } from '../../../utils/cn';

interface Props {
    classNames?: {
        subMenuGroup?: string;
        subMenuContainer?: string;
        subMenuLinks?: string;
    };
    dropdownPosition?:
        | 'left'
        | 'right'
        | 'center'
        | 'relative-left'
        | 'relative-right'
        | 'relative-center';
    subMenu: ICategoryShort[];
}

const MegaDropdown = ({ subMenu, classNames, dropdownPosition }: Props) => {
    const rootCategories = subMenu?.filter((item) => !item.parent_category) || [];
    const columnCount = Math.min(Math.max(rootCategories.length, 1), 3);

    return (
        <div
            className={cn(
                'absolute top-[calc(100%+12px)] z-10 grid max-h-[calc(100vh-250px)] w-max max-w-[calc(100vw-30px)] gap-9 overflow-y-auto rounded-card bg-white px-16 py-10 text-zinc-900 translate-y-2 opacity-0 invisible group-hover:translate-y-0 group-hover:opacity-100 group-hover:visible transition-all duration-300',
                classNames?.subMenuContainer,
                {
                    'left-[14px]': dropdownPosition === 'left',
                    'right-[14px]': dropdownPosition === 'right',

                    'right-0': dropdownPosition === 'relative-left',
                    'left-0': dropdownPosition === 'relative-right',

                    'left-1/2 -translate-x-1/2':
                        dropdownPosition === 'center' ||
                        dropdownPosition === 'relative-center',
                },
            )}
            style={{
                gridTemplateColumns: `repeat(${columnCount}, minmax(220px, 240px))`,
            }}
        >
            {rootCategories.map((item, subIdx) =>
                !item.parent_category ? (
                    <div key={item.id}>
                        <NavLink
                            to={`/categories/${item.slug}`}
                            className={cn(
                                'inline-flex gap-2 line-clamp-1 text-base font-medium text-zinc-900 hover:text-primary',
                                classNames?.subMenuLinks,
                            )}
                            key={subIdx}
                        >
                            {item.name}
                        </NavLink>
                        <div className="border-b mt-3 mb-5">
                            <span className="w-full block max-w-[50px] border-b-4 border-secondary"></span>
                        </div>

                        <ul className="space-y-2">
                            {item.children_categories.map((children) => (
                                <li key={children.id}>
                                    <NavLink
                                        to={`/categories/${children.slug}`}
                                        className={cn(
                                            'inline-flex gap-2 text-zinc-700 group/link hover:text-primary',
                                            classNames?.subMenuLinks,
                                        )}
                                    >
                                        <span className="text-xl w-0 group-hover/link:w-5 group-hover/link:text-secondary overflow-hidden transition-all">
                                            <IoArrowForward />
                                        </span>
                                        {children.name}
                                    </NavLink>
                                </li>
                            ))}
                        </ul>
                    </div>
                ) : null,
            )}
        </div>
    );
};

export default MegaDropdown;
