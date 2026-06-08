import React, { KeyboardEvent, useEffect, useRef, useState } from 'react';
import { FaBars, FaCommentDots, FaPaperPlane, FaTrash } from 'react-icons/fa';
import { TbDots } from 'react-icons/tb';
import { translate } from '../../utils/translate';
import SearchForm from '../inputs/SearchForm';
import Spinner from '../loader/Spinner';
import SectionTitle from './SectionTitle';

import toast from 'react-hot-toast';
import { useSearchParams } from 'react-router-dom';
import { IConversation, IMessage } from '../../types';
import {
    getConversations,
    getMessages,
    sendMessage,
} from '../../utils/actions';
import { cn } from '../../utils/cn';
import Image from '../Image';
import Conversation from './Conversation';

const ChatHistory = () => {
    const [conversationBarOpen, setConversationBarOpen] = useState(true);
    const [searchParam, setSearchParam] = useSearchParams();
    const inputEl = useRef<HTMLTextAreaElement>(null);

    const [searchKey, setSearchKey] = useState<string>('');
    const [isSending, setIsSending] = useState(false);
    const [messages, setMessages] = useState<IMessage[]>([]);
    const [message, setMessage] = useState<string>('');

    const [conversations, setConversations] = useState<IConversation[]>([]);
    const [selectedConversation, setSelectedConversation] =
        useState<IConversation>();
    const conversationId = searchParam.get('conversationId');

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
        const conversationId = searchParam.get('conversationId');

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

    // get messages after every 4 seconds
    useEffect(() => {
        if (!conversationId) return;
        setConversationBarOpen(false);

        // fetch messages at first then after every 4 seconds
        getMessages(conversationId).then(setMessages);

        const interval = setInterval(async () => {
            getMessages(conversationId).then(setMessages);
        }, 4000);

        return () => clearInterval(interval);
    }, [conversationId]);

    // get conversation after every 8 seconds
    useEffect(() => {
        getConversations({ searchKey }).then(setConversations);

        const interval = setInterval(async () => {
            await getConversations({ searchKey }).then(setConversations);
        }, 8000);

        return () => clearInterval(interval);
    }, [searchKey]);

    const handleKeyDown = (event: KeyboardEvent<HTMLTextAreaElement>) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            handleSendMessage();
        }
    };

    const handleSendMessage = async () => {
        if (message.trim() === '' || !conversationId) return;

        try {
            setIsSending(true);
            const res = await sendMessage(+conversationId, message);

            setIsSending(false);
            setMessages(res);
            setMessage('');
            inputEl.current?.focus();
        } catch (error) {
            toast.error('Failed to send message');
        }
    };

    return (
        <div
            className="bg-background border border-border rounded-md relative overflow-hidden"
            id="my-orders"
        >
            <div className="px-4 md:px-8 pt-4 md:pt-10">
                <SectionTitle
                    icon={<FaCommentDots />}
                    title={translate('Chat History')}
                    connector
                    className=""
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
                        'max-md:absolute max-md:top-0 max-md:bottom-0 max-md:left-0 max-md:shadow-xl max-md:z-[2] bg-background md:col-span-1 border-r border-primary-14 flex flex-col transition-all',
                        {
                            'max-md:-translate-x-full': !conversationBarOpen,
                        },
                    )}
                >
                    <div className="px-6 pb-5 pt-7 border-b border-primary-14">
                        <SearchForm
                            value={searchKey}
                            onChange={(e) => setSearchKey(e.target.value)}
                            placeholder={translate('Search customer') + '...'}
                        />
                    </div>

                    <div className="grow overflow-auto max-h-[calc(100vh-300px)]">
                        {!conversations.length ? (
                            <p className="text-center mt-5">
                                {translate('No conversation found')}
                            </p>
                        ) : (
                            conversations.map((conversation) => (
                                <Conversation
                                    key={1}
                                    conversation={conversation}
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
                                className="text-base md:hidden text-muted"
                                onClick={() =>
                                    setConversationBarOpen(!conversationBarOpen)
                                }
                            >
                                <FaBars />
                            </button>
                            <div>
                                <Image
                                    src={selectedConversation?.userAvatar}
                                    alt={selectedConversation?.userName}
                                    width={52}
                                    height={52}
                                />
                            </div>
                            <div>
                                <h3 className="text-lg font-semibold">
                                    {selectedConversation?.userName}
                                </h3>
                            </div>
                        </div>

                        <div className="option-dropdown">
                            <button className="text-3xl text-muted option-dropdown__toggler no-style no-arrow">
                                <TbDots />
                            </button>

                            <div className="option-dropdown__options right-0 left-auto min-w-[170px]">
                                <ul>
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
                                        item.userId === +window.userId,
                                })}
                                key={item.id}
                            >
                                <div>
                                    <Image
                                        src={item.userAvatar}
                                        alt={item.userName}
                                        width={42}
                                        height={42}
                                        className="rounded-full w-[42px] aspect-square border border-border"
                                    />
                                </div>
                                <div className="max-w-[calc(100%-120px)] flex flex-col">
                                    <time className="text-muted text-xs mb-1">
                                        {item.messageAt}
                                    </time>
                                    <div className="text-sm bg-background-active px-5 py-3 rounded-md">
                                        {item.message}
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>

                    <form
                        className="border-t border-primary-14 relative"
                        onSubmit={handleSendMessage}
                    >
                        <textarea
                            name="message"
                            id="message"
                            rows={1}
                            value={message}
                            onChange={(e) => setMessage(e.target.value)}
                            placeholder={translate('Type your message')}
                            className="w-full pl-5 pr-14 py-5 resize-none"
                            ref={inputEl}
                            onKeyDown={handleKeyDown}
                        ></textarea>

                        <button
                            type="button"
                            className="absolute top-1/2 -translate-y-1/2 right-5 h-7 w-7 rounded-full flex items-center justify-center bg-secondary-light text-white border border-dashed border-transparent focus:border-black"
                            onClick={handleSendMessage}
                        >
                            {isSending ? <Spinner /> : <FaPaperPlane />}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    );
};

export default ChatHistory;
