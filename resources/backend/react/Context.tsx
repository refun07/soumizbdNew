import React, {
    ReactNode,
    createContext,
    useContext,
    useEffect,
    useState,
} from 'react';
import { IAppContextStore, IAppContextType } from './types';
import { getGoal, postGoal } from './utils/actions';
import { translate } from './utils/translate';

export const AppContext = createContext<IAppContextType | null>(null);

const AppContextProvider: React.FC<{ children: ReactNode }> = ({
    children,
}) => {
    const [context, setContext] = useState<IAppContextStore>({
        popup: {
            name: null,
        },
        goal: undefined,
    });

    useEffect(() => {
        // fetch goal
        getGoal().then((goal) => {
            setContext((prev) => ({ ...prev, goal }));
        });
    }, []);

    const setPopup = (popup: IAppContextStore['popup']) => {
        setContext((prev) => ({ ...prev, popup }));
    };
    const closePopup = () => {
        setContext((prev) => ({ ...prev, popup: { name: null } }));
    };

    const updateGoal = async (amount: number) => {
        await postGoal({ monthlyGoalAmount: amount });

        // fetch goal
        const goal = await getGoal();
        setContext((prev) => ({ ...prev, goal }));
    };

    return (
        <AppContext.Provider
            value={{ ...context, setPopup, closePopup, updateGoal }}
        >
            {children}
        </AppContext.Provider>
    );
};

export const useAppContext = () => {
    const context = useContext(AppContext);
    if (!context) {
        throw new Error(
            translate('useAppContext must be used within a AppContextProvider'),
        );
    }
    return context;
};

export default AppContextProvider;
