import { Features } from '@/components/blocks/features';
import { Footer2 } from '@/components/blocks/footer2';
import { Hero2 } from '@/components/blocks/hero2';
import { type Action } from '@/lib/actions';
import { type Feature } from '@/lib/features';

export default function App() {
    const title: string = 'Build Your next cli tool with |Laravel Zero|';
    const tagline: string =
        "The Laravel way to build fast, powerful, and elegant console applications, with the simplicity and flexibility you've come to love.";
    const command: string = 'composer global require laravel-zero/installer';
    const actions: Action[] = [
        {
            label: 'Get Started',
            href: 'https://laravel-zero.com/docs/introduction',
            target: '_blank',
        },
    ];

    const features1: Feature[] = [
        {
            title: 'Highly modular Framework design',
            description:
                'Laravel Zero is a lightweight and modular micro-framework for developing fast and powerful console applications. Built on top of the Laravel components.',
            icon: 'Layers',
        },
        {
            title: 'Write powerful Console applications',
            description:
                'Laravel Zero has a simple and powerful syntax that enables developers to build very complex applications far more quickly than with any previous framework.',
            icon: 'Terminal',
        },
        {
            title: 'For Artisans 100% Open Source',
            description:
                'You’re free to dig through the source to see exactly how it works. See something that needs to be improved? Just send us a pull request on GitHub.',
            icon: 'AppWindowMac',
        },
    ];

    const features2: Feature[] = [
        {
            title: 'Commands',
            description:
                'Build powerful and easy-to-use console commands without breaking a sweat. Using a straightforward syntax that gets the job done quickly.',
            icon: 'SquareTerminal',
        },
        {
            title: 'Service Providers',
            description: 'Tap into the full power of Laravel by using service providers to easily add more functionality to your application.',
            icon: 'CopyCheck',
        },
        {
            title: 'Database',
            description:
                'Work with your database like a pro using the DB facade. Whether it’s retrieving data or making changes, it’s simple and hassle-free.',
            icon: 'Database',
        },
        {
            title: 'Logging',
            description:
                'Stay on top of what’s happening in your app with built-in logging. Debug issues, track activity, and keep everything running smoothly.',
            icon: 'ScanText',
        },
        {
            title: 'Filesystem',
            description: 'Handle files effortlessly. From reading and writing to managing files, Laravel Zero gives you all the tools you need.',
            icon: 'Folders',
        },
        {
            title: 'Desktop notifications',
            description: 'Make your app stand out by sending friendly desktop notifications that keep users informed in real-time.',
            icon: 'BellRing',
        },
    ];
    const footerText: string =
        '&copy; <a href="https://laravel-zero.com">Laravel Zero</a> 2026. Built with <a href="https://laravel.com">Laravel</a> and <a href="https://tailwindcss.com">Tailwind CSS</a>. Logo by <a href="https://twitter.com/caneco">Caneco</a>.';

    return (
        <div className="flex min-h-screen flex-col items-center justify-center bg-background">
            <div className="flex grow flex-col">
                <Hero2 title={title} tagline={tagline} command={command} actions={actions} />

                <Features
                    columns={{
                        default: 1,
                        md: 3,
                    }}
                    features={features1}
                />

                <Features
                    title="Features you'll love"
                    description="Laravel Zero is a lightweight and flexible framework for building powerful console applications, built on top of Laravel."
                    columns={2}
                    features={features2}
                    footerTitle="Wait, there's more!"
                    footerActions={[{ label: 'Check out all features', href: '/features' }]}
                />
            </div>

            <Footer2 text={footerText} />
        </div>
    );
}
