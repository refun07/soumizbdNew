export function saleCountdown(endsAt: string | null, now: number) {
    if (!endsAt) return null;
    const end = Date.parse(endsAt);
    if (!Number.isFinite(end)) return null;
    const remaining = Math.max(0, Math.ceil((end - now) / 1000));
    return {
        expired: end <= now,
        days: Math.floor(remaining / 86400),
        hours: Math.floor((remaining % 86400) / 3600),
        minutes: Math.floor((remaining % 3600) / 60),
        seconds: remaining % 60,
    };
}
