import { cn } from '../../utils/cn';
import { translate } from '../../utils/translate';

interface Props extends React.TextareaHTMLAttributes<HTMLTextAreaElement> {
    error?: string;
    touched?: boolean;
}
const Textarea = ({ name, className, error, touched, ...rest }: Props) => {
    const hasError = error && touched;

    return (
        <>
            <textarea
                name={name}
                rows={3}
                className={cn(
                    `w-full border border-primary-14 rounded-card py-2 px-[15px] focus:border-secondary placeholder:text-black/70`,
                    className,
                )}
                {...rest}
            />

            {hasError && (
                <span className="text-sm text-theme-alert">
                    {translate(error)}
                </span>
            )}
        </>
    );
};

export default Textarea;
