import React from 'react';
import { FaChevronLeft, FaChevronRight } from 'react-icons/fa';
import ReactPaginate, { ReactPaginateProps } from 'react-paginate';
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
    onPageChange,
    ...rest
}: Props) => {
    if (!pagination || pagination.last_page < 2) return null;

    const handlePageClick = (selectedItem: { selected: number }) => {
        const urlParams = new URLSearchParams(window.location.search);

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
        urlParams.set('page', String(page));
        history.replaceState(null, '', `?${urlParams.toString()}`);
        onPageChange?.({ selected: page });
    };

    const itemClasses = `h-7 sm:h-[38px] aspect-square rounded inline-flex items-center justify-center text-[13px] text-muted border border-border hover:border-secondary-light hover:text-secondary-light cursor-pointer`;

    return (
        <div
            className={cn(
                'flex max-md:flex-col items-center justify-end gap-y-2 sm:gap-10 border-t border-border mt-12 pt-4 md:pt-8',
                className,
            )}
        >
            <p className="text-muted">
                {translate('Showing')}{' '}
                <span className="text-foreground">{pagination.per_page}</span>{' '}
                {translate('of')}{' '}
                <span className="text-foreground">{pagination.total}</span>{' '}
                {resourceName}
            </p>

            <ReactPaginate
                breakLabel={<span className={itemClasses}>...</span>}
                onPageChange={handlePageClick}
                disableInitialCallback={true}
                pageRangeDisplayed={5}
                initialPage={pagination.current_page - 1}
                pageCount={pagination.last_page}
                renderOnZeroPageCount={null}
                containerClassName={cn(
                    `flex justify-end gap-2`,
                    rest.containerClassName,
                )}
                activeLinkClassName={cn(
                    '!text-white bg-secondary-light border border-border',
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
