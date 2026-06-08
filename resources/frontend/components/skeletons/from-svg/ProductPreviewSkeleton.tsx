import * as React from 'react';
import type { SVGProps } from 'react';
interface SVGRProps {
    title?: string;
    titleId?: string;
}
const SvgProductPreviewSkeleton = ({
    title,
    titleId,
    ...props
}: SVGProps<SVGSVGElement> & SVGRProps) => (
    <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 958 660"
        aria-labelledby={titleId}
        {...props}
    >
        {title ? <title id={titleId}>{title}</title> : null}
        <rect width={483} height={462} x={34} y={33} fill="#E5E7EB" rx={5} />
        <rect width={111} height={111} x={95} y={512} fill="#E5E7EB" rx={5} />
        <rect width={111} height={111} x={220} y={512} fill="#E5E7EB" rx={5} />
        <rect width={111} height={111} x={345} y={512} fill="#E5E7EB" rx={5} />
        <rect width={109} height={14} x={534} y={34} fill="#E5E7EB" rx={3} />
        <rect width={315} height={18} x={534} y={65} fill="#E5E7EB" rx={3} />
        <rect width={70} height={18} x={534} y={164} fill="#E5E7EB" rx={3} />
        <rect width={198} height={18} x={534} y={90} fill="#E5E7EB" rx={3} />
        <rect width={70} height={18} x={534} y={223} fill="#E5E7EB" rx={3} />
        <rect width={70} height={20} x={534} y={368} fill="#E5E7EB" rx={3} />
        <rect width={74} height={26} x={620} y={365} fill="#E5E7EB" rx={3} />
        <rect width={30} height={30} x={619} y={159} fill="#E5E7EB" rx={3} />
        <rect width={30} height={30} x={619} y={218} fill="#E5E7EB" rx={3} />
        <rect width={30} height={30} x={657} y={159} fill="#E5E7EB" rx={3} />
        <rect width={30} height={30} x={657} y={218} fill="#E5E7EB" rx={3} />
        <rect width={30} height={30} x={693} y={159} fill="#E5E7EB" rx={3} />
        <rect width={30} height={30} x={693} y={218} fill="#E5E7EB" rx={3} />
        <rect width={160} height={47} x={534} y={448} fill="#E5E7EB" rx={5} />
        <rect width={160} height={47} x={705} y={448} fill="#E5E7EB" rx={5} />
    </svg>
);
export default SvgProductPreviewSkeleton;
