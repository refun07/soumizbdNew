import React, { InputHTMLAttributes } from 'react';
import { FaMagnifyingGlass } from 'react-icons/fa6';
import { cn } from '../../utils/cn';

interface Props extends InputHTMLAttributes<HTMLInputElement> {
    classNames?: {
        form?: string;
        input?: string;
        icon?: string;
    };
}

const SearchForm = ({ classNames, ...rest }: Props) => {
    return (
        <div
            className={cn(
                `search-form relative flex gap-3 grow`,
                classNames?.form,
            )}
        >
            <input
                type="text"
                className={cn(
                    'theme-input search-form__input',
                    classNames?.input,
                )}
                autoComplete="off"
                {...rest}
            />
            <button
                type="submit"
                className={cn(
                    'text-secondary absolute top-0 right-0 h-full flex items-center justify-center px-3 pointer-events-none rounded-r-md',
                    classNames?.icon,
                )}
            >
                <FaMagnifyingGlass />
            </button>
        </div>
    );
};

export default SearchForm;
