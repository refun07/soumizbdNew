import React, { useEffect, useState } from 'react';
import { FaCheck, FaTimes } from 'react-icons/fa';
import { useAppContext } from '../Context';
import Button from '../components/Button';
import Spinner from '../components/Spinner';
import { IChecklist } from '../types';
import { getChecklist } from '../utils/actions';

const Checklist = () => {
    const { setState } = useAppContext();
    const [isLoading, setIsLoading] = useState(false);
    const [list, setList] = useState<IChecklist>();

    const isAllPermissionGranted = () => {
        if (list) {
            return (
                Object.values(list).findIndex((item) => item === false) === -1
            );
        }
        return false;
    };

    useEffect(() => {
        setIsLoading(true);
        getChecklist()
            .then((data) => {
                setList(data);
                if (isAllPermissionGranted()) {
                    setState({ fullPermission: true });
                }
            })
            .finally(() => {
                setIsLoading(false);
            });
    }, []);

    const okIcon = (
        <span className="text-xs h-6 w-6 rounded-full inline-flex items-center justify-center bg-teal-600 text-white">
            {isLoading ? <Spinner /> : <FaCheck />}
        </span>
    );
    const notOkIcon = (
        <span className="text-xs h-6 w-6 rounded-full inline-flex items-center justify-center bg-red-600 text-white">
            {isLoading ? <Spinner /> : <FaTimes />}
        </span>
    );

    return (
        <div className="max-h-screen overflow-y-auto w-full max-w-[620px] bg-white rounded-md min-h-[300px] shadow-theme px-4 py-7 md:px-9 md:py-12 lg:px-[70px] lg:py-20 mx-3">
            <div className="text-center flex flex-col items-center">
                <img src="/images/logo.png" alt="" />
                <h2 className="py-2 font-semibold text-2xl">
                    Requirements Checklist
                </h2>
                <p className="text-neutral-500">
                    Thank you for choosing EpikCart as your eCommerce solution.
                    Let’s move forward with the installation process!
                </p>
            </div>

            <table className="mt-8 w-full rounded-md">
                <tbody>
                    <tr>
                        <td>
                            {list?.curlEnabled ? okIcon : notOkIcon}
                            <span className="ml-3">CURL Enabled</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {list?.envFileWritePermission ? okIcon : notOkIcon}
                            <span className="ml-3">
                                ENV File Write Permission
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {list?.routesFileWritePermission
                                ? okIcon
                                : notOkIcon}
                            <span className="ml-3">
                                Routes File Write Permission
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div className="flex justify-center">
                <Button
                    as="a"
                    href="/setup"
                    className="mt-8 mb-14"
                    disabled={isAllPermissionGranted() ? false : true}
                >
                    NEXT
                </Button>
            </div>
        </div>
    );
};

export default Checklist;
