import * as React from 'react';

import { cn } from '@/lib/utils';

type Footer1Props = React.ComponentProps<'footer'> & {
    label?: string;
    image?: React.ReactNode | string;
    imageDark?: React.ReactNode | string;
    imageAlt?: string;
    imageWidth?: number;
    description?: string;
    links?: React.ReactNode;
};

export function Footer1({ className, label, image, imageDark, imageAlt = '', imageWidth = 144, description, links, ...props }: Footer1Props) {
    const renderImage = (src: React.ReactNode | string) =>
        typeof src === 'string' ? <img src={src} alt={imageAlt} className="max-w-full" style={{ width: imageWidth }} /> : src;

    return (
        <footer data-slot="footer" className={cn('mx-auto flex max-w-160 flex-col items-center gap-2 px-6 py-16 text-center', className)} {...props}>
            {label && <p className="text-sm text-muted-foreground">{label}</p>}
            {image && (
                <div className="flex items-center justify-center gap-3">
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
            {description && <p className="text-pretty text-muted-foreground">{description}</p>}
            {links && <div className="flex flex-wrap items-center justify-center gap-x-6 gap-y-2">{links}</div>}
        </footer>
    );
}
