import { useEffect } from 'react';
import { useDispatch } from 'react-redux';
import { Outlet, useNavigate } from 'react-router-dom';
import { useAuth } from '../store/features/auth/authSlice';
import { togglePopup } from '../store/features/popup/popupSlice';

const ProtectedLayout = () => {
    const dispatch = useDispatch();
    const navigate = useNavigate();
    const { user, isLoading } = useAuth();

    useEffect(() => {
        if (!user && !isLoading) {
            navigate('/');

            setTimeout(() => {
                dispatch(togglePopup('signin'));
            }, 100);
        }
    }, [user, isLoading]);

    if (!user) return null;

    return (
        <>
            <Outlet />
        </>
    );
};

export default ProtectedLayout;
