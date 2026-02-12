import { Hero, HeroActions, HeroContent, HeroImage, HeroTagline, HeroTitle } from '@/components/blocks/hero';
import { Button } from '@/components/ui/button';
import { Footer, FooterDescription, FooterLabel, FooterLinks, FooterLogo } from '@/components/ui/footer';
import { Link } from '@inertiajs/react';
import { ArrowRight, ArrowUpRight } from 'lucide-react';

export default function App({ title, message }) {
    return (
        <div className="flex min-h-screen flex-col items-center justify-center bg-background p-4">
            <div>
                <Hero>
                    <HeroContent>
                        <HeroTitle>Discover Great Movies</HeroTitle>
                        <HeroTagline>Your ultimate guide to the best films — from timeless classics to the latest releases.</HeroTagline>
                        <HeroActions>
                            <Button>
                                <Link
                                    href="#"
                                    className="flex items-center gap-1"
                                >
                                    <span>Browse movies</span>
                                    <ArrowRight className="size-4 shrink-0" />
                                </Link>
                            </Button>
                            <Button variant="ghost">
                                <Link
                                    href="#"
                                    className="flex items-center gap-1"
                                >
                                    <span>Top rated</span>
                                    <ArrowUpRight className="size-4 shrink-0" />
                                </Link>
                            </Button>
                        </HeroActions>
                    </HeroContent>
                    <HeroImage>
                        <img
                            src="https://images.unsplash.com/photo-1536440136628-849c177e76a1?q=80&w=2650&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                            alt="Exterior of Central Cinema at night with glowing red neon signs, a person sitting at the entrance, and a small table with a chair on the patterned sidewalk."
                        />
                    </HeroImage>
                </Hero>
                <Footer>
                    <FooterLabel>A film by</FooterLabel>
                    <FooterLogo>
                        <img
                            src="https://images.unsplash.com/photo-1485846234645-a62644f84728?q=80&w=200&auto=format&fit=crop"
                            alt="Movie reel"
                            className="m-auto mb-8 w-36 rounded"
                        />
                    </FooterLogo>
                    <FooterDescription>
                        Now playing in theaters and streaming everywhere. Experience the story that critics are calling unforgettable.
                    </FooterDescription>
                    <FooterLinks>
                        <Link
                            href="#"
                            className="underline"
                        >
                            Find showtimes
                        </Link>
                    </FooterLinks>
                </Footer>
            </div>
        </div>
    );
}
