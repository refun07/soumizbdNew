import React from 'react';
import { FaMagnifyingGlass } from 'react-icons/fa6';

interface Props
    extends React.DetailedHTMLProps<
        React.InputHTMLAttributes<HTMLInputElement>,
        HTMLInputElement
    > {}

const SearchForm = ({ name, className, ...rest }: Props) => {
    const handleSubmit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
    };

    return (
        <form
            onSubmit={handleSubmit}
            className={`search-form relative flex-grow w-full`}
        >
            <input
                type="text"
                name={name}
                className={`theme-input search-form__input ${className}`}
                autoComplete="off"
                {...rest}
            />
            <span className="text-primary absolute top-0 right-0 h-full flex items-center justify-center px-3 pointer-events-none">
                <FaMagnifyingGlass />
            </span>
        </form>
    );
};

export default SearchForm;
