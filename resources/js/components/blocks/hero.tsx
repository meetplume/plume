import * as React from 'react';

import { cn } from '@/lib/utils';

function Hero({ className, ...props }: React.ComponentProps<'section'>) {
    return (
        <section
            data-slot="hero"
            className={cn(
                'grid items-center gap-4 py-8 text-center md:grid-cols-[7fr_4fr] md:gap-[3%] md:py-[clamp(2.5rem,calc(1rem+10vmin),10rem)] md:text-start',
                className,
            )}
            {...props}
        />
    );
}

function HeroContent({ className, ...props }: React.ComponentProps<'div'>) {
    return (
        <div
            data-slot="hero-content"
            className={cn('flex flex-col items-center gap-[clamp(1.5rem,calc(1.5rem+1vw),2rem)] md:items-start', className)}
            {...props}
        />
    );
}

function HeroTitle({ className, ...props }: React.ComponentProps<'h1'>) {
    return (
        <h1
            data-slot="hero-title"
            className={cn(
                'max-w-[50ch] text-[clamp(2rem,calc(0.25rem+5vw),3.75rem)] leading-tight font-semibold text-balance text-foreground',
                className,
            )}
            {...props}
        />
    );
}

function HeroTagline({ className, ...props }: React.ComponentProps<'p'>) {
    return (
        <p
            data-slot="hero-tagline"
            className={cn('max-w-[50ch] text-[clamp(1rem,calc(0.0625rem+2vw),1.25rem)] text-pretty text-muted-foreground', className)}
            {...props}
        />
    );
}

function HeroActions({ className, ...props }: React.ComponentProps<'div'>) {
    return (
        <div
            data-slot="hero-actions"
            className={cn('flex flex-wrap items-center justify-center gap-x-8 gap-y-4 md:justify-start', className)}
            {...props}
        />
    );
}

type HeroImageProps = (React.ComponentProps<'div'> & { src?: never; dark?: never }) | (React.ComponentProps<'img'> & { dark?: string });

function HeroImage({ className, dark, ...props }: HeroImageProps) {
    const containerClass = 'order-first mx-auto w-[min(70%,20rem)] md:mx-0 md:order-last md:w-[min(100%,25rem)]';

    if ('src' in props && props.src) {
        const { src, alt, ...imgProps } = props;
        if (dark) {
            return (
                <div data-slot="hero-image" className={containerClass}>
                    <img className={cn('object-contain dark:hidden', className)} src={src} alt={alt} {...imgProps} />
                    <img className={cn('hidden object-contain dark:block', className)} src={dark} alt={alt} {...imgProps} />
                </div>
            );
        }

        return (
            <div data-slot="hero-image" className={containerClass}>
                <img className={cn('object-contain', className)} src={src} alt={alt} {...imgProps} />
            </div>
        );
    }

    return <div data-slot="hero-image" className={cn(containerClass, className)} {...(props as React.ComponentProps<'div'>)} />;
}

export { Hero, HeroActions, HeroContent, HeroImage, HeroTagline, HeroTitle };
