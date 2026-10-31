import { Component, ReactNode } from 'react';

export default class PageErrorBoundary extends Component<
    { children: ReactNode },
    { failed: boolean }
> {
    state = { failed: false };

    static getDerivedStateFromError() {
        return { failed: true };
    }

    render() {
        if (this.state.failed) {
            return (
                <section role="alert" className="container py-16 text-center">
                    <h1 className="text-2xl font-semibold">This page could not load</h1>
                    <p className="mt-3">Please reload to get the latest version.</p>
                    <button type="button" className="mt-6 min-h-11 rounded-full bg-[#087c3e] px-6 text-white" onClick={() => window.location.reload()}>
                        Reload page
                    </button>
                </section>
            );
        }
        return this.props.children;
    }
}
