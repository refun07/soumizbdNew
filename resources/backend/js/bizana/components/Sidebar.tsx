import { useState, useEffect } from 'react';
import { Link, usePage } from '@inertiajs/react';
import {
    MdDashboard,
    MdShoppingBag,
    MdReceipt,
    MdTrendingUp,
    MdGroup,
    MdInventory2,
    MdExpandMore,
    MdExpandLess,
    MdAccountBalance,
    MdMonetizationOn,
    MdAssessment,
    MdCategory,
    MdReceiptLong,
    MdRepeat,
    MdAttachMoney,
} from 'react-icons/md';

export interface SidebarProps {
    isCollapsed: boolean;
}

interface NavigationItem {
    id: string;
    label: string;
    icon: React.ComponentType<{ size?: number; className?: string }>;
    href: string;
    subItems?: {
        id: string;
        label: string;
        href: string;
        icon: React.ComponentType<{ size?: number; className?: string }>;
    }[];
}

export function Sidebar({ isCollapsed }: SidebarProps) {
    const [activeSection, setActiveSection] = useState<string>('dashboard');
    const { url } = usePage();

    // Auto-detect active section based on current URL
    useEffect(() => {
        const getActiveSection = () => {
            // Remove query parameters for URL matching
            const urlWithoutQuery = url.split('?')[0];

            if (urlWithoutQuery === '/admin/bizana') return 'dashboard';
            if (urlWithoutQuery === '/admin/bizana/products/price-calculator') return 'products';
            if (urlWithoutQuery === '/admin/bizana/monthly-expense') return 'monthly-expense';
            if (urlWithoutQuery === '/admin/bizana/daily-expenses') return 'daily-expenses';
            if (url.includes('/admin/bizana/accounting')) return 'accounting';
            if (url.includes('/admin/bizana/order-profits')) return 'order-profits';
            // Check other potential routes here
            return 'dashboard';
        };

        const activeSection = getActiveSection();
        setActiveSection(activeSection);

        // Auto-expand parent section when its sub-item is active
        if (activeSection && (activeSection === 'products' || activeSection === 'accounting')) {
            setExpandedSections(prev =>
                prev.includes(activeSection) ? prev : [...prev, activeSection]
            );
        }
    }, [url]);

    const navigationItems: NavigationItem[] = [
        {
            id: 'dashboard',
            label: 'Dashboard',
            icon: MdDashboard,
            href: '/admin/bizana',
        },
        {
            id: 'products',
            label: 'Products',
            icon: MdShoppingBag,
            href: '#products',
            subItems: [
                {
                    id: 'products-list',
                    label: 'Products',
                    href: '#products-list',
                    icon: MdInventory2,
                },
                {
                    id: 'sales',
                    label: 'Sales',
                    href: '#sales',
                    icon: MdTrendingUp,
                },
                {
                    id: 'refunds',
                    label: 'Refunds',
                    href: '#refunds',
                    icon: MdReceipt,
                },
                {
                    id: 'customers',
                    label: 'Customers',
                    href: '#customers',
                    icon: MdGroup,
                },
                {
                    id: 'price-calculator',
                    label: 'Price Calculator',
                    href: '/admin/bizana/products/price-calculator',
                    icon: MdTrendingUp,
                },
            ],
        },
        {
            id: 'orders',
            label: 'Orders',
            icon: MdReceipt,
            href: '#orders',
        },
        {
            id: 'sales',
            label: 'Sales',
            icon: MdTrendingUp,
            href: '#sales',
        },
        {
            id: 'order-profits',
            label: 'Order Profits',
            icon: MdAttachMoney,
            href: '/admin/bizana/order-profits',
        },
        {
            id: 'refunds',
            label: 'Refunds',
            icon: MdReceipt,
            href: '#refunds',
        },
        {
            id: 'customers',
            label: 'Customers',
            icon: MdGroup,
            href: '#customers',
        },
        {
            id: 'monthly-expense',
            label: 'Monthly Expense',
            icon: MdMonetizationOn,
            href: '/admin/bizana/monthly-expense',
        },
        {
            id: 'daily-expenses',
            label: 'Daily Expenses',
            icon: MdReceipt,
            href: '/admin/bizana/daily-expenses',
        },
        {
            id: 'accounting',
            label: 'Accounting',
            icon: MdAccountBalance,
            href: '#accounting',
            subItems: [
                {
                    id: 'accounting-dashboard',
                    label: 'Dashboard',
                    href: '/admin/bizana/accounting',
                    icon: MdDashboard,
                },
                {
                    id: 'chart-of-accounts',
                    label: 'Chart of Accounts',
                    href: '/admin/bizana/accounting/chart-of-accounts',
                    icon: MdMonetizationOn,
                },
                {
                    id: 'financial-periods',
                    label: 'Financial Periods',
                    href: '/admin/bizana/accounting/financial-periods',
                    icon: MdReceiptLong,
                },
                {
                    id: 'tax-settings',
                    label: 'Tax Settings',
                    href: '/admin/bizana/accounting/tax-settings',
                    icon: MdMonetizationOn,
                },
                {
                    id: 'monthly-expenses',
                    label: 'Recurring Expenses',
                    href: '/admin/bizana/accounting/monthly-expenses',
                    icon: MdRepeat,
                },
                {
                    id: 'system-diagnostics',
                    label: 'System Diagnostics',
                    href: '/admin/bizana/accounting/system-diagnostics',
                    icon: MdAssessment,
                },
            ],
        },
    ];

    const [expandedSections, setExpandedSections] = useState<string[]>([]);

    const toggleSection = (sectionId: string) => {
        setExpandedSections((prev) =>
            prev.includes(sectionId)
                ? prev.filter((id) => id !== sectionId)
                : [...prev, sectionId],
        );
    };

    return (
        <div
            className={`bg-white shadow-lg transition-all duration-300 ${
                isCollapsed ? 'w-16' : 'w-64'
            } flex flex-col`}
        >
            {/* Navigation Menu */}
            <nav className="flex-1 overflow-y-auto p-2">
                <ul className="space-y-1">
                    {navigationItems.map((item) => (
                        <li key={item.id}>
                            {item.subItems ? (
                                <div className="mb-2">
                                    <div
                                        className={`flex items-center justify-between p-3 rounded-md cursor-pointer transition-colors ${
                                            activeSection === item.id
                                                ? 'bg-blue-600 text-white'
                                                : 'text-gray-700 hover:bg-gray-100'
                                        }`}
                                        onClick={() => {
                                            setActiveSection(item.id);
                                            if (!isCollapsed) {
                                                toggleSection(item.id);
                                            }
                                        }}
                                        title={
                                            isCollapsed ? item.label : undefined
                                        }
                                    >
                                        <div className="flex items-center">
                                            <item.icon
                                                size={18}
                                                className="mr-3"
                                            />
                                            {!isCollapsed && (
                                                <span className="font-medium">
                                                    {item.label}
                                                </span>
                                            )}
                                        </div>
                                        {!isCollapsed &&
                                            (expandedSections.includes(
                                                item.id,
                                            ) ? (
                                                <MdExpandLess size={16} />
                                            ) : (
                                                <MdExpandMore size={16} />
                                            ))}
                                    </div>

                                    {/* Sub-menu */}
                                    {!isCollapsed &&
                                        item.subItems &&
                                        expandedSections.includes(item.id) && (
                                            <ul className="mt-1 space-y-1 bg-gray-100 rounded-md py-2 pr-2">
                                                {item.subItems.map(
                                                    (subItem) => (
                                                        <li
                                                            key={subItem.id}
                                                            className="border-b border-gray-200 last:border-b-0"
                                                        >
                                                            <Link
                                                                href={
                                                                    subItem.href
                                                                }
                                                                className={`flex items-center p-3 ml-0 text-sm rounded-md transition-colors ${
                                                                    url.split('?')[0] === subItem.href
                                                                        ? 'bg-blue-100 text-blue-600 border-r-2 border-blue-600'
                                                                        : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600'
                                                                }`}
                                                            >
                                                                <subItem.icon
                                                                    size={16}
                                                                    className="mr-3"
                                                                />
                                                                {subItem.label}
                                                            </Link>
                                                        </li>
                                                    ),
                                                )}
                                            </ul>
                                        )}
                                </div>
                            ) : (
                                <Link
                                    href={item.href}
                                    className={`flex items-center p-3 rounded-md cursor-pointer transition-colors mb-2 ${
                                        activeSection === item.id
                                            ? 'bg-blue-600 text-white'
                                            : 'text-gray-700 hover:bg-gray-100'
                                    }`}
                                    onClick={() => setActiveSection(item.id)}
                                    title={isCollapsed ? item.label : undefined}
                                >
                                    <div className="flex items-center">
                                        <item.icon size={18} className="mr-3" />
                                        {!isCollapsed && (
                                            <span className="font-medium">
                                                {item.label}
                                            </span>
                                        )}
                                    </div>
                                </Link>
                            )}
                        </li>
                    ))}
                </ul>
            </nav>

            {/* Sidebar Footer */}
            {/*{!isCollapsed && (
                <div className="p-4 border-t border-gray-200">
                    <div className="text-xs text-gray-500 text-center">
                        Analytics Dashboard v1.0
                    </div>
                </div>
            )}*/}
        </div>
    );
}
