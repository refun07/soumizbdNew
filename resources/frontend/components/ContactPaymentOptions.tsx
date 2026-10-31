import { FaFacebookMessenger, FaPhone, FaWhatsapp } from 'react-icons/fa';

export default function ContactPaymentOptions({
    className = '',
    showContactButtons = true,
}: {
    className?: string;
    showContactButtons?: boolean;
}) {
    const callPhone = window.config.topbarHotline?.replace(/[^+\d]/g, '') || '';

    // Handle WhatsApp - use floating chat config
    const getWhatsAppContacts = () => {
        const config = (window as any).config?.floatingChat || {};
        return (config.whatsapp_enabled
            ? [
                { phone: config.whatsapp_number, label: 'WhatsApp' },
                { phone: config.secondary_whatsapp_number, label: 'WhatsApp 2' },
            ]
            : []
        )
            .map((contact: { phone?: string; label: string }) => ({
                ...contact,
                phone: contact.phone?.replace(/\D/g, '') || '',
            }))
            .filter((contact: { phone: string; label: string }) => contact.phone);
    };

    const messengerUrl = window.config.floatingChat?.messenger_url || '';

    const whatsappContacts = getWhatsAppContacts();

    return (
        <div className={className}>
                {/* Contact Buttons Section */}
                {showContactButtons && (window.config?.floatingChat?.call_enabled ||
                    whatsappContacts.length > 0 ||
                    window.config?.floatingChat?.messenger_enabled) && (
                    <div className="bot-product-contact-options mt-3 space-y-2">
                        {/* Call Now Button */}
                        {window.config?.floatingChat?.call_enabled && callPhone && (
                            <a
                                href={`tel:${callPhone}`}
                                className="w-full flex items-center justify-center gap-2 h-10 md:h-12 rounded-lg font-medium text-white bg-[#2563EB] hover:bg-[#1D4ED8] transition-colors duration-200"
                            >
                                <FaPhone className="text-lg" />
                                <span>Call Now</span>
                            </a>
                        )}

                        {/* WhatsApp and Messenger Buttons */}
                        {(whatsappContacts.length > 0 ||
                            window.config?.floatingChat?.messenger_enabled) && (
                            <div className="grid grid-cols-1 gap-2">
                                {whatsappContacts.map((contact) => (
                                    <a
                                        key={contact.label}
                                        href={`https://wa.me/${contact.phone}`}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className="flex-1 flex items-center justify-center gap-2 h-10 md:h-12 px-4 rounded-lg border-2 border-[#25D366] text-[#25D366] bg-white hover:bg-[#25D366]/10 transition-colors duration-200 font-medium"
                                    >
                                        <FaWhatsapp className="text-lg" />
                                        <span>{contact.label}</span>
                                    </a>
                                ))}

                                {/* Messenger Button - Blue Outline */}
                                {window.config?.floatingChat
                                    ?.messenger_enabled && messengerUrl && (
                                    <a
                                        href={messengerUrl}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className="flex-1 flex items-center justify-center gap-2 h-10 md:h-12 px-4 rounded-lg border-2 border-[#0084FF] text-[#0084FF] bg-white hover:bg-[#0084FF]/10 transition-colors duration-200 font-medium"
                                    >
                                        <FaFacebookMessenger className="text-lg" />
                                        <span>Messenger</span>
                                    </a>
                                )}
                            </div>
                        )}
                    </div>
                )}
                <div className="bot-product-payment-methods mt-4 rounded-xl border border-gray-200 bg-white p-3">
                    <img
                        src="/images/storefront/payment-methods-sslcommerz.png"
                        alt="Payment methods through SSLCommerz, including Visa, Mastercard, bKash, Nagad and bank payments"
                        width="5235"
                        height="586"
                        loading="lazy"
                        className="block h-auto w-full"
                    />
                </div>
        </div>
    );
}
