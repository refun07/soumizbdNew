import React from 'react';
import Button from '../components/Button';

const Success = () => {
    return (
        <div className="max-h-screen overflow-y-auto w-full max-w-[700px] bg-white rounded-md min-h-[300px] shadow-theme px-4 py-7 md:px-9 md:py-20 lg:px-[80px] lg:py-20 mx-3">
            <div className="flex flex-col items-center text-center max-w-[450px] mx-auto">
                <div>
                    <img
                        src="/images/install-success.svg"
                        alt="Success"
                        className="mx-auto"
                    />
                </div>
                <h1 className="mt-10 text-2xl font-medium">
                    Installation Successful
                </h1>
                <p className="mt-4 leading-snug">
                    Now you can login to your dashboard and further customize
                    and edit your brand new eCommerce website.
                </p>

                <div className="grid xs:grid-cols-2 gap-4 mt-8">
                    <Button as="a" href="/" reloadDocument>
                        BROWSE FRONTEND
                    </Button>
                    <Button as="a" href="/login" reloadDocument>
                        LOGIN TO DASHBOARD
                    </Button>
                </div>
            </div>
        </div>
    );
};

export default Success;
