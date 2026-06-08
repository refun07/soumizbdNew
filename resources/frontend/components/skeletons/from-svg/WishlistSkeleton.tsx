import * as React from 'react';
import type { SVGProps } from 'react';
interface SVGRProps {
    title?: string;
    titleId?: string;
}
const SvgWishlistSkeleton = ({
    title,
    titleId,
    ...props
}: SVGProps<SVGSVGElement> & SVGRProps) => (
    <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 928 945"
        aria-labelledby={titleId}
        {...props}
    >
        {title ? <title id={titleId}>{title}</title> : null}
        <path fill="#fff" d="M0 0h928v945H0z" />
        <rect
            width={202}
            height={396}
            x={37.5}
            y={88.5}
            stroke="#EEE"
            rx={4.5}
        />
        <rect width={159} height={159} x={59} y={104} fill="#E5E7EB" rx={5} />
        <rect width={109} height={18} x={59} y={292} fill="#E5E7EB" rx={2} />
        <rect width={158} height={14} x={59} y={323} fill="#E5E7EB" rx={2} />
        <rect width={130} height={14} x={59} y={342} fill="#E5E7EB" rx={2} />
        <rect width={54} height={14} x={59} y={361} fill="#E5E7EB" rx={2} />
        <rect width={72} height={22} x={59} y={394} fill="#E5E7EB" rx={2} />
        <rect width={119} height={34} x={59} y={435} fill="#E5E7EB" rx={2} />
        <rect
            width={202}
            height={396}
            x={37.5}
            y={499.5}
            stroke="#EEE"
            rx={4.5}
        />
        <rect width={159} height={159} x={59} y={515} fill="#E5E7EB" rx={5} />
        <rect width={109} height={18} x={59} y={703} fill="#E5E7EB" rx={2} />
        <rect width={158} height={14} x={59} y={734} fill="#E5E7EB" rx={2} />
        <rect width={130} height={14} x={59} y={753} fill="#E5E7EB" rx={2} />
        <rect width={54} height={14} x={59} y={772} fill="#E5E7EB" rx={2} />
        <rect width={72} height={22} x={59} y={805} fill="#E5E7EB" rx={2} />
        <rect width={119} height={34} x={59} y={846} fill="#E5E7EB" rx={2} />
        <rect
            width={202}
            height={396}
            x={254.5}
            y={88.5}
            stroke="#EEE"
            rx={4.5}
        />
        <rect width={159} height={159} x={276} y={104} fill="#E5E7EB" rx={5} />
        <rect width={109} height={18} x={276} y={292} fill="#E5E7EB" rx={2} />
        <rect width={158} height={14} x={276} y={323} fill="#E5E7EB" rx={2} />
        <rect width={130} height={14} x={276} y={342} fill="#E5E7EB" rx={2} />
        <rect width={54} height={14} x={276} y={361} fill="#E5E7EB" rx={2} />
        <rect width={72} height={22} x={276} y={394} fill="#E5E7EB" rx={2} />
        <rect width={119} height={34} x={276} y={435} fill="#E5E7EB" rx={2} />
        <rect
            width={202}
            height={396}
            x={254.5}
            y={499.5}
            stroke="#EEE"
            rx={4.5}
        />
        <rect width={159} height={159} x={276} y={515} fill="#E5E7EB" rx={5} />
        <rect width={109} height={18} x={276} y={703} fill="#E5E7EB" rx={2} />
        <rect width={158} height={14} x={276} y={734} fill="#E5E7EB" rx={2} />
        <rect width={130} height={14} x={276} y={753} fill="#E5E7EB" rx={2} />
        <rect width={54} height={14} x={276} y={772} fill="#E5E7EB" rx={2} />
        <rect width={72} height={22} x={276} y={805} fill="#E5E7EB" rx={2} />
        <rect width={119} height={34} x={276} y={846} fill="#E5E7EB" rx={2} />
        <rect
            width={202}
            height={396}
            x={471.5}
            y={88.5}
            stroke="#EEE"
            rx={4.5}
        />
        <rect width={159} height={159} x={493} y={104} fill="#E5E7EB" rx={5} />
        <rect width={109} height={18} x={493} y={292} fill="#E5E7EB" rx={2} />
        <rect width={158} height={14} x={493} y={323} fill="#E5E7EB" rx={2} />
        <rect width={130} height={14} x={493} y={342} fill="#E5E7EB" rx={2} />
        <rect width={54} height={14} x={493} y={361} fill="#E5E7EB" rx={2} />
        <rect width={72} height={22} x={493} y={394} fill="#E5E7EB" rx={2} />
        <rect width={119} height={34} x={493} y={435} fill="#E5E7EB" rx={2} />
        <rect
            width={202}
            height={396}
            x={471.5}
            y={499.5}
            stroke="#EEE"
            rx={4.5}
        />
        <rect width={159} height={159} x={493} y={515} fill="#E5E7EB" rx={5} />
        <rect width={109} height={18} x={493} y={703} fill="#E5E7EB" rx={2} />
        <rect width={158} height={14} x={493} y={734} fill="#E5E7EB" rx={2} />
        <rect width={130} height={14} x={493} y={753} fill="#E5E7EB" rx={2} />
        <rect width={54} height={14} x={493} y={772} fill="#E5E7EB" rx={2} />
        <rect width={72} height={22} x={493} y={805} fill="#E5E7EB" rx={2} />
        <rect width={119} height={34} x={493} y={846} fill="#E5E7EB" rx={2} />
        <rect
            width={202}
            height={396}
            x={688.5}
            y={88.5}
            stroke="#EEE"
            rx={4.5}
        />
        <rect width={159} height={159} x={710} y={104} fill="#E5E7EB" rx={5} />
        <rect width={109} height={18} x={710} y={292} fill="#E5E7EB" rx={2} />
        <rect width={158} height={14} x={710} y={323} fill="#E5E7EB" rx={2} />
        <rect width={130} height={14} x={710} y={342} fill="#E5E7EB" rx={2} />
        <rect width={54} height={14} x={710} y={361} fill="#E5E7EB" rx={2} />
        <rect width={72} height={22} x={710} y={394} fill="#E5E7EB" rx={2} />
        <rect width={119} height={34} x={710} y={435} fill="#E5E7EB" rx={2} />
        <rect
            width={202}
            height={396}
            x={688.5}
            y={499.5}
            stroke="#EEE"
            rx={4.5}
        />
        <rect width={159} height={159} x={710} y={515} fill="#E5E7EB" rx={5} />
        <rect width={109} height={18} x={710} y={703} fill="#E5E7EB" rx={2} />
        <rect width={158} height={14} x={710} y={734} fill="#E5E7EB" rx={2} />
        <rect width={130} height={14} x={710} y={753} fill="#E5E7EB" rx={2} />
        <rect width={54} height={14} x={710} y={772} fill="#E5E7EB" rx={2} />
        <rect width={72} height={22} x={710} y={805} fill="#E5E7EB" rx={2} />
        <rect width={119} height={34} x={710} y={846} fill="#E5E7EB" rx={2} />
    </svg>
);
export default SvgWishlistSkeleton;
