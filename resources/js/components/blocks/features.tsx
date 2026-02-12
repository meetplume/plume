import * as React from 'react';

import { type Action, renderActions } from '@/lib/actions';
import { type Columns, columnsClass } from '@/lib/columns';
import { type Feature } from '@/lib/features';
import { cn } from '@/lib/utils';
import * as LucideIcons from 'lucide-react';

type FeaturesProps = React.ComponentProps<'section'> & {
    title?: string;
    description?: string;
    columns?: Columns;
    features: Feature[];
    footerTitle?: string;
    footerActions?: Action[];
};

export function Features({ className, title, description, columns = 2, features, footerTitle, footerActions, ...props }: FeaturesProps) {
    return (
        <section
            data-slot="features"
            className={cn('w-full max-w-[100vw] px-4 py-12 md:px-8 md:py-20', className)}
            {...props}
        >
            {(title || description) && (
                <div className="mx-auto mb-12 max-w-2xl text-center">
                    {title && (
                        <h2 className="text-3xl font-bold tracking-tight text-foreground md:text-4xl">{title}</h2>
                    )}
                    {description && (
                        <p className="mt-4 text-pretty text-muted-foreground">{description}</p>
                    )}
                </div>
            )}

            <div className={cn('mx-auto grid max-w-6xl gap-x-12 gap-y-10', columnsClass(columns))}>
                {features.map((feature) => {
                    const Icon = feature.icon ? LucideIcons[feature.icon] : null;

                    return (
                        <div key={feature.title} className="flex items-start gap-4">
                            {Icon && (
                                <div className="flex size-12 shrink-0 items-center justify-center rounded-full bg-primary/10">
                                    <Icon className="size-5 text-primary" />
                                </div>
                            )}
                            <div>
                                <h3 className="font-bold text-foreground">{feature.title}</h3>
                                <p className="mt-1 text-sm leading-relaxed text-muted-foreground">{feature.description}</p>
                            </div>
                        </div>
                    );
                })}
            </div>

            {(footerTitle || footerActions) && (
                <div className="mx-auto mt-12 flex max-w-6xl flex-col items-center gap-4 text-center">
                    {footerTitle && (
                        <p className="text-muted-foreground">{footerTitle}</p>
                    )}
                    {footerActions && (
                        <div className="flex flex-wrap items-center justify-center gap-4">
                            {renderActions(footerActions)}
                        </div>
                    )}
                </div>
            )}
        </section>
    );
}
