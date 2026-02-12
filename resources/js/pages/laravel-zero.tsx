import { Hero2 } from '@/components/blocks/hero2';
import { Button } from '@/components/ui/button';

export default function App() {
    return (
        <div className="flex min-h-screen items-center justify-center bg-background p-4">
            <Hero2
                title="Build Your next cli tool with |Laravel Zero|"
                tagline="The Laravel way to build fast, powerful, and elegant console applications, with the simplicity and flexibility you've come to love."
                command="composer global require laravel-zero/installer"
                actions={
                    <>
                        <Button>
                            <a href="https://laravel-zero.com/docs/introduction" target="_blank">
                                Get Started
                            </a>
                        </Button>
                    </>
                }
            />
        </div>
    );
}
