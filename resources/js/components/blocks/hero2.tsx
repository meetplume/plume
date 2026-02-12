import * as React from 'react';

import { type Action, renderActions } from '@/lib/actions';
import { cn } from '@/lib/utils';
import { Check, Copy } from 'lucide-react';

type Hero2Props = Omit<React.ComponentProps<'section'>, 'title'> & {
    title: string;
    tagline?: string;
    command?: string;
    actions?: Action[];
};

function parseTitle(title: string) {
    const parts = title.split(/\|([^|]+)\|/);
    return parts.map((part, i) =>
        i % 2 === 1 ? (
            <span key={i} className="text-primary">
                {part}
            </span>
        ) : (
            part
        ),
    );
}

export function Hero2({ className, title, tagline, command, actions, ...props }: Hero2Props) {
    const [copied, setCopied] = React.useState(false);

    const handleCopy = () => {
        if (!command) return;
        navigator.clipboard
            .writeText(command)
            .then(() => setCopied(true))
            .catch((err) => console.error('Failed to copy: ', err));
        setTimeout(() => setCopied(false), 2000);
    };

    return (
        <section
            data-slot="hero2"
            className={cn('relative overflow-hidden py-8 text-center md:py-[clamp(2.5rem,calc(1rem+10vmin),10rem)]', className)}
            {...props}
        >
            <div className="flex w-full flex-col items-center gap-[clamp(1.5rem,calc(1.5rem+1vw),2rem)] px-4">
                <h1 className="max-w-[20ch] text-[clamp(2.5rem,calc(0.25rem+6vw),4.5rem)] leading-[1.1] font-extrabold tracking-tight text-balance text-foreground">
                    {parseTitle(title)}
                </h1>

                {tagline && (
                    <p className="max-w-[55ch] text-[clamp(1rem,calc(0.0625rem+2vw),1.125rem)] leading-relaxed text-pretty text-muted-foreground">
                        {tagline}
                    </p>
                )}

                <div className="flex w-full flex-col items-center gap-4">
                    {command && (
                        <div className="inline-flex max-w-full items-center gap-3 rounded-full bg-primary/10 px-5 py-2.5 font-mono text-sm text-foreground/70">
                            <code className="min-w-0 truncate">{command}</code>
                            <button
                                type="button"
                                onClick={handleCopy}
                                className="shrink-0 cursor-pointer text-muted-foreground transition-colors hover:text-foreground"
                                aria-label={copied ? 'Copied' : 'Copy to clipboard'}
                            >
                                {copied ? <Check className="size-4" /> : <Copy className="size-4" />}
                            </button>
                        </div>
                    )}

                    {actions && <div className="flex flex-wrap items-center justify-center gap-4">{renderActions(actions)}</div>}
                </div>
            </div>
        </section>
    );
}
