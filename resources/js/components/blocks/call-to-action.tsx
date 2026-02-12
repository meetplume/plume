import * as React from 'react';

import { type Action, renderActions } from '@/lib/actions';
import { cn } from '@/lib/utils';

export type CallToActionProps = Omit<React.ComponentProps<'section'>, 'title'> & {
    badge?: string;
    title: string;
    description?: string;
    actions?: Action[];
    image?: React.ReactNode | string;
    imageDark?: React.ReactNode | string;
    imageAlt?: string;
};

export function CallToAction({ className, badge, title, description, actions, image, imageDark, imageAlt = '', ...props }: CallToActionProps) {
    const renderImage = (src: React.ReactNode | string) =>
        typeof src === 'string' ? <img src={src} alt={imageAlt} className="object-contain" /> : src;
    return (
        <section className={cn('mx-auto grid w-full max-w-6xl gap-5 px-4 py-12 md:grid-cols-2 md:px-8 md:py-20', className)} {...props}>
            <div className="flex flex-wrap items-center">
                <div className="flex flex-col gap-4 md:max-w-sm">
                    {badge && (
                        <span className="mr-auto rounded-full bg-primary/10 px-3 py-1 text-xs font-medium tracking-wider text-foreground/70 uppercase">
                            {badge}
                        </span>
                    )}
                    <h2 className="text-2xl font-bold text-balance text-foreground uppercase">{title}</h2>
                    {description && <p className="text-sm leading-relaxed text-pretty text-muted-foreground">{description}</p>}
                    {actions && <div className="flex flex-wrap items-center gap-4">{renderActions(actions)}</div>}
                </div>
            </div>
            <div className="mx-auto flex w-full min-w-0 items-center">
                {image &&
                    (imageDark ? (
                        <>
                            <div className="dark:hidden">{renderImage(image)}</div>
                            <div className="hidden dark:block">{renderImage(imageDark)}</div>
                        </>
                    ) : (
                        renderImage(image)
                    ))}
            </div>
        </section>
    );
}
