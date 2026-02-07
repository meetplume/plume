import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Feather } from 'lucide-react';

export default function App({ title, message }) {
    return (
        <div className="flex min-h-screen items-center justify-center bg-background p-4">
            <Card className="w-full max-w-md">
                <CardHeader>
                    <CardTitle className="flex items-center gap-2 text-2xl">
                        <Feather className="size-6" />
                        {title ?? 'Welcome to Plume'}
                    </CardTitle>
                    <CardDescription>
                        {message ?? 'A Markdown tool for content: pages, docs, wikis and more.'}
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <p className="text-sm text-muted-foreground">
                        Get started by editing your content with Plume's powerful Markdown
                        editor.
                    </p>
                </CardContent>
                <CardFooter>
                    <Button onClick={() => alert('Movies')}>Get Started</Button>
                </CardFooter>
            </Card>
        </div>
    );
}
