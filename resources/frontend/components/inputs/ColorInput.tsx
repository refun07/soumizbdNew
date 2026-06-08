import ColorCheckbox from './ColorCheckbox';

interface Props {
    name: string;
    colors: {
        id: number;
        label?: string;
        code: string;
    }[];
    selected?: number[];

    onChange?: (e: React.ChangeEvent<HTMLInputElement>) => void;
}

const ColorInput = ({ name, colors, selected, onChange }: Props) => {
    return (
        <div className="flex gap-2 flex-wrap">
            {colors.map((color, index) => (
                <ColorCheckbox
                    key={index}
                    name={name}
                    value={color.id}
                    code={color.code}
                    checked={selected?.includes(color.id)}
                    onChange={onChange}
                />
            ))}
        </div>
    );
};

export default ColorInput;
