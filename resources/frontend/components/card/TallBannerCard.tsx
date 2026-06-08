import { FaArrowRight } from 'react-icons/fa';
import IconButton from '../buttons/IconButton';

interface Props {
    image: string;
    bg: string;
    subTitle: string;
    title: string;
    description: string;
    link: string;
}

const TallBannerCard = ({ image, bg, subTitle, title, description, link }: Props) => {
    return (
        <div
            className="group rounded-banner text-center px-9 pt-7 pb-14 relative overflow-hidden h-full flex flex-col"
            style={{
                backgroundColor: `${bg}80`,
            }}
        >
            <div
                className="absolute top-0 left-0 right-0 h-[140%] -z-[1] rounded-b-full -translate-y-[59%] xl:-translate-y-[67%] 2xl:-translate-y-[59%] group-hover:translate-y-0 group-hover:delay-200 transition-all ease-in-out duration-500"
                style={{
                    backgroundColor: bg,
                }}
            ></div>

            <div className="flex justify-center items-center mb-20 h-[250px]">
                <img
                    src={image}
                    alt={title}
                    className="max-h-full w-auto max-w-[90%] group-hover:scale-110 transition-all duration-500 object-contain"
                />
            </div>

            <div className="flex-grow">
                <h5 className="text-black/70">{subTitle}</h5>
                <h4 className="ek-h4 font-semibold mt-2">{title}</h4>
                {description && (
                    <p className="text-sm text-black/60 mt-2 whitespace-pre-wrap">{description}</p>
                )}
            </div>

            <div className="flex justify-center mt-6">
                <IconButton
                    as="link"
                    to={link}
                    className="rounded-full max-md:size-10 size-12 border-secondary text-secondary"
                >
                    <FaArrowRight />
                </IconButton>
            </div>
        </div>
    );
};

export default TallBannerCard;
