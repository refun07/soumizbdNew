import React, { ReactNode, createContext, useContext, useState } from 'react';
import { IAppContextStore, IAppContextType } from './types';

export const AppContext = createContext<IAppContextType | null>(null);

const AppContextProvider: React.FC<{ children: ReactNode }> = ({
    children,
}) => {
    const [context, setContext] = useState<IAppContextStore>({
        fullPermission: false,
        databaseSetup: false,
        shopSettings: false,

        countries: [],
    });

    const setState = (newState: Partial<IAppContextStore>) => {
        setContext((prevState) => ({ ...prevState, ...newState }));
    };

    return (
        <AppContext.Provider value={{ ...context, setState }}>
            {children}
        </AppContext.Provider>
    );
};

export const useAppContext = () => {
    const context = useContext(AppContext);
    if (!context) {
        throw new Error(
            'useAppContext must be used within a AppContextProvider',
        );
    }
    return context;
};

export default AppContextProvider;
