import { CallToAction, CallToActionProps } from '@/components/blocks/call-to-action';
import { type FeaturesProps, Features } from '@/components/blocks/features';
import { type Footer2Props, Footer2 } from '@/components/blocks/footer2';
import { type Hero2Props, Hero2 } from '@/components/blocks/hero2';

type Props = {
    hero: Hero2Props;
    features1: FeaturesProps;
    callToAction: CallToActionProps;
    features2: FeaturesProps;
    footer: Footer2Props;
};

export default function App({ hero, features1, callToAction, features2, footer }: Props) {
    return (
        <div className="flex min-h-screen flex-col items-center justify-center bg-background">
            <div className="flex grow flex-col">
                <Hero2 {...hero} />

                <Features {...features1} />

                <CallToAction {...callToAction} />

                <Features {...features2} />
            </div>

            <Footer2 {...footer} />
        </div>
    );
}
