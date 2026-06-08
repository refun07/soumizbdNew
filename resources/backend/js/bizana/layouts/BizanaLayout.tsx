import { useState, ReactNode } from 'react';
import { Sidebar } from '../components/Sidebar';
import { IoNotifications, IoCart, IoChevronDown } from 'react-icons/io5';

interface BizanaLayoutProps {
    children: ReactNode;
    title?: string;
}

export default function BizanaLayout({ children, title }: BizanaLayoutProps) {
    const [sidebarCollapsed, setSidebarCollapsed] = useState(false);

    return (
        <div className="flex flex-col min-h-svh bg-gray-50">
            {/* Header */}
            <div className="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
                <div className="flex items-center justify-between">
                    <div className="flex items-center space-x-4">
                        <button
                            onClick={() =>
                                setSidebarCollapsed(!sidebarCollapsed)
                            }
                            className="p-2 rounded-md hover:bg-gray-100 transition-colors"
                            title={
                                sidebarCollapsed
                                    ? 'Expand sidebar'
                                    : 'Collapse sidebar'
                            }
                        >
                            <svg
                                className="w-6 h-6 text-gray-600"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth={2}
                                    d="M4 6h16M4 12h16M4 18h16"
                                />
                            </svg>
                        </button>
                        <div>
                            <img
                                src="/images/epik_cart.png"
                                alt="Epik Cart"
                                className="h-8"
                            />
                        </div>
                        <div className="pl-48">
                            <div className="relative">
                                <input
                                    type="text"
                                    placeholder="Search Options"
                                    className="w-96 pl-4 pr-10 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                />
                                <div className="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg
                                        className="w-5 h-5 text-gray-400"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            strokeWidth={2}
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                        />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="flex items-center space-x-4">
                        {/* Add New Button */}
                        <button className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center space-x-2">
                            <span>ADD NEW</span>
                            <IoChevronDown className="w-4 h-4" />
                        </button>

                        {/* Notification Bell */}
                        <div className="relative">
                            <button className="p-2 text-gray-600 hover:text-gray-800 transition-colors">
                                <IoNotifications className="w-6 h-6" />
                            </button>
                            {/* Notification Count */}
                            <span className="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                1
                            </span>
                        </div>

                        {/* Profile Menu */}
                        <div className="flex items-center space-x-3 pl-2">
                            <div className="text-right">
                                <div className="text-sm font-medium text-gray-900">
                                    Angeline Jolie
                                </div>
                                <div className="text-xs text-gray-500">
                                    admin
                                </div>
                            </div>
                            <img
                                src="https://ui-avatars.com/api/?name=Angeline+Jolie&background=3b82f6&color=fff"
                                alt="Profile"
                                className="w-8 h-8 rounded-full"
                            />
                        </div>
                    </div>
                </div>
            </div>

            {/* Main Content Area */}
            <div className="flex flex-1 overflow-hidden bg-blue-50 pt-4">
                {/* Sidebar */}
                <Sidebar isCollapsed={sidebarCollapsed} />

                {/* Main Content */}
                <main className="flex-1 overflow-y-auto mt-4 ml-14 max-w-7xl mx-auto">
                    <div>
                        <h1 className="text-xl font-semibold mb-4">{title || 'Dashboard'}</h1>
                        <div>{children}</div>
                    </div>
                </main>
            </div>
        </div>
    );
}
