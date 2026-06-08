import { KeyboardEvent, useEffect, useRef, useState } from 'react';
import toast from 'react-hot-toast';
import { FaBars, FaPaperPlane, FaTrash } from 'react-icons/fa';
import { FaShop } from 'react-icons/fa6';
import { TbDots } from 'react-icons/tb';
import { Link, useSearchParams } from 'react-router-dom';
import Image from '../../components/Image';
import ChatSearchForm from '../../components/chat/ChatSearchForm';
import Conversation from '../../components/chat/Conversation';
import Spinner from '../../components/loader/Spinner';
import SectionTitle from '../../components/titles/SectionTitle';
import {
    useLazyGetConversationsQuery,
    useLazyGetMessagesQuery,
    useSendMessageMutation,
} from '../../store/features/api/chatApi';
import { useAuth } from '../../store/features/auth/authSlice';
import { IConversation, IMessage } from '../../types/chat';
import { cn } from '../../utils/cn';
import { translate } from '../../utils/translate';

const ChatHistory = () => {
    const [conversationBarOpen, setConversationBarOpen] = useState(true);
    const { user } = useAuth();
    const inputEl = useRef<HTMLTextAreaElement>(null);
    const [searchParam, setSearchParam] = useSearchParams();
    const [
        getConversations,
        { data: conversations = [], isLoading: loadingConversation },
    ] = useLazyGetConversationsQuery();
    const [getMessages] = useLazyGetMessagesQuery();
    const [sendMessage, { isLoading }] = useSendMessageMutation();

    const [searchKey, setSearchKey] = useState<string>('');
    const [messages, setMessages] = useState<IMessage[]>([]);
    const [message, setMessage] = useState<string>('');
    const [selectedConversation, setSelectedConversation] =
        useState<IConversation>();

    const conversationId = searchParam.get('conversationId');

    const fetchMessages = async () => {
        if (!conversationId) return;

        return await getMessages({
            conversationId: +conversationId,
        })
            .unwrap()
            .then(setMessages);
    };

    // get messages after every 4 seconds
    useEffect(() => {
        if (!conversationId) return;
        setConversationBarOpen(false);

        fetchMessages();

        const interval = setInterval(async () => {
            await fetchMessages();
        }, 4000);

        return () => clearInterval(interval);
    }, [conversationId]);

    // get conversation after every 8 seconds
    useEffect(() => {
        getConversations({ searchKey });

        const interval = setInterval(async () => {
            await getConversations({ searchKey });
        }, 8000);

        return () => clearInterval(interval);
    }, [searchKey]);

    // auto resize textarea
    useEffect(() => {
        if (inputEl.current) {
            inputEl.current.style.height = 'auto';
            inputEl.current.style.height = `${Math.min(
                inputEl.current.scrollHeight,
                150,
            )}px`;
        }
    }, [message]);

    // set selected conversation from url
    useEffect(() => {
        if (conversationId) {
            const conversation = conversations.find(
                (item) => item.id === +conversationId,
            );
            setSelectedConversation(conversation);
        } else if (conversations.length) {
            searchParam.set('conversationId', conversations?.[0].id.toString());
            setSearchParam(searchParam);
        }
    }, [searchParam, conversations]);

    const handleKeyDown = (event: KeyboardEvent<HTMLTextAreaElement>) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            handleSendMessage();
        }
    };

    const handleSendMessage = async () => {
        if (message.trim() === '' || !conversationId) return;

        try {
            const res = await sendMessage({
                chatId: +conversationId,
                message: message,
            }).unwrap();

            setMessages(res);
            setMessage('');
            inputEl.current?.focus();
        } catch (error) {
            toast.error('Failed to send message');
        }
    };

    return (
        <div
            className="border rounded-card relative overflow-hidden"
            id="my-orders"
        >
            <div className="px-4 md:px-8 pt-4 md:pt-10">
                <SectionTitle
                    title={translate('[Chat] History')}
                    classNames={{
                        title: 'ek-h3',
                    }}
                />
            </div>

            <div className="grid md:grid-cols-3 gap-0 -mt-8">
                <div
                    className={cn('overlay absolute md:hidden', {
                        active: conversationBarOpen,
                    })}
                    onClick={() => setConversationBarOpen(false)}
                ></div>

                <div
                    className={cn(
                        'max-md:absolute max-md:top-0 max-md:bottom-0 max-md:left-0 max-md:shadow-xl max-md:z-[2] bg-white md:col-span-1 border-r border-primary-14 flex flex-col transition-all',
                        {
                            'max-md:-translate-x-full': !conversationBarOpen,
                        },
                    )}
                >
                    <div className="px-6 pb-5 pt-7 border-b border-primary-14">
                        <ChatSearchForm
                            value={searchKey}
                            onChange={(e) => setSearchKey(e.target.value)}
                            placeholder={
                                translate('Search conversation') + '...'
                            }
                        />
                    </div>

                    <div className="grow overflow-auto max-h-[calc(100vh-300px)]">
                        {loadingConversation ? (
                            <></>
                        ) : !conversations.length ? (
                            <p className="text-center mt-5">
                                {translate('No conversation found')}
                            </p>
                        ) : (
                            conversations!.map((item) => (
                                <Conversation
                                    key={item.id}
                                    conversation={item}
                                />
                            ))
                        )}
                    </div>
                </div>
                <div className="md:col-span-2 flex flex-col">
                    <div
                        className={cn(
                            'px-6 pb-3 pt-6 border-b border-primary-14 flex items-center justify-between',
                            {
                                'opacity-0': !selectedConversation,
                            },
                        )}
                    >
                        <div className="flex items-center gap-3">
                            <button
                                className="md:hidden text-black/70"
                                onClick={() =>
                                    setConversationBarOpen(!conversationBarOpen)
                                }
                            >
                                <FaBars />
                            </button>

                            <div>
                                <Image
                                    src={selectedConversation?.shopLogo}
                                    alt={selectedConversation?.shopName}
                                    width={52}
                                    height={52}
                                />
                            </div>
                            <div>
                                <h3 className="text-lg font-semibold">
                                    {selectedConversation?.shopName}
                                </h3>
                            </div>
                        </div>

                        <div className="option-dropdown">
                            <button className="text-3xl text-black/70 option-dropdown__toggler no-style no-arrow">
                                <TbDots />
                            </button>

                            <div className="option-dropdown__options right-0 left-auto min-w-[170px]">
                                <ul>
                                    {window.config.generalSettings.appMode ===
                                        'multiVendor' && (
                                        <li>
                                            <Link
                                                to={`/shops/${selectedConversation?.shopSlug}`}
                                                className="option-dropdown__option rounded-none"
                                            >
                                                <span className="text-lg text-stone-300">
                                                    <FaShop />
                                                </span>
                                                <span>
                                                    {translate('Seller Page')}
                                                </span>
                                            </Link>
                                        </li>
                                    )}
                                    <li>
                                        <button
                                            className="option-dropdown__option rounded-none"
                                            onClick={() => {
                                                // delete chat
                                            }}
                                        >
                                            <span className="text-lg text-red-400">
                                                <FaTrash />
                                            </span>
                                            <span className="text-red-400">
                                                {translate('Delete Chat')}
                                            </span>
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {/* messages */}
                    <div className="grow px-6 py-4 gap-8 flex flex-col-reverse max-h-[calc(100vh-330px)] min-h-[300px] overflow-y-auto">
                        {messages.map((item) => (
                            <div
                                className={cn('flex gap-5', {
                                    'flex-row-reverse':
                                        item.userId === user?.id,
                                })}
                                key={item.id}
                            >
                                <div>
                                    <Image
                                        src={item.userAvatar}
                                        alt={item.userName}
                                        width={42}
                                        height={42}
                                        className="rounded-full w-[42px] aspect-square border border-zinc-100"
                                    />
                                </div>
                                <div className="max-w-[calc(100%-120px)] flex flex-col">
                                    <time className="text-black/70 text-sm mb-1">
                                        {item.messageAt}
                                    </time>
                                    <div className="text-black/70 bg-neutral-100 px-5 py-3 rounded-card ">
                                        {item.message}
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>

                    <form className="border-t border-primary-14 relative">
                        <textarea
                            name="message"
                            id="message"
                            rows={1}
                            value={message}
                            onChange={(e) => setMessage(e.target.value)}
                            placeholder={translate('Type your message') + '...'}
                            className="w-full pl-5 pr-14 py-5 resize-none"
                            ref={inputEl}
                            onKeyDown={handleKeyDown}
                        ></textarea>

                        <button
                            type="button"
                            className="absolute top-1/2 -translate-y-1/2 right-5 h-7 w-7 rounded-full flex items-center justify-center bg-secondary text-white border border-dashed border-transparent focus:border-black"
                            onClick={handleSendMessage}
                        >
                            {isLoading ? <Spinner /> : <FaPaperPlane />}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    );
};

export default ChatHistory;
