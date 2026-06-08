import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import AdminConfiguration from '../components/setupTabs/AdminConfiguration';
import DatabaseInfo from '../components/setupTabs/DatabaseInfo';
import RunMigration from '../components/setupTabs/RunMigration';

export type TStep = 'activation' | 'database' | 'migration' | 'admin config';

const steps: TStep[] = ['database', 'migration', 'admin config'];

const Setup = () => {
    const navigate = useNavigate();
    const [currentStep, setCurrentStep] = useState(1);

    const handleNext = async () => {
        if (currentStep < steps.length) {
            setCurrentStep(currentStep + 1);
        } else {
            handleSubmit();
        }
    };

    const handleSubmit = async () => {
        navigate('/success');
    };

    return (
        <div className="max-h-screen overflow-y-auto w-full max-w-[700px] bg-white rounded-md min-h-[300px] shadow-theme px-4 py-7 md:px-9 md:py-20 lg:px-[80px] lg:py-20 mx-3">
            <ul className="flex max-w-[546px] items-center justify-between mb-10 mx-auto">
                {steps.map((item, i) => (
                    <li
                        key={item}
                        className={`flex items-center relative ${
                            i !== 0 && 'grow justify-end'
                        }`}
                    >
                        {i !== 0 && (
                            <span
                                className={`absolute top-[13px] sm:top-4 md:top-[22px] right-[22px] h-0.5 sm:h-1 w-full ${
                                    i + 1 <= currentStep
                                        ? 'bg-secondary-light'
                                        : 'bg-zinc-300'
                                }`}
                            ></span>
                        )}
                        <div className="flex flex-col items-center gap-1 sm:gap-2.5">
                            <span
                                className={`relative z-[1] w-7 sm:w-9 md:w-11 aspect-square flex items-center justify-center rounded-full leading-none text-white ${
                                    i + 1 <= currentStep
                                        ? 'bg-secondary-light'
                                        : 'bg-zinc-300'
                                }`}
                            >
                                {i + 1}
                            </span>
                            <span className="text-xs font-public-sans uppercase mt-2">
                                {item}
                            </span>
                        </div>
                    </li>
                ))}
            </ul>

            {currentStep === 1 ? (
                <DatabaseInfo onSubmit={handleNext} />
            ) : currentStep === 2 ? (
                <RunMigration onSubmit={handleNext} />
            ) : (
                <AdminConfiguration onSubmit={handleNext} />
            )}
        </div>
    );
};

export default Setup;
