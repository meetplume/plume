import { Footer2 } from '@/components/blocks/footer2';
import { Hero2 } from '@/components/blocks/hero2';
import { type Action } from '@/lib/actions';

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
    const footerText: string =
        '&copy; <a href="https://laravel-zero.com">Laravel Zero</a> 2026. Built with <a href="https://laravel.com">Laravel</a> and <a href="https://tailwindcss.com">Tailwind CSS</a>. Logo by <a href="https://twitter.com/caneco">Caneco</a>.';

    return (
        <div className="flex min-h-screen flex-col items-center justify-center bg-background">
            <div className="grow">
                <Hero2 title={title} tagline={tagline} command={command} actions={actions} />
            </div>
            <Footer2 text={footerText} />
        </div>
    );
}
