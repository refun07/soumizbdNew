import { closePopup, usePopup } from '../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../store/store';
import { cn } from '../../utils/cn';
import SearchForm from '../inputs/SearchForm';
import ModalWrapper from './ModalWrapper';

const SearchPopup = () => {
    const dispatch = useAppDispatch();
    const { popup } = usePopup();
    const isActive = popup === 'search';

    return (
        <ModalWrapper
            isActive={isActive}
            size="xl"
            className={cn('w-full bottom-8 translate-y-0 opacity-100 z-[3]', {
                'top-0': isActive,
                'top-full': !isActive,
            })}
        >
            <div className="theme-modal__body p-5 w-full">
                <SearchForm
                    placeholder="Search..."
                    autoFocus={isActive}
                    searchOnType={false}
                    searchPopupCloseBtn={true}
                    classNames={{
                        suggestionWrapper:
                            'relative translate-y-0 opacity-100 visible !shadow-none mt-2.5 -mx-4',
                        suggestionCategory: 'py-2.5 bg-[#F2F2F2]',
                        suggestionList: 'divide-y divide-neutral-200',
                        suggestionItem: 'py-1',
                    }}
                    onSubmit={() => dispatch(closePopup())}
                />
            </div>
        </ModalWrapper>
    );
};

export default SearchPopup;
