import { LiaTimesSolid } from 'react-icons/lia';
import { useGetAllCategoriesQuery } from '../../../store/features/api/apiSlice';
import { closePopup, usePopup } from '../../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../../store/store';
import { translate } from '../../../utils/translate';
import Spinner from '../../loader/Spinner';
import Category from './Category';

const CategorySidebar = () => {
    const { popup } = usePopup();
    const dispatch = useAppDispatch();
    const { data: categoriesRes, isLoading } = useGetAllCategoriesQuery();

    const isActive = popup === 'categories';

    return (
        <aside
            className={`flex flex-col fixed top-0 left-0 bottom-0 w-[calc(100%-50px)] max-w-[300px] bg-white z-[5] overflow-y-auto text-sm ${
                isActive ? 'translate-x-0 delay-150' : '-translate-x-full'
            } transition-all duration-150 ease-in-out`}
            aria-hidden={!isActive}
        >
            <div className="bg-primary text-white flex items-center justify-between h-12 px-8">
                <h4 className="">{translate('categories')}</h4>

                <button
                    className="text-xl"
                    onClick={() => dispatch(closePopup())}
                >
                    <LiaTimesSolid />
                </button>
            </div>

            <div className="grow relative overflow-x-hidden text-zinc-900">
                <ul className="divide-y divide-zinc-100">
                    {isLoading ? (
                        <li className="py-6 text-center">
                            <Spinner size="xlarge" color="#333" />
                        </li>
                    ) : (
                        categoriesRes?.categories?.map((category) => (
                            <Category
                                category={category}
                                label={0}
                                key={category.id}
                            />
                        ))
                    )}
                </ul>
            </div>
        </aside>
    );
};

export default CategorySidebar;
