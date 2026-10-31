import { useState } from 'react';
import { cn } from '../../utils/cn';
import { translate } from '../../utils/translate';
import Checkbox from './Checkbox';

interface Props extends React.InputHTMLAttributes<HTMLInputElement> {
    error?: string;
    touched?: boolean;
    toggleBtn?: boolean;
}

const Input = ({
    name,
    type,
    className,
    error,
    touched,
    toggleBtn = true,
    ...rest
}: Props) => {
    const [inputType, setInputType] = useState(type);

    return (
        <>
            <input
                type={inputType}
                name={name}
                className={cn(
                    `bot-ui-input w-full min-w-0 border border-primary-14 rounded-card h-11 sm:h-10 text-base sm:text-sm px-[15px] focus:border-secondary placeholder:text-black/70`,
                    className,
                )}
                {...rest}
            />

            {error && touched && (
                <span className="text-sm text-theme-alert block">
                    {translate(error)}
                </span>
            )}

            {type === 'password' && toggleBtn && (
                <Checkbox
                    label={translate('Show Password')}
                    className="mt-2"
                    labelClassName="text-sm"
                    onChange={() => {
                        setInputType(
                            inputType === 'password' ? 'text' : 'password',
                        );
                    }}
                />
            )}
        </>
    );
};

export default Input;
