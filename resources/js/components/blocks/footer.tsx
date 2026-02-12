import * as React from 'react';

import { cn } from '@/lib/utils';

function Footer({ className, ...props }: React.ComponentProps<'footer'>) {
    return (
        <footer
            data-slot="footer"
            className={cn('mx-auto flex max-w-160 flex-col items-center gap-2 px-6 py-16 text-center', className)}
            {...props}
        />
    );
}

function FooterLabel({ className, ...props }: React.ComponentProps<'p'>) {
    return <p data-slot="footer-label" className={cn('text-sm text-muted-foreground', className)} {...props} />;
}

function FooterLogo({ className, ...props }: React.ComponentProps<'div'>) {
    return <div data-slot="footer-logo" className={cn('flex items-center justify-center gap-3', className)} {...props} />;
}

function FooterDescription({ className, ...props }: React.ComponentProps<'p'>) {
    return <p data-slot="footer-description" className={cn('text-pretty text-muted-foreground', className)} {...props} />;
}

function FooterLinks({ className, ...props }: React.ComponentProps<'div'>) {
    return <div data-slot="footer-links" className={cn('flex flex-wrap items-center justify-center gap-x-6 gap-y-2', className)} {...props} />;
}

export { Footer, FooterDescription, FooterLabel, FooterLinks, FooterLogo };
