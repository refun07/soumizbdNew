import React from 'react';
import toast from 'react-hot-toast';
import { translate } from '../utils/translate';

interface Props {
    copyText: string;
    successMessage?: string;
    children?: React.ReactNode;
}

const Copy = ({ copyText, successMessage, children }: Props) => {
    const handleCopy = () => {
        navigator.clipboard.writeText(copyText);
        toast.success(successMessage || translate('Copied to clipboard'));
    };

    return <span onClick={handleCopy}>{children}</span>;
};

export default Copy;
