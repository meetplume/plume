import { CallToAction, type CallToActionProps } from '@/components/blocks/call-to-action';
import { Features, type FeaturesProps } from '@/components/blocks/features';
import { Hero1, type Hero1Props } from '@/components/blocks/hero1';
import { Hero2, type Hero2Props } from '@/components/blocks/hero2';

export type SectionProps =
    | ({ type: 'hero1' } & Hero1Props)
    | ({ type: 'hero2' } & Hero2Props)
    | ({ type: 'features' } & FeaturesProps)
    | ({ type: 'callToAction' } & CallToActionProps);

export function Section(props: SectionProps) {
    switch (props.type) {
        case 'hero1':
            return <Hero1 {...props} />;
        case 'hero2':
            return <Hero2 {...props} />;
        case 'features':
            return <Features {...props} />;
        case 'callToAction':
            return <CallToAction {...props} />;
    }
}
