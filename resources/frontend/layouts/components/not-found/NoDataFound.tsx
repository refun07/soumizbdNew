import { translate } from '../../../utils/translate';

interface Props {
    image: string;
    title: string;
    description: string;
}

const NoDataFound = ({ title, image, description }: Props) => {
    return (
        <div className="text-center mt-10">
            <div className="">
                <img src={image} alt="" className="mx-auto" />
            </div>
            <h4 className="text-3xl mt-3">{translate(title)}</h4>
            <p className="mt-2">{translate(description)}</p>
        </div>
    );
};

export default NoDataFound;
