import * as React from 'react';
import type { SVGProps } from 'react';
interface SVGRProps {
    title?: string;
    titleId?: string;
}
const SvgDashboardOrderSkeleton = ({
    title,
    titleId,
    ...props
}: SVGProps<SVGSVGElement> & SVGRProps) => (
    <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 928 631"
        aria-labelledby={titleId}
        {...props}
    >
        {title ? <title id={titleId}>{title}</title> : null}
        <g clipPath="url(#dashboard-order-skeleton_svg__a)">
            <path fill="#fff" d="M928-151H0v782h928z" />
            <path
                fill="#E5E7EB"
                d="M927 0H0v55h927zM139 104H41a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h98a3 3 0 0 0 3-3v-10a3 3 0 0 0-3-3M460 104h-65a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h65a3 3 0 0 0 3-3v-10a3 3 0 0 0-3-3M568 104h-55a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h55a3 3 0 0 0 3-3v-10a3 3 0 0 0-3-3M876 100h-79a3 3 0 0 0-3 3v19a3 3 0 0 0 3 3h79a3 3 0 0 0 3-3v-19a3 3 0 0 0-3-3M719 104h-69a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h69a3 3 0 0 0 3-3v-10a3 3 0 0 0-3-3M634 119a6 6 0 1 0 0-12 6 6 0 0 0 0 12M323 114H216a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h107a3 3 0 0 0 3-3v-10a3 3 0 0 0-3-3M290 94h-74a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h74a3 3 0 0 0 3-3V97a3 3 0 0 0-3-3M139 190H41a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h98a3 3 0 0 0 3-3v-10a3 3 0 0 0-3-3M460 190h-65a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h65a3 3 0 0 0 3-3v-10a3 3 0 0 0-3-3M568 190h-55a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h55a3 3 0 0 0 3-3v-10a3 3 0 0 0-3-3M876 186h-79a3 3 0 0 0-3 3v19a3 3 0 0 0 3 3h79a3 3 0 0 0 3-3v-19a3 3 0 0 0-3-3M719 190h-69a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h69a3 3 0 0 0 3-3v-10a3 3 0 0 0-3-3M634 205a6 6 0 1 0 0-12 6 6 0 0 0 0 12M323 200H216a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h107a3 3 0 0 0 3-3v-10a3 3 0 0 0-3-3M290 180h-74a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h74a3 3 0 0 0 3-3v-10a3 3 0 0 0-3-3M139 276H41a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h98a3 3 0 0 0 3-3v-10a3 3 0 0 0-3-3M460 276h-65a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h65a3 3 0 0 0 3-3v-10a3 3 0 0 0-3-3M568 276h-55a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h55a3 3 0 0 0 3-3v-10a3 3 0 0 0-3-3M876 272h-79a3 3 0 0 0-3 3v19a3 3 0 0 0 3 3h79a3 3 0 0 0 3-3v-19a3 3 0 0 0-3-3M719 276h-69a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h69a3 3 0 0 0 3-3v-10a3 3 0 0 0-3-3M634 291a6 6 0 1 0 0-12 6 6 0 0 0 0 12M323 286H216a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h107a3 3 0 0 0 3-3v-10a3 3 0 0 0-3-3M290 266h-74a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h74a3 3 0 0 0 3-3v-10a3 3 0 0 0-3-3M139 362H41a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h98a3 3 0 0 0 3-3v-10a3 3 0 0 0-3-3M460 362h-65a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h65a3 3 0 0 0 3-3v-10a3 3 0 0 0-3-3M568 362h-55a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h55a3 3 0 0 0 3-3v-10a3 3 0 0 0-3-3M876 358h-79a3 3 0 0 0-3 3v19a3 3 0 0 0 3 3h79a3 3 0 0 0 3-3v-19a3 3 0 0 0-3-3M719 362h-69a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h69a3 3 0 0 0 3-3v-10a3 3 0 0 0-3-3M634 377a6 6 0 1 0 0-12 6 6 0 0 0 0 12M323 372H216a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h107a3 3 0 0 0 3-3v-10a3 3 0 0 0-3-3M290 352h-74a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h74a3 3 0 0 0 3-3v-10a3 3 0 0 0-3-3"
            />
        </g>
        <defs>
            <clipPath id="dashboard-order-skeleton_svg__a">
                <path fill="#fff" d="M0 0h928v631H0z" />
            </clipPath>
        </defs>
    </svg>
);
export default SvgDashboardOrderSkeleton;
