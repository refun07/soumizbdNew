import React from 'react';
import { Outlet } from 'react-router-dom';

const Layout = () => {
    return (
        <div className="relative">
            <div className="flex h-screen justify-center items-center">
                <Outlet />
            </div>

            <div className="hidden md:fixed bottom-7 md:bottom-20 left-10 md:left-28">
                <p className="text-[#454444] leading-none">Powered By</p>
                <img src="/images/logo.png" alt="" />
            </div>
        </div>
    );
};

export default Layout;
