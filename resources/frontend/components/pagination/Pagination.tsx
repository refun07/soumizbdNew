import { FaChevronLeft, FaChevronRight } from 'react-icons/fa';
import ReactPaginate, { ReactPaginateProps } from 'react-paginate';
import { useSearchParams } from 'react-router-dom';
import { IPaginationMeta } from '../../types';
import { cn } from '../../utils/cn';
import { translate } from '../../utils/translate';

interface Props extends Partial<ReactPaginateProps> {
    pagination?: IPaginationMeta;
    scrollTo?: string;
    resourceName?: string;
}

const Pagination = ({
    className,
    pagination,
    scrollTo,
    resourceName,
    ...rest
}: Props) => {
    const [searchParams, setSearchParams] = useSearchParams();

    if (!pagination || pagination.last_page < 2) return null;

    const handlePageClick = (selectedItem: { selected: number }) => {
        // Scroll to top or to the element with the id of `scrollTo`
        const scrollToElement = scrollTo
            ? (document.querySelector(scrollTo) as HTMLElement)
            : null;

        if (scrollToElement) {
            window.scrollTo({
                top: (scrollToElement?.offsetTop || 0) - 90,
                behavior: 'instant',
            });
        }

        const page = selectedItem.selected + 1;
        searchParams.set('page', String(page));

        setSearchParams(searchParams);
    };

    const itemClasses = `size-7 sm:size-[38px] rounded inline-flex items-center justify-center text-sm text-black/70 border border-secondary hover:border-primary hover:text-white hover:bg-primary cursor-pointer`;

    return (
        <div
            className={cn(
                'flex max-md:flex-col items-center justify-end gap-3 sm:gap-10 border-t border-zinc-100 mt-12 pt-8',
                className,
            )}
        >
            <p className="text-black/70">
                {translate('showing')}{' '}
                <span className="text-black">{pagination.per_page}</span>{' '}
                {translate('of')}{' '}
                <span className="text-black">{pagination.total}</span>{' '}
                {resourceName}
            </p>

            <ReactPaginate
                breakLabel={<span className={itemClasses}>...</span>}
                onPageChange={handlePageClick}
                disableInitialCallback={true}
                pageRangeDisplayed={3}
                initialPage={pagination.current_page - 1}
                pageCount={pagination.last_page}
                renderOnZeroPageCount={null}
                containerClassName={cn(
                    `flex justify-end gap-2`,
                    rest.containerClassName,
                )}
                activeLinkClassName={cn(
                    '!text-white bg-primary border border-primary',
                    rest.activeLinkClassName,
                )}
                nextLabel={<FaChevronRight />}
                nextLinkClassName={itemClasses}
                previousLabel={<FaChevronLeft />}
                previousLinkClassName={itemClasses}
                pageLinkClassName={itemClasses}
            />
        </div>
    );
};

export default Pagination;
