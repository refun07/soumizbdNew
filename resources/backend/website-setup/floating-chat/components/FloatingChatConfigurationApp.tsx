import React, { useState, useEffect } from 'react';
import { useTranslation } from 'react-i18next';
import ToastContainer from '../../theme/components/Toast';
import useToast from '../../theme/hooks/useToast';

interface FloatingChatConfig {
    call_enabled: boolean;
    whatsapp_enabled: boolean;
    whatsapp_number: string;
    secondary_whatsapp_number: string;
    messenger_enabled: boolean;
    messenger_url: string;
    tiktok_enabled: boolean;
    tiktok_url: string;
}

const FloatingChatConfigurationApp: React.FC = () => {
    const { t } = useTranslation();
    const [callEnabled, setCallEnabled] = useState(false);
    const [whatsappEnabled, setWhatsappEnabled] = useState(false);
    const [whatsappNumber, setWhatsappNumber] = useState('');
    const [secondaryWhatsappNumber, setSecondaryWhatsappNumber] = useState('');
    const [messengerEnabled, setMessengerEnabled] = useState(false);
    const [messengerUrl, setMessengerUrl] = useState('');
    const [tiktokEnabled, setTiktokEnabled] = useState(false);
    const [tiktokUrl, setTiktokUrl] = useState('');
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);
    const { toasts, removeToast, success, error } = useToast();

    const apiPrefix = (window as any).floatingChatConfig?.apiPrefix || '/admin/api/floating-chat';

    useEffect(() => {
        const loadConfig = async () => {
            try {
                const response = await fetch(`${apiPrefix}/config`);
                const result = await response.json();

                if (result.success) {
                    setCallEnabled(Boolean(result.data.call_enabled));
                    setWhatsappEnabled(Boolean(result.data.whatsapp_enabled));
                    setWhatsappNumber(result.data.whatsapp_number || '');
                    setSecondaryWhatsappNumber(result.data.secondary_whatsapp_number || '');
                    setMessengerEnabled(Boolean(result.data.messenger_enabled));
                    setMessengerUrl(result.data.messenger_url || '');
                    setTiktokEnabled(Boolean(result.data.tiktok_enabled));
                    setTiktokUrl(result.data.tiktok_url || '');
                } else {
                    error('Failed to load floating chat configuration');
                }
            } catch (err) {
                error('Error loading floating chat configuration');
            } finally {
                setLoading(false);
            }
        };

        loadConfig();
    }, [apiPrefix]);

    const handleSave = async () => {
        setSaving(true);
        try {
            const response = await fetch(`${apiPrefix}/config`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || ''
                },
                body: JSON.stringify({
                    call_enabled: callEnabled,
                    whatsapp_enabled: whatsappEnabled,
                    whatsapp_number: whatsappNumber,
                    secondary_whatsapp_number: secondaryWhatsappNumber,
                    messenger_enabled: messengerEnabled,
                    messenger_url: messengerUrl,
                    tiktok_enabled: tiktokEnabled,
                    tiktok_url: tiktokUrl
                })
            });

            const result = await response.json();

            if (result.success) {
                success('Floating chat configuration saved successfully');
            } else {
                error(result.message || 'Failed to save configuration');
            }
        } catch (err) {
            error('Error saving floating chat configuration');
        } finally {
            setSaving(false);
        }
    };

    if (loading) {
        return (
            <div className="flex items-center justify-center h-64">
                <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
            </div>
        );
    }

    return (
        <div className="p-6">
            <ToastContainer toasts={toasts} onRemove={removeToast} />

            <div className="mb-8">
                <h2 className="text-lg font-semibold mb-4">{t('Floating Chat Buttons')}</h2>
                <p className="text-sm text-muted">{t('Configure the floating chat buttons that appear on your website.')}</p>
            </div>

            <div className="space-y-8">
                {/* Call Now Section */}
                <div className="bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <div className="flex items-center justify-between">
                        <div className="flex items-center gap-3">
                            <div className="w-10 h-10 bg-[#2563EB] rounded-full flex items-center justify-center">
                                <span className="text-white text-lg font-bold">
                                    <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                    </svg>
                                </span>
                            </div>
                            <div>
                                <h3 className="font-medium">{t('Call Now')}</h3>
                                <p className="text-sm text-muted">{t('Enable Call Now button on product pages')}</p>
                            </div>
                        </div>
                        <label className="relative inline-flex items-center cursor-pointer">
                            <input
                                type="checkbox"
                                checked={callEnabled}
                                onChange={(e) => setCallEnabled(e.target.checked)}
                                className="sr-only peer"
                            />
                            <div className="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                        </label>
                    </div>
                    <p className="text-xs text-muted mt-3 ml-13">{t('Uses the hotline number from General Settings')}</p>
                </div>

                <div className="bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <div className="flex items-center justify-between mb-4">
                        <div className="flex items-center gap-3">
                            <div className="w-10 h-10 bg-[#25D366] rounded-full flex items-center justify-center">
                                <span className="text-white text-lg font-bold">W</span>
                            </div>
                            <div>
                                <h3 className="font-medium">{t('WhatsApp')}</h3>
                                <p className="text-sm text-muted">{t('Enable WhatsApp chat button')}</p>
                            </div>
                        </div>
                        <label className="relative inline-flex items-center cursor-pointer">
                            <input
                                type="checkbox"
                                checked={whatsappEnabled}
                                onChange={(e) => setWhatsappEnabled(e.target.checked)}
                                className="sr-only peer"
                            />
                            <div className="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                        </label>
                    </div>

                    {whatsappEnabled && (
                        <div className="ml-13">
                            <label className="block text-sm font-medium mb-2">{t('Phone Number')}</label>
                            <input
                                type="text"
                                value={whatsappNumber}
                                onChange={(e) => setWhatsappNumber(e.target.value)}
                                placeholder="e.g. 8801961177726"
                                className="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                            />
                            <p className="text-xs text-muted mt-1">{t('Enter phone number with country code, without + sign')}</p>

                            <label className="block text-sm font-medium mb-2 mt-4">{t('Secondary Phone Number')}</label>
                            <input
                                type="text"
                                value={secondaryWhatsappNumber}
                                onChange={(e) => setSecondaryWhatsappNumber(e.target.value)}
                                placeholder="e.g. 8801961177726"
                                className="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                            />
                            <p className="text-xs text-muted mt-1">{t('Optional: add a second WhatsApp number with country code, without + sign')}</p>
                        </div>
                    )}
                </div>

                <div className="bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <div className="flex items-center justify-between mb-4">
                        <div className="flex items-center gap-3">
                            <div className="w-10 h-10 bg-[#0084FF] rounded-full flex items-center justify-center">
                                <span className="text-white text-lg font-bold">M</span>
                            </div>
                            <div>
                                <h3 className="font-medium">{t('Messenger')}</h3>
                                <p className="text-sm text-muted">{t('Enable Facebook Messenger chat button')}</p>
                            </div>
                        </div>
                        <label className="relative inline-flex items-center cursor-pointer">
                            <input
                                type="checkbox"
                                checked={messengerEnabled}
                                onChange={(e) => setMessengerEnabled(e.target.checked)}
                                className="sr-only peer"
                            />
                            <div className="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                        </label>
                    </div>

                    {messengerEnabled && (
                        <div className="ml-13">
                            <label className="block text-sm font-medium mb-2">{t('Messenger Page URL')}</label>
                            <input
                                type="url"
                                value={messengerUrl}
                                onChange={(e) => setMessengerUrl(e.target.value)}
                                placeholder="e.g. https://m.me/yourpage"
                                className="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                            />
                            <p className="text-xs text-muted mt-1">{t('Enter your Facebook Messenger page URL')}</p>
                        </div>
                    )}
                </div>

                <div className="bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <div className="flex items-center justify-between mb-4">
                        <div className="flex items-center gap-3">
                            <div className="w-10 h-10 bg-black rounded-full flex items-center justify-center">
                                <span className="text-white text-lg font-bold">T</span>
                            </div>
                            <div>
                                <h3 className="font-medium">{t('TikTok')}</h3>
                                <p className="text-sm text-muted">{t('Enable TikTok profile link button')}</p>
                            </div>
                        </div>
                        <label className="relative inline-flex items-center cursor-pointer">
                            <input
                                type="checkbox"
                                checked={tiktokEnabled}
                                onChange={(e) => setTiktokEnabled(e.target.checked)}
                                className="sr-only peer"
                            />
                            <div className="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                        </label>
                    </div>

                    {tiktokEnabled && (
                        <div className="ml-13">
                            <label className="block text-sm font-medium mb-2">{t('TikTok Profile URL')}</label>
                            <input
                                type="url"
                                value={tiktokUrl}
                                onChange={(e) => setTiktokUrl(e.target.value)}
                                placeholder="e.g. https://tiktok.com/@yourusername"
                                className="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                            />
                            <p className="text-xs text-muted mt-1">{t('Enter your TikTok profile URL')}</p>
                        </div>
                    )}
                </div>
            </div>

            <div className="mt-8 flex justify-end">
                <button
                    onClick={handleSave}
                    disabled={saving}
                    className="px-6 py-2 bg-primary text-white rounded-md hover:bg-primary/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                >
                    {saving && (
                        <svg className="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                            <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    )}
                    {t('Save Configuration')}
                </button>
            </div>
        </div>
    );
};

export default FloatingChatConfigurationApp;
