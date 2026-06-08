import ReactOtpInput from 'react-otp-input';

interface Props {
    value: string;
    onChange: (value: string) => void;
}

const OTPInput = ({ value, onChange }: Props) => {
    return (
        <ReactOtpInput
            value={value}
            onChange={onChange}
            numInputs={6}
            containerStyle={{
                display: 'flex',
                gap: '10px',
            }}
            inputStyle={{
                width: '40px',
                height: '40px',
            }}
            renderInput={(props) => (
                <input
                    {...props}
                    className="text-black border border-gray-200 rounded-card"
                />
            )}
        />
    );
};

export default OTPInput;
