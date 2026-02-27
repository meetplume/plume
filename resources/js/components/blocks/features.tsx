import { Link } from '@/components/link';
import { useIcon } from '@/lib/icons';
import { type Columns, columnsClass } from '@/lib/columns';
import { cn } from '@/lib/utils';
import { type Feature } from '@/types/Feature';
import { type Link as LinkType } from '@/types/Link';

export type FeaturesProps = {
    className?: string;
    title?: string;
    description?: string;
    columns?: Columns;
    features: Feature[];
    footerTitle?: string;
    footerLinks?: LinkType[];
};

export function Features({ className, title, description, columns = 2, features, footerTitle, footerLinks, ...props }: FeaturesProps) {
    return (
        <section data-slot="features" className={cn('w-full max-w-[100vw] px-4 py-12 md:px-8 md:py-20', className)} {...props}>
            {(title || description) && (
                <div className="mx-auto mb-12 max-w-2xl text-center">
                    {title && <h2 className="text-3xl font-bold tracking-tight text-foreground md:text-4xl">{title}</h2>}
                    {description && <p className="mt-4 text-pretty text-muted-foreground">{description}</p>}
                </div>
            )}

            <div className={cn('mx-auto grid max-w-6xl gap-x-12 gap-y-10', columnsClass(columns))}>
                {features.map((feature) => (
                    <FeatureItem key={feature.title} feature={feature} />
                ))}
            </div>

            {(footerTitle || footerLinks) && (
                <div className="mx-auto mt-12 flex max-w-6xl flex-col items-center gap-4 text-center">
                    {footerTitle && <p className="text-muted-foreground">{footerTitle}</p>}
                    {footerLinks && (
                        <div className="flex flex-wrap items-center justify-center gap-4">
                            {footerLinks.map((link) => (
                                <Link key={link.label} {...link} />
                            ))}
                        </div>
                    )}
                </div>
            )}
        </section>
    );
}

function FeatureItem({ feature }: { feature: Feature }) {
    const Icon = useIcon(feature.icon);

    return (
        <div className="flex items-start gap-4">
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
}
