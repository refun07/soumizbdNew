import * as React from 'react';
import type { SVGProps } from 'react';
interface SVGRProps {
    title?: string;
    titleId?: string;
}
const SvgDashboardReviewsSkeleton = ({
    title,
    titleId,
    ...props
}: SVGProps<SVGSVGElement> & SVGRProps) => (
    <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 590 632"
        aria-labelledby={titleId}
        {...props}
    >
        {title ? <title id={titleId}>{title}</title> : null}
        <path fill="#fff" d="M0 0h590v632H0z" />
        <rect width={215} height={14} x={43} y={53} fill="#E5E7EB" rx={2} />
        <rect width={375} height={14} x={43} y={109} fill="#E5E7EB" rx={2} />
        <rect width={460} height={14} x={43} y={127} fill="#E5E7EB" rx={2} />
        <rect width={261} height={14} x={43} y={145} fill="#E5E7EB" rx={2} />
        <rect width={107} height={14} x={43} y={73} fill="#E5E7EB" rx={2} />
        <rect width={72} height={72} x={41} y={194} fill="#E5E7EB" rx={5} />
        <rect width={72} height={72} x={121} y={194} fill="#E5E7EB" rx={5} />
        <rect width={72} height={72} x={201} y={194} fill="#E5E7EB" rx={5} />
        <rect width={215} height={14} x={43} y={316} fill="#E5E7EB" rx={2} />
        <rect width={375} height={14} x={43} y={372} fill="#E5E7EB" rx={2} />
        <rect width={460} height={14} x={43} y={390} fill="#E5E7EB" rx={2} />
        <rect width={261} height={14} x={43} y={408} fill="#E5E7EB" rx={2} />
        <rect width={107} height={14} x={43} y={336} fill="#E5E7EB" rx={2} />
        <rect width={72} height={72} x={41} y={457} fill="#E5E7EB" rx={5} />
        <rect width={72} height={72} x={121} y={457} fill="#E5E7EB" rx={5} />
        <rect width={72} height={72} x={201} y={457} fill="#E5E7EB" rx={5} />
    </svg>
);
export default SvgDashboardReviewsSkeleton;
