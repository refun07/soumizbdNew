interface Props extends React.HTMLProps<HTMLDivElement> {}

const InputGroup = ({ children, ...rest }: Props) => {
    return <div {...rest}>{children}</div>;
};

export default InputGroup;
