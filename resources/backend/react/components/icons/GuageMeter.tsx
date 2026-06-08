import * as React from 'react';
import type { SVGProps } from 'react';
interface SVGRProps {
    title?: string;
    titleId?: string;
}
const SvgGuageMeter = ({
    title,
    titleId,
    ...props
}: SVGProps<SVGSVGElement> & SVGRProps) => (
    <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 176 176"
        aria-labelledby={titleId}
        {...props}
    >
        {title ? <title id={titleId}>{title}</title> : null}
        <circle
            cx={88}
            cy={88}
            r={78}
            stroke="#fff"
            strokeDasharray="3 3"
            strokeOpacity={0.5}
            strokeWidth={19}
        />
    </svg>
);
export default SvgGuageMeter;
