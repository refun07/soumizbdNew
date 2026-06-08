import * as React from 'react';
import type { SVGProps } from 'react';
interface SVGRProps {
    title?: string;
    titleId?: string;
}
const SvgCouponSkeleton = ({
    title,
    titleId,
    ...props
}: SVGProps<SVGSVGElement> & SVGRProps) => (
    <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 381 169"
        aria-labelledby={titleId}
        {...props}
    >
        {title ? <title id={titleId}>{title}</title> : null}
        <g clipPath="url(#coupon-skeleton_svg__a)">
            <path fill="#F9F9F9" d="M0 0h381v169H0z" />
            <rect width={75} height={12} x={45} y={19} fill="#E5E7EB" rx={3} />
            <rect width={141} height={35} x={45} y={33} fill="#E5E7EB" rx={3} />
            <rect
                width={109}
                height={27}
                x={227}
                y={25}
                fill="#E5E7EB"
                rx={3}
            />
            <rect width={50} height={9} x={286} y={57} fill="#E5E7EB" rx={3} />
            <rect width={49} height={10} x={45} y={100} fill="#E5E7EB" rx={3} />
            <rect width={44} height={10} x={45} y={115} fill="#E5E7EB" rx={3} />
            <rect
                width={78}
                height={10}
                x={111}
                y={100}
                fill="#E5E7EB"
                rx={3}
            />
            <rect
                width={63}
                height={10}
                x={111}
                y={115}
                fill="#E5E7EB"
                rx={3}
            />
            <path
                fill="#fff"
                d="M381 59a26 26 0 1 0 0 52V85zM0 111a26 26 0 1 0 0-52v26z"
            />
            <path stroke="#E5E7EB" strokeDasharray="3 3" d="M26 84.5h329" />
        </g>
        <defs>
            <clipPath id="coupon-skeleton_svg__a">
                <rect width={381} height={169} fill="#fff" rx={5} />
            </clipPath>
        </defs>
    </svg>
);
export default SvgCouponSkeleton;
