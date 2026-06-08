import { cn } from '../utils/cn';

interface Props {
    links: {
        facebook?: string;
        twitter?: string;
        instagram?: string;
        youtube?: string;
        linkedin?: string;
    };
    classNames?: {
        container?: string;
        item?: string;
    };
}

const SocialLinks = ({ links, classNames }: Props) => {
    return (
        <div className={cn('flex gap-2', classNames?.container)}>
            {links.facebook ? (
                <a
                    href={links.facebook}
                    target="_blank"
                    rel="noopener noreferrer"
                    className={cn(
                        'size-10 bg-[-6px_-5px] rounded-full',
                        classNames?.item,
                    )}
                    style={{
                        backgroundImage: `url(/images/social-media-logos.png)`,
                    }}
                ></a>
            ) : null}
            {links.twitter ? (
                <a
                    href={links.twitter}
                    target="_blank"
                    rel="noopener noreferrer"
                    className={cn(
                        'size-10 bg-[-53px_-5px] rounded-full',
                        classNames?.item,
                    )}
                    style={{
                        backgroundImage: `url(/images/social-media-logos.png)`,
                    }}
                ></a>
            ) : null}
            {links.instagram ? (
                <a
                    href={links.instagram}
                    target="_blank"
                    rel="noopener noreferrer"
                    className={cn(
                        'size-10 bg-[-100px_-5px] rounded-full',
                        classNames?.item,
                    )}
                    style={{
                        backgroundImage: `url(/images/social-media-logos.png)`,
                    }}
                ></a>
            ) : null}
            {links.youtube ? (
                <a
                    href={links.youtube}
                    target="_blank"
                    rel="noopener noreferrer"
                    className={cn(
                        'size-10 bg-[-146px_-5px] rounded-full',
                        classNames?.item,
                    )}
                    style={{
                        backgroundImage: `url(/images/social-media-logos.png)`,
                    }}
                ></a>
            ) : null}
            {links.linkedin ? (
                <a
                    href={links.linkedin}
                    target="_blank"
                    rel="noopener noreferrer"
                    className={cn(
                        'size-10 bg-[-237px_-5px] rounded-full',
                        classNames?.item,
                    )}
                    style={{
                        backgroundImage: `url(/images/social-media-logos.png)`,
                    }}
                ></a>
            ) : null}
        </div>
    );
};

export default SocialLinks;
