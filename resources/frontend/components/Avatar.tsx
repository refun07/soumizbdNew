import { cn } from '../utils/cn';
import Image from './Image';

interface Props {
    name: string;
    avatar?: string;
    size?: 'sm' | 'md' | 'lg' | 'xl' | 'xxl';
    className?: string;
}

const Avatar = ({ name, avatar, size = 'md', className }: Props) => {
    const sizeClasses = {
        sm: 'w-6 text-sm',
        md: 'w-9 text-xl',
        lg: 'w-12 text-2xl',
        xl: 'w-16 text-3xl',
        xxl: 'w-[96px] text-3xl',
    };

    if (!avatar) {
        return (
            <div
                className={cn(
                    `aspect-square rounded-full bg-zinc-300 uppercase flex items-center justify-center`,
                    sizeClasses[size],
                    className,
                )}
            >
                {name[0]}
            </div>
        );
    }

    return (
        <div className="">
            <Image
                src={avatar}
                alt=""
                className={`aspect-square rounded-full ${sizeClasses[size]}`}
            />
        </div>
    );
};

export default Avatar;
