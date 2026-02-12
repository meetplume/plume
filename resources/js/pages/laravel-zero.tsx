import { CallToAction, CallToActionProps } from '@/components/blocks/call-to-action';
import { type FeaturesProps, Features } from '@/components/blocks/features';
import { type Footer2Props, Footer2 } from '@/components/blocks/footer2';
import { type Hero2Props, Hero2 } from '@/components/blocks/hero2';

export default function App() {
    const hero: Hero2Props = {
        title: 'Build Your next cli tool with |Laravel Zero|',
        tagline:
            "The Laravel way to build fast, powerful, and elegant console applications, with the simplicity and flexibility you've come to love.",
        command: 'composer global require laravel-zero/installer',
        actions: [
            {
                label: 'Get Started',
                href: 'https://laravel-zero.com/docs/introduction',
                target: '_blank',
            },
        ],
    };

    const features1: FeaturesProps = {
        columns: {
            default: 1,
            md: 3,
        },
        features: [
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
        ],
    };

    const callToAction: CallToActionProps = {
        badge: 'Best cli apps builder ever',
        title: 'Build your next CLI tool faster than ever',
        description:
            'Laravel Zero offers a simple, yet powerful framework to create fast, reliable, and scalable console applications built on the robust Laravel components.',
        actions: [{ label: 'Read the docs', href: '/docs', variant: 'link', icon: 'ArrowRight' }],
        image: 'https://laravel-zero.com/assets/img/logo-large.png',
        imageAlt: 'Laravel Zero',
    };

    const features2: FeaturesProps = {
        title: "Features you'll love",
        description: 'Laravel Zero is a lightweight and flexible framework for building powerful console applications, built on top of Laravel.',
        columns: 2,
        footerTitle: "Wait, there's more!",
        footerActions: [{ label: 'Check out all features', href: '/features' }],
        features: [
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
        ],
    };

    const footer: Footer2Props = {
        text: '&copy; <a href="https://laravel-zero.com">Laravel Zero</a> 2026. Built with <a href="https://laravel.com">Laravel</a> and <a href="https://tailwindcss.com">Tailwind CSS</a>. Logo by <a href="https://twitter.com/caneco">Caneco</a>.',
    };

    return (
        <div className="flex min-h-screen flex-col items-center justify-center bg-background">
            <div className="flex grow flex-col">
                <Hero2 title={hero.title} tagline={hero.tagline} command={hero.command} actions={hero.actions} />

                <Features columns={features1.columns} features={features1.features} />

                <CallToAction
                    badge={callToAction.badge}
                    title={callToAction.title}
                    description={callToAction.description}
                    actions={callToAction.actions}
                    image={callToAction.image}
                    imageDark={callToAction.imageDark}
                    imageAlt={callToAction.imageAlt}
                />

                <Features
                    title={features2.title}
                    description={features2.description}
                    columns={features2.columns}
                    features={features2.features}
                    footerTitle={features2.footerTitle}
                    footerActions={features2.footerActions}
                />
            </div>

            <Footer2 text={footer.text} />
        </div>
    );
}
