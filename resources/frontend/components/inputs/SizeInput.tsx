import { useState } from 'react';

interface Props {
    name: string;
    sizes: string[];
    label?: string;
    value?: string;

    onChange?: (value: string) => void;
}

const SizeInput = ({ name, sizes, value, label, onChange }: Props) => {
    const [selected, setSelected] = useState(value);

    const handleClick = (value: string) => {
        setSelected(value);
        onChange?.(value);
    };

    return (
        <div className="flex gap-2 flex-wrap">
            {sizes.map((size) => (
                <button
                    className={`h-7 sm:h-[38px] aspect-square rounded inline-flex items-center justify-center text-sm text-black/70 border ${
                        selected === size
                            ? 'border-secondary bg-secondary text-white'
                            : 'border-zinc-100'
                    }`}
                    onClick={() => handleClick(size)}
                    key={size}
                >
                    {size}
                </button>
            ))}
        </div>
    );
};

export default SizeInput;
