import React, { useState } from 'react';
import toast from 'react-hot-toast';
import { useAppContext } from '../../Context';
import { runMigration } from '../../utils/actions';
import Button from '../Button';

interface Props {
    onSubmit: () => void;
}

const RunMigration = ({ onSubmit }: Props) => {
    const { setState } = useAppContext();
    const [isLoading, setIsLoading] = useState(false);
    const [demo, setDemo] = useState(false);

    const handleRunMigrationSubmit = async (demo: boolean) => {
        setDemo(demo);
        setIsLoading(true);

        try {
            const res = await runMigration(demo);

            onSubmit();
            setState({ countries: res.countries });
        } catch (error: any) {
            toast.error(error.response.data.message);
        }
        setIsLoading(false);
    };

    return (
        <div className="max-w-[500px] mx-auto">
            <div className="text-center mb-10">
                <h2 className="text-2xl font-medium mb-3">Run Migration</h2>
                <p>
                    Thank you for choosing EpikCart as your eCommerce solution.
                    Let’s move forward with the installation process!
                </p>
            </div>

            <div className="max-w-[450px] mx-auto flex items-center justify-center gap-4">
                <Button
                    type="button"
                    onClick={() => handleRunMigrationSubmit(false)}
                    disabled={isLoading && demo === false}
                    isLoading={isLoading && demo === false}
                >
                    Run Migration
                </Button> 
            </div>
        </div>
    );
};

export default RunMigration;
