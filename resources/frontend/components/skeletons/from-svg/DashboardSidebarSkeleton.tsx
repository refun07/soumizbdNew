import * as React from 'react';
import type { SVGProps } from 'react';
interface SVGRProps {
    title?: string;
    titleId?: string;
}
const SvgDashboardSidebarSkeleton = ({
    title,
    titleId,
    ...props
}: SVGProps<SVGSVGElement> & SVGRProps) => (
    <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 299 538"
        aria-labelledby={titleId}
        {...props}
    >
        {title ? <title id={titleId}>{title}</title> : null}
        <g clipPath="url(#dashboard-sidebar-skeleton_svg__a)">
            <path fill="#fff" d="M0 0h299v538H0z" />
            <path
                fill="#fff"
                d="M294 0H5a5 5 0 0 0-5 5v528a5 5 0 0 0 5 5h289a5 5 0 0 0 5-5V5a5 5 0 0 0-5-5"
            />
            <path
                fill="#E5E7EB"
                d="M195 81c0-25.957-21.043-47-47-47s-47 21.043-47 47 21.043 47 47 47 47-21.043 47-47M222 145H73v18h149zM199 167H96v12h103zM49 214H39a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-10a3 3 0 0 0-3-3M49 260H39a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-10a3 3 0 0 0-3-3M49 306H39a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-10a3 3 0 0 0-3-3M49 352H39a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-10a3 3 0 0 0-3-3M49 398H39a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-10a3 3 0 0 0-3-3M49 444H39a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-10a3 3 0 0 0-3-3M49 490H39a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-10a3 3 0 0 0-3-3M133 214H72a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h61a3 3 0 0 0 3-3v-10a3 3 0 0 0-3-3M168 260H72a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h96a3 3 0 0 0 3-3v-10a3 3 0 0 0-3-3M143 306H72a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h71a3 3 0 0 0 3-3v-10a3 3 0 0 0-3-3M189 352H72a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h117a3 3 0 0 0 3-3v-10a3 3 0 0 0-3-3M200 398H72a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h128a3 3 0 0 0 3-3v-10a3 3 0 0 0-3-3M125 444H72a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h53a3 3 0 0 0 3-3v-10a3 3 0 0 0-3-3M168 490H72a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h96a3 3 0 0 0 3-3v-10a3 3 0 0 0-3-3"
            />
        </g>
        <defs>
            <clipPath id="dashboard-sidebar-skeleton_svg__a">
                <path fill="#fff" d="M0 0h299v538H0z" />
            </clipPath>
        </defs>
    </svg>
);
export default SvgDashboardSidebarSkeleton;
