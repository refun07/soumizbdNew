import React, { useState, useRef, useEffect } from 'react';
import { FaWhatsapp, FaFacebookMessenger, FaTiktok, FaCommentDots } from 'react-icons/fa';

interface FloatingChatConfig {
    whatsapp_enabled: boolean;
    whatsapp_number: string;
    secondary_whatsapp_number: string;
    messenger_enabled: boolean;
    messenger_url: string;
    tiktok_enabled: boolean;
    tiktok_url: string;
}

const FloatingChatButtons: React.FC = () => {
    const [isOpen, setIsOpen] = useState(false);
    const panelRef = useRef<HTMLDivElement>(null);

    const config: FloatingChatConfig = (window as any).config?.floatingChat || {
        whatsapp_enabled: false,
        whatsapp_number: '',
        secondary_whatsapp_number: '',
        messenger_enabled: false,
        messenger_url: '',
        tiktok_enabled: false,
        tiktok_url: ''
    };

    const whatsappContacts = config.whatsapp_enabled
        ? [
            { number: config.whatsapp_number, label: 'WhatsApp' },
            { number: config.secondary_whatsapp_number, label: 'WhatsApp 2' }
        ].filter((contact) => contact.number?.trim())
        : [];

    const hasAnyEnabled = whatsappContacts.length > 0 ||
        config.messenger_enabled ||
        config.tiktok_enabled;

    useEffect(() => {
        const handleClickOutside = (event: MouseEvent) => {
            if (panelRef.current && !panelRef.current.contains(event.target as Node)) {
                setIsOpen(false);
            }
        };

        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    const getWhatsAppUrl = (number: string) => {
        const phone = number?.replace(/\D/g, '') || '';
        return `https://wa.me/${phone}`;
    };

    if (!hasAnyEnabled) {
        return null;
    }

    return (
        <>
            <div
                ref={panelRef}
                className="fixed bottom-20 right-6 z-[100] flex flex-col items-end lg:bottom-6"
            >
                <div
                    className={`flex flex-col gap-3 mb-4 transition-all duration-300 ease-in-out ${
                        isOpen
                            ? 'opacity-100 transform translate-y-0'
                            : 'opacity-0 transform translate-y-4 pointer-events-none'
                    }`}
                >
                    {config.tiktok_enabled && config.tiktok_url && (
                        <a
                            href={config.tiktok_url}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="w-12 h-12 bg-black text-white rounded-full flex items-center justify-center shadow-lg hover:bg-gray-800 hover:scale-110 transition-all duration-200"
                            title="TikTok"
                        >
                            <FaTiktok className="text-xl" />
                        </a>
                    )}

                    {config.messenger_enabled && config.messenger_url && (
                        <a
                            href={config.messenger_url}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="w-12 h-12 bg-[#0084FF] text-white rounded-full flex items-center justify-center shadow-lg hover:bg-[#0073E6] hover:scale-110 transition-all duration-200"
                            title="Messenger"
                        >
                            <FaFacebookMessenger className="text-xl" />
                        </a>
                    )}

                    {whatsappContacts.map((contact, index) => (
                        <a
                            key={contact.label}
                            href={getWhatsAppUrl(contact.number)}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="relative w-12 h-12 bg-[#25D366] text-white rounded-full flex items-center justify-center shadow-lg hover:bg-[#20BD5A] hover:scale-110 transition-all duration-200"
                            title={contact.label}
                        >
                            <FaWhatsapp className="text-xl" />
                            {whatsappContacts.length > 1 && (
                                <span className="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-white text-[#25D366] text-[10px] font-semibold flex items-center justify-center">
                                    {index + 1}
                                </span>
                            )}
                        </a>
                    ))}
                </div>

                <button
                    onClick={() => setIsOpen(!isOpen)}
                    className={`w-14 h-14 bg-primary text-white rounded-full flex items-center justify-center shadow-lg transition-all duration-300 ${
                        isOpen ? 'hover:bg-primary/90 rotate-90' : 'hover:bg-primary/90'
                    }`}
                >
                    {isOpen ? (
                        <span className="text-2xl">✕</span>
                    ) : (
                        <FaCommentDots className="text-2xl" />
                    )}
                </button>
            </div>

            {isOpen && (
                <div
                    className="fixed inset-0 z-[99]"
                    onClick={() => setIsOpen(false)}
                />
            )}

        </>
    );
};

export default FloatingChatButtons;
