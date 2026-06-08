import React, { useEffect, useState } from 'react';
import { FaCartShopping } from 'react-icons/fa6';
import { GiBeachBag } from 'react-icons/gi';
import { HiMiniUsers } from 'react-icons/hi2';
import { translate } from '../../../react/utils/translate';
import { INewUpdates } from '../../types/response';
import { getNewUpdates } from '../../utils';

const NewUpdatesCard = () => {
    const [newUpdates, setNewUpdates] = useState<INewUpdates>();

    useEffect(() => {
        getNewUpdates().then(setNewUpdates);
    }, []);

    return (
        <div className="relative bg-background rounded-md overflow-hidden px-4 py-3 sm:px-8 sm:py-6 xl:py-9 2xl:px-[70px] 2xl:py-[55px]">
            <h1 className="text-sm sm:text-lg lg:text-2xl font-medium relative">
                {translate('Hey Admin!')} <br />
                {translate('Welcome Back to Your Dashboard')}
            </h1>
            <p className="text-muted text-xs md:text-lg font-light mt-1 relative">
                {translate('You have new notifications!')}
            </p>

            <div className="mt-5 flex md:items-center gap-5 max-md:flex-col relative">
                <div className="flex items-center gap-4">
                    <div className="relative">
                        <div className="bg-rose-500 text-white leading-none text-[8px] md:text-[11px] border-2 border-background font-bold h-4 min-w-[20px] md:h-6 md:min-w-[24px] flex items-center justify-center rounded-full absolute right-0 top-0 -translate-y-1 translate-x-3">
                            {newUpdates?.newOrders || 0}
                        </div>
                        <div className="bg-lime-200 text-black/20 h-7 w-7 md:h-10 md:w-10 rounded-full flex items-center justify-center text-xl">
                            <FaCartShopping />
                        </div>
                    </div>

                    <div className="max-sm:text-xs text-muted">
                        {translate('New Orders')}
                    </div>
                </div>

                <div className="flex items-center gap-4">
                    <div className="relative">
                        <div className="bg-rose-500 text-white leading-none text-[8px] md:text-[11px] border-2 border-background font-bold h-4 min-w-[20px] md:h-6 md:min-w-[24px] flex items-center justify-center rounded-full absolute right-0 top-0 -translate-y-1 translate-x-3">
                            {newUpdates?.newSellers || 0}
                        </div>
                        <div className="bg-sky-200 text-black/20 h-7 w-7 md:h-10 md:w-10 rounded-full flex items-center justify-center text-xl">
                            <GiBeachBag />
                        </div>
                    </div>

                    <div className="max-sm:text-xs text-muted">
                        {translate('New Sellers')}
                    </div>
                </div>

                <div className="flex items-center gap-4">
                    <div className="relative">
                        <div className="bg-rose-500 text-white leading-none text-[8px] md:text-[11px] border-2 border-background font-bold h-4 min-w-[20px] md:h-6 md:min-w-[24px] flex items-center justify-center rounded-full absolute right-0 top-0 -translate-y-1 translate-x-3">
                            {newUpdates?.newCustomers || 0}
                        </div>
                        <div className="bg-sky-200 text-black/20 h-7 w-7 md:h-10 md:w-10 rounded-full flex items-center justify-center text-xl">
                            <HiMiniUsers />
                        </div>
                    </div>

                    <div className="max-sm:text-xs text-muted">
                        {translate('New Customers')}
                    </div>
                </div>
            </div>
        </div>
    );
};

export default NewUpdatesCard;
