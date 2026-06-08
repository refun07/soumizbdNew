import { LiaTimesSolid } from 'react-icons/lia';
import DashboardSidebar from '../../layouts/components/DashboardSidebar';
import { closePopup, usePopup } from '../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../store/store';
import { translate } from '../../utils/translate';

const DashboardSidebarModal = () => {
    const dispatch = useAppDispatch();
    const { popup } = usePopup();
    const isActive = popup === 'dashboard-sidebar';

    return (
        <aside
            className={`fixed top-0 right-0 bottom-0 w-[calc(100%-50px)] max-w-[300px] bg-white z-[5] overflow-y-auto text-sm ${
                isActive ? 'translate-x-0 delay-150' : 'translate-x-full'
            } transition-all duration-150 ease-in-out`}
            aria-hidden={!isActive}
        >
            <div className="bg-primary text-white flex items-center justify-between h-12 px-8">
                <h4 className="uppercase">{translate('My Accounts')}</h4>

                <button
                    className="text-xl"
                    onClick={() => dispatch(closePopup())}
                >
                    <LiaTimesSolid />
                </button>
            </div>

            <DashboardSidebar className="static pb-10" />
        </aside>
    );
};

export default DashboardSidebarModal;
