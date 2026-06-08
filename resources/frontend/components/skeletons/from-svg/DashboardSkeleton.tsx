import * as React from 'react';
import type { SVGProps } from 'react';
interface SVGRProps {
    title?: string;
    titleId?: string;
}
const SvgDashboardSkeleton = ({
    title,
    titleId,
    ...props
}: SVGProps<SVGSVGElement> & SVGRProps) => (
    <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 928 639"
        aria-labelledby={titleId}
        {...props}
    >
        {title ? <title id={titleId}>{title}</title> : null}
        <g clipPath="url(#dashboard-skeleton_svg__a)">
            <path fill="#fff" d="M0 0h928v639H0z" />
            <path
                fill="#fff"
                d="M923 0H5a5 5 0 0 0-5 5v629a5 5 0 0 0 5 5h918a5 5 0 0 0 5-5V5a5 5 0 0 0-5-5"
            />
            <path
                fill="#E5E7EB"
                d="M316 47H51a5 5 0 0 0-5 5v151a5 5 0 0 0 5 5h265a5 5 0 0 0 5-5V52a5 5 0 0 0-5-5M602 47H337a5 5 0 0 0-5 5v151a5 5 0 0 0 5 5h265a5 5 0 0 0 5-5V52a5 5 0 0 0-5-5M887 47H622a5 5 0 0 0-5 5v151a5 5 0 0 0 5 5h265a5 5 0 0 0 5-5V52a5 5 0 0 0-5-5"
            />
            <path stroke="#F7F7F7" d="M46 247.5h846" />
            <path
                stroke="#EEE"
                d="M316 282.5H50a4.5 4.5 0 0 0-4.5 4.5v89a4.5 4.5 0 0 0 4.5 4.5h266a4.5 4.5 0 0 0 4.5-4.5v-89a4.5 4.5 0 0 0-4.5-4.5ZM603 282.5H337a4.5 4.5 0 0 0-4.5 4.5v89a4.5 4.5 0 0 0 4.5 4.5h266a4.5 4.5 0 0 0 4.5-4.5v-89a4.5 4.5 0 0 0-4.5-4.5ZM888 282.5H622a4.5 4.5 0 0 0-4.5 4.5v89a4.5 4.5 0 0 0 4.5 4.5h266a4.5 4.5 0 0 0 4.5-4.5v-89a4.5 4.5 0 0 0-4.5-4.5Z"
            />
            <path
                fill="#E5E7EB"
                d="M111 306H71a5 5 0 0 0-5 5v40a5 5 0 0 0 5 5h40a5 5 0 0 0 5-5v-40a5 5 0 0 0-5-5M398 306h-40a5 5 0 0 0-5 5v40a5 5 0 0 0 5 5h40a5 5 0 0 0 5-5v-40a5 5 0 0 0-5-5M683 306h-40a5 5 0 0 0-5 5v40a5 5 0 0 0 5 5h40a5 5 0 0 0 5-5v-40a5 5 0 0 0-5-5M212 320h-77a5 5 0 0 0-5 5v9a5 5 0 0 0 5 5h77a5 5 0 0 0 5-5v-9a5 5 0 0 0-5-5M499 320h-77a5 5 0 0 0-5 5v9a5 5 0 0 0 5 5h77a5 5 0 0 0 5-5v-9a5 5 0 0 0-5-5M784 320h-77a5 5 0 0 0-5 5v9a5 5 0 0 0 5 5h77a5 5 0 0 0 5-5v-9a5 5 0 0 0-5-5M294 317h-26a5 5 0 0 0-5 5v15a5 5 0 0 0 5 5h26a5 5 0 0 0 5-5v-15a5 5 0 0 0-5-5M581 317h-26a5 5 0 0 0-5 5v15a5 5 0 0 0 5 5h26a5 5 0 0 0 5-5v-15a5 5 0 0 0-5-5M866 317h-26a5 5 0 0 0-5 5v15a5 5 0 0 0 5 5h26a5 5 0 0 0 5-5v-15a5 5 0 0 0-5-5M393 462H50a5 5 0 0 0-5 5v126a5 5 0 0 0 5 5h343a5 5 0 0 0 5-5V467a5 5 0 0 0-5-5M538 462H413a5 5 0 0 0-5 5v126a5 5 0 0 0 5 5h125a5 5 0 0 0 5-5V467a5 5 0 0 0-5-5"
            />
            <path
                stroke="#9C9C9C"
                strokeWidth={3}
                d="M475.5 506v48M451 529.5h48"
            />
        </g>
        <defs>
            <clipPath id="dashboard-skeleton_svg__a">
                <path fill="#fff" d="M0 0h928v639H0z" />
            </clipPath>
        </defs>
    </svg>
);
export default SvgDashboardSkeleton;
