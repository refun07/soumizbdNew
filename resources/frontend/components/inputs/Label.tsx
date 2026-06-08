interface Props extends React.HTMLProps<HTMLLabelElement> {}

const Label = ({ children, className, ...rest }: Props) => {
    return (
        <label
            className={`mb-2 text-sm uppercase inline-block ${className || ''}`}
            {...rest}
        >
            {children}
        </label>
    );
};

export default Label;
