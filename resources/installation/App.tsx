import React from 'react';
import { Route, Routes } from 'react-router-dom';
import Layout from './components/Layout';
import Checklist from './pages/Checklist';
import Home from './pages/Home';
import Setup from './pages/Setup';
import Success from './pages/Success';

const App = () => {
    return (
        <Routes>
            <Route element={<Layout />}>
                <Route path="/" element={<Home />} />
                <Route path="/checklist" element={<Checklist />} />
                <Route path="/setup" element={<Setup />} />
                <Route path="/success" element={<Success />} />
            </Route>
        </Routes>
    );
};

export default App;
