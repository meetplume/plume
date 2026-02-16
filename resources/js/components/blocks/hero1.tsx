import * as React from 'react';

import { Action } from '@/components/action';
import { cn } from '@/lib/utils';
import { type Action as ActionType } from '@/types/Action';

type Hero1Props = {
    className?: string;
    title: string;
    tagline?: string;
    actions?: ActionType[];
    image?: React.ReactNode | string;
    imageDark?: React.ReactNode | string;
    imageAlt?: string;
};

export function Hero1({ className, title, tagline, actions, image, imageDark, imageAlt = '', ...props }: Hero1Props) {
    const renderImage = (src: React.ReactNode | string) =>
        typeof src === 'string' ? <img src={src} alt={imageAlt} className="object-contain" /> : src;
    return (
        <section
            data-slot="hero1"
            className={cn(
                'grid items-center gap-4 py-8 text-center md:grid-cols-[7fr_4fr] md:gap-[3%] md:py-[clamp(2.5rem,calc(1rem+10vmin),10rem)] md:text-start',
                className,
            )}
            {...props}
        >
            <div className="flex flex-col items-center gap-[clamp(1.5rem,calc(1.5rem+1vw),2rem)] md:items-start">
                <h1 className="max-w-[50ch] text-[clamp(2rem,calc(0.25rem+5vw),3.75rem)] leading-tight font-semibold text-balance text-foreground">
                    {title}
                </h1>

                {tagline && (
                    <p className="max-w-[50ch] text-[clamp(1rem,calc(0.0625rem+2vw),1.25rem)] text-pretty text-muted-foreground">{tagline}</p>
                )}

                {actions && (
                    <div className="flex flex-wrap items-center justify-center gap-x-8 gap-y-4 md:justify-start">
                        {actions.map((action) => (
                            <Action key={action.label} {...action} />
                        ))}
                    </div>
                )}
            </div>

            {image && (
                <div className="order-first mx-auto w-[min(70%,20rem)] md:order-last md:mx-0 md:w-[min(100%,25rem)]">
                    {imageDark ? (
                        <>
                            <div className="dark:hidden">{renderImage(image)}</div>
                            <div className="hidden dark:block">{renderImage(imageDark)}</div>
                        </>
                    ) : (
                        renderImage(image)
                    )}
                </div>
            )}
        </section>
    );
}
