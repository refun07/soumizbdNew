import * as React from 'react';
import type { SVGProps } from 'react';
interface SVGRProps {
    title?: string;
    titleId?: string;
}
const SvgProductPreviewSkeletonMobile = ({
    title,
    titleId,
    ...props
}: SVGProps<SVGSVGElement> & SVGRProps) => (
    <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 496 1136"
        aria-labelledby={titleId}
        {...props}
    >
        {title ? <title id={titleId}>{title}</title> : null}
        <path
            fill="#E5E7EB"
            d="M490.865 0H5.135C2.299 0 0 2.268 0 5.065v457.87C0 465.732 2.299 468 5.135 468h485.73c2.836 0 5.135-2.268 5.135-5.065V5.065C496 2.268 493.701 0 490.865 0M173 488H72a5 5 0 0 0-5 5v101a5 5 0 0 0 5 5h101a5 5 0 0 0 5-5V493a5 5 0 0 0-5-5M298 488H197a5 5 0 0 0-5 5v101a5 5 0 0 0 5 5h101a5 5 0 0 0 5-5V493a5 5 0 0 0-5-5M423 488H322a5 5 0 0 0-5 5v101a5 5 0 0 0 5 5h101a5 5 0 0 0 5-5V493a5 5 0 0 0-5-5M106 651H3a3 3 0 0 0-3 3v8a3 3 0 0 0 3 3h103a3 3 0 0 0 3-3v-8a3 3 0 0 0-3-3M312 682H3a3 3 0 0 0-3 3v12a3 3 0 0 0 3 3h309a3 3 0 0 0 3-3v-12a3 3 0 0 0-3-3M67 781H3a3 3 0 0 0-3 3v12a3 3 0 0 0 3 3h64a3 3 0 0 0 3-3v-12a3 3 0 0 0-3-3M195 707H3a3 3 0 0 0-3 3v12a3 3 0 0 0 3 3h192a3 3 0 0 0 3-3v-12a3 3 0 0 0-3-3M67 840H3a3 3 0 0 0-3 3v12a3 3 0 0 0 3 3h64a3 3 0 0 0 3-3v-12a3 3 0 0 0-3-3M67 985H3a3 3 0 0 0-3 3v14c0 1.66 1.343 3 3 3h64c1.657 0 3-1.34 3-3v-14a3 3 0 0 0-3-3M157 982H89a3 3 0 0 0-3 3v20c0 1.66 1.343 3 3 3h68c1.657 0 3-1.34 3-3v-20a3 3 0 0 0-3-3M112 776H88a3 3 0 0 0-3 3v24a3 3 0 0 0 3 3h24a3 3 0 0 0 3-3v-24a3 3 0 0 0-3-3M112 835H88a3 3 0 0 0-3 3v24a3 3 0 0 0 3 3h24a3 3 0 0 0 3-3v-24a3 3 0 0 0-3-3M150 776h-24a3 3 0 0 0-3 3v24a3 3 0 0 0 3 3h24a3 3 0 0 0 3-3v-24a3 3 0 0 0-3-3M150 835h-24a3 3 0 0 0-3 3v24a3 3 0 0 0 3 3h24a3 3 0 0 0 3-3v-24a3 3 0 0 0-3-3M186 776h-24a3 3 0 0 0-3 3v24a3 3 0 0 0 3 3h24a3 3 0 0 0 3-3v-24a3 3 0 0 0-3-3M186 835h-24a3 3 0 0 0-3 3v24a3 3 0 0 0 3 3h24a3 3 0 0 0 3-3v-24a3 3 0 0 0-3-3M155 1065H5a5 5 0 0 0-5 5v37a5 5 0 0 0 5 5h150c2.761 0 5-2.24 5-5v-37c0-2.76-2.239-5-5-5M326 1065H176c-2.761 0-5 2.24-5 5v37c0 2.76 2.239 5 5 5h150c2.761 0 5-2.24 5-5v-37c0-2.76-2.239-5-5-5"
        />
    </svg>
);
export default SvgProductPreviewSkeletonMobile;
