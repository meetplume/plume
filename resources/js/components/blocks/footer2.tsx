import * as React from 'react';

import { cn } from '@/lib/utils';

type Footer2Props = React.ComponentProps<'footer'> & {
    text: string;
};

export function Footer2({ className, text, ...props }: Footer2Props) {
    return (
        <footer
            data-slot="footer2"
            className={cn('w-full bg-primary px-6 py-4 text-center text-sm text-primary-foreground [&_a]:font-bold', className)}
            {...props}
        >
            <span dangerouslySetInnerHTML={{ __html: text }} />
        </footer>
    );
}
