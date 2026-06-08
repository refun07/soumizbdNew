import * as React from 'react';
import type { SVGProps } from 'react';
interface SVGRProps {
    title?: string;
    titleId?: string;
}
const SvgOrderDetailsSkeleton = ({
    title,
    titleId,
    ...props
}: SVGProps<SVGSVGElement> & SVGRProps) => (
    <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 930 628"
        aria-labelledby={titleId}
        {...props}
    >
        {title ? <title id={titleId}>{title}</title> : null}
        <path fill="#fff" d="M0 0h930v628H0z" />
        <rect width={78} height={16} x={46} y={49} fill="#E5E7EB" rx={3} />
        <rect width={45} height={14} x={46} y={192} fill="#E5E7EB" rx={3} />
        <rect width={45} height={14} x={136} y={192} fill="#E5E7EB" rx={3} />
        <rect width={45} height={14} x={226} y={192} fill="#E5E7EB" rx={3} />
        <rect width={45} height={14} x={316} y={192} fill="#E5E7EB" rx={3} />
        <rect width={45} height={14} x={406} y={192} fill="#E5E7EB" rx={3} />
        <rect width={124} height={14} x={469} y={89} fill="#E5E7EB" rx={3} />
        <rect width={62} height={14} x={531} y={114} fill="#E5E7EB" rx={3} />
        <rect width={151} height={20} x={46} y={72} fill="#E5E7EB" rx={3} />
        <rect width={152} height={37} x={441} y={49} fill="#E5E7EB" rx={3} />
        <circle cx={67.5} cy={163.5} r={17.5} fill="#E5E7EB" />
        <circle cx={157.5} cy={163.5} r={17.5} fill="#E5E7EB" />
        <circle cx={247.5} cy={163.5} r={17.5} fill="#E5E7EB" />
        <circle cx={337.5} cy={163.5} r={17.5} fill="#E5E7EB" />
        <circle cx={427.5} cy={163.5} r={17.5} fill="#E5E7EB" />
        <path fill="#E5E7EB" d="M0 293h639v48H0z" />
        <rect width={59} height={14} x={406} y={376} fill="#E5E7EB" rx={2} />
        <rect width={149} height={14} x={130} y={366} fill="#E5E7EB" rx={2} />
        <rect width={89} height={14} x={130} y={386} fill="#E5E7EB" rx={2} />
        <path fill="#E5E7EB" d="M46 352h62v62H46z" />
        <rect width={59} height={14} x={406} y={466} fill="#E5E7EB" rx={2} />
        <rect width={149} height={14} x={130} y={456} fill="#E5E7EB" rx={2} />
        <rect width={89} height={14} x={130} y={476} fill="#E5E7EB" rx={2} />
        <path fill="#E5E7EB" d="M46 442h62v62H46z" />
        <path stroke="#DBDBDB" strokeDasharray="2 2" d="M676.5 100v241" />
        <rect width={92} height={14} x={694} y={94} fill="#E5E7EB" rx={2} />
        <rect width={174} height={14} x={694} y={120} fill="#E5E7EB" rx={2} />
        <rect width={111} height={14} x={694} y={139} fill="#E5E7EB" rx={2} />
        <circle cx={677} cy={101} r={6} fill="#E5E7EB" />
        <rect width={92} height={14} x={694} y={210} fill="#E5E7EB" rx={2} />
        <rect width={174} height={14} x={694} y={236} fill="#E5E7EB" rx={2} />
        <rect width={111} height={14} x={694} y={255} fill="#E5E7EB" rx={2} />
        <circle cx={677} cy={217} r={6} fill="#E5E7EB" />
        <rect width={92} height={14} x={694} y={332} fill="#E5E7EB" rx={2} />
        <rect width={174} height={14} x={694} y={358} fill="#E5E7EB" rx={2} />
        <rect width={111} height={14} x={694} y={377} fill="#E5E7EB" rx={2} />
        <circle cx={677} cy={339} r={6} fill="#E5E7EB" />
        <path stroke="#374CB9" strokeOpacity={0.14} d="M639.5 0v628" />
    </svg>
);
export default SvgOrderDetailsSkeleton;
