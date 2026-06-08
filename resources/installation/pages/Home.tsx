import React from 'react';
import Button from '../components/Button';

const Home = () => {
    return (
        <div className="max-h-screen overflow-y-auto w-full max-w-[620px] bg-white rounded-md min-h-[300px] shadow-theme px-4 py-7 md:px-9 md:py-12 lg:px-[70px] lg:py-20 mx-3">
            <div className="text-center flex flex-col items-center">
                <img src="/images/logo.png" alt="" />
                <h2 className="py-2 font-semibold text-2xl">
                    Script Installation
                </h2>
                <p className="text-neutral-500">
                    Thank you for choosing EpikCart as your eCommerce solution.
                    Let’s move forward with the installation process!
                </p>
            </div>

            <table className="mt-8 w-full rounded-md">
                <thead>
                    <tr>
                        <td
                            colSpan={2}
                            className="rounded-t-md bg-secondary-light/10 font-medium"
                        >
                            You need the following before installation
                        </td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <ul className="list-disc pl-4">
                                <li>Database Name</li>
                            </ul>
                        </td>
                        <td>
                            <ul className="list-disc pl-4">
                                <li>Database Username</li>
                            </ul>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <ul className="list-disc pl-4">
                                <li>Database Hostname</li>
                            </ul>
                        </td>
                        <td>
                            <ul className="list-disc pl-4">
                                <li>Database Password</li>
                            </ul>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div className="flex justify-center">
                <Button as="a" href="/checklist" className="mt-8 mb-14">
                    START INSTALLATION
                </Button>
            </div>
        </div>
    );
};

export default Home;
