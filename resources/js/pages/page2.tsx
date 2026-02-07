import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { ArrowRight, Feather } from 'lucide-react';

export default function App({ appName, title }) {
    return (
        <div className="flex min-h-screen items-center justify-center bg-background p-4">
            <div className="max-w-lg space-y-6 text-center">
                <div className="mx-auto flex size-16 items-center justify-center rounded-full bg-primary">
                    <Feather className="size-8 text-primary-foreground" />
                </div>

                <div className="space-y-2">
                    <Badge variant="secondary">{appName ?? 'Plume'}</Badge>
                    <h1 className="text-4xl font-bold tracking-tight text-foreground">
                        {title ?? 'Welcome to Plume'}
                    </h1>
                    <p className="text-muted-foreground">
                        A Markdown tool for content: pages, docs, wikis and more.
                    </p>
                </div>

                <Separator />

                <div className="flex justify-center gap-3">
                    <Button onClick={() => alert('Learn More')} variant="outline">Learn More</Button>
                    <Button onClick={() => alert('Get started')}>
                        Get Started <ArrowRight className="size-4" />
                    </Button>
                </div>
            </div>
        </div>
    );
}
