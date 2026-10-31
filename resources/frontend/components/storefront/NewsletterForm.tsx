import { FormEvent, useId, useState } from 'react';
import { LuArrowUpRight } from 'react-icons/lu';
import { useSubscribeMutation } from '../../store/features/api/userApi';

export default function NewsletterForm({
    compact = false,
}: {
    compact?: boolean;
}) {
    const id = useId();
    const [email, setEmail] = useState('');
    const [message, setMessage] = useState('');
    const [failed, setFailed] = useState(false);
    const [subscribe, { isLoading }] = useSubscribeMutation();

    async function submit(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();
        setMessage('');
        setFailed(false);
        try {
            await subscribe(email).unwrap();
            setMessage('You’re on the list. Thank you for subscribing!');
            setEmail('');
        } catch {
            setFailed(true);
            setMessage('We couldn’t subscribe you. Please try again.');
        }
    }

    return (
        <form
            className={`bot-newsletter-form ${
                compact ? 'bot-newsletter-form--compact' : ''
            }`}
            onSubmit={submit}
        >
            <label htmlFor={id} className="bot-sr-only">
                Email address
            </label>
            <div className="bot-email-row">
                <input
                    id={id}
                    type="email"
                    autoComplete="email"
                    placeholder="Your email address"
                    required
                    value={email}
                    onChange={(event) => setEmail(event.target.value)}
                    aria-describedby={`${id}-message`}
                />
                <button
                    type="submit"
                    disabled={isLoading}
                    aria-label={
                        compact ? 'Subscribe to our newsletter' : undefined
                    }
                >
                    {!compact && (isLoading ? 'Joining…' : 'Subscribe')}
                    <LuArrowUpRight aria-hidden="true" />
                </button>
            </div>
            <p
                id={`${id}-message`}
                className={
                    failed
                        ? 'bot-form-message bot-form-message--error'
                        : 'bot-form-message'
                }
                role={failed ? 'alert' : 'status'}
            >
                {message}
            </p>
        </form>
    );
}
