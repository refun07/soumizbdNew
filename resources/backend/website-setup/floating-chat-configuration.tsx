import React from 'react';
import { createRoot } from 'react-dom/client';
import '../css/app.css';
import FloatingChatConfigurationApp from './floating-chat/components/FloatingChatConfigurationApp';

const FloatingChatConfiguration: React.FC = () => {
    return (
        <div className="pt-4 min-h-screen">
            <div className="bg-white py-4 rounded-md">
                <FloatingChatConfigurationApp />
            </div>
        </div>
    );
};

const container = document.getElementById('floating-chat-configuration-app');
if (container) {
    const root = createRoot(container);
    root.render(<FloatingChatConfiguration />);
}
