import React from 'react';
import { useSearchParams } from 'react-router-dom';
import { IConversation } from '../../types';
import { cn } from '../../utils/cn';
import Image from '../Image';

interface Props {
    conversation: IConversation;
}

const Conversation = ({ conversation }: Props) => {
    const [searchParam, setSearchParam] = useSearchParams();
    const conversationId = searchParam.get('conversationId');

    const handleConversationClick = () => {
        searchParam.set('conversationId', conversation.id.toString());
        setSearchParam(searchParam);
    };

    return (
        <div
            className={cn(
                'flex items-center gap-5 border-b border-primary-14 px-6 py-4 cursor-pointer hover:bg-background-hover transition-all relative',
                {
                    'bg-background-active':
                        conversationId === conversation.id.toString(),
                },
            )}
            onClick={handleConversationClick}
        >
            <div>
                <Image
                    src={conversation.userAvatar}
                    alt={conversation.userName}
                    width={52}
                    height={52}
                    className="rounded-full border border-border"
                />
            </div>
            <div>
                <h5 className="mb-1 text-foreground">
                    {conversation.userName}
                </h5>
                <p className="text-muted">{conversation.lastMessageAt}</p>
            </div>

            {conversation.unseenMessageCounter > 0 && (
                <span className="absolute top-1/2 right-3 -translate-y-1/2 bg-theme-alert text-white h-5 min-w-[20px] rounded-full flex items-center justify-center">
                    {conversation.unseenMessageCounter}
                </span>
            )}
        </div>
    );
};

export default Conversation;
