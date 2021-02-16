import { useState, createContext } from '@wordpress/element';

export const IconsContext = createContext();

// This context provider is passed to any component requiring the context
export const IconsProvider = ({ children }) => {
    const [iconValue, setIconValue] = useState({});
    return (
        <IconsContext.Provider
        value={{
            iconValue,
            setIconValue,
        }}
        >
        {children}
        </IconsContext.Provider>
    );
};
