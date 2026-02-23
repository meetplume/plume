import { Footer1 } from '@/components/blocks/footer1';
import { Hero1 } from '@/components/blocks/hero1';
import { Link } from '@/components/link';
import { Card, CardAction, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import type { Link as LinkType } from '@/types/Link';

export default function App() {
    const links: LinkType[] = [
        { label: 'Browse movies', href: '#', icon: 'ArrowRight' },
        { label: 'Top rated', href: '#', variant: 'ghost', icon: 'ArrowUpRight' },
    ];

    return (
        <div className="flex min-h-screen flex-col items-center justify-center bg-background p-4">
            <div>
                <Hero1
                    title="Discover Great Movies"
                    tagline="Your ultimate guide to the best films — from timeless classics to the latest releases."
                    image="https://images.unsplash.com/photo-1598899134739-24c46f58b8c0?q=80&w=2056&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                    imageDark="https://images.unsplash.com/photo-1536440136628-849c177e76a1?q=80&w=2650&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                    imageAlt="Exterior of Central Cinema at night with glowing red neon signs, a person sitting at the entrance, and a small table with a chair on the patterned sidewalk."
                    links={links}
                />

                <Card className="mx-auto max-w-lg">
                    <CardHeader>
                        <CardTitle>Card Title</CardTitle>
                        <CardDescription>Card Description</CardDescription>
                        <CardAction>Card Action</CardAction>
                    </CardHeader>
                    <CardContent>
                        <p>Card Content</p>
                    </CardContent>
                    <CardFooter>
                        <p>Card Footer</p>
                    </CardFooter>
                </Card>

                <Footer1
                    label="A film by"
                    image="https://images.unsplash.com/photo-1485846234645-a62644f84728?q=80&w=200&auto=format&fit=crop"
                    imageAlt="Movie reel"
                    description="Now playing in theaters and streaming everywhere. Experience the story that critics are calling unforgettable."
                    links={<Link href="#" className="underline" variant="link" label="Find showtimes" />}
                />
            </div>
        </div>
    );
}
