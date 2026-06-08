import React from 'react';
import { Route, Routes } from 'react-router-dom';
import ChatHistory from '../react/components/chat/ChatHistory';

const App = () => {
    return (
        <Routes>
            <Route index element={<ChatHistory />} />
        </Routes>
    );
};

export default App;
