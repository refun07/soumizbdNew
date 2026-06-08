import { useSearchParams } from 'react-router-dom';
import { IConversation } from '../../types/chat';
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
                'flex items-center gap-5 border-b px-6 py-4 cursor-pointer hover:bg-secondary/5 transition-all relative',
                {
                    'bg-secondary/20':
                        conversationId === conversation.id.toString(),
                },
            )}
            onClick={handleConversationClick}
        >
            <div>
                <Image
                    src={conversation.shopLogo}
                    alt={conversation.shopName}
                    width={52}
                    height={52}
                    className="rounded-full border border-zinc-100"
                />
            </div>
            <div>
                <h5 className="mb-1">{conversation.shopName}</h5>
                <p className="text-black/70">{conversation.lastMessageAt}</p>
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
