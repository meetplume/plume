import { type Footer1Props, Footer1 } from '@/components/blocks/footer1';
import { type Footer2Props, Footer2 } from '@/components/blocks/footer2';
import { type Header1Props, Header1 } from '@/components/blocks/header1';
import { type SectionProps, Section } from '@/components/blocks/section';

type HeaderProps = { type: 'header1' } & Header1Props;

type FooterProps = ({ type: 'footer1' } & Footer1Props) | ({ type: 'footer2' } & Footer2Props);

type Props = {
    header: HeaderProps;
    sections: SectionProps[];
    footer: FooterProps;
};

function renderHeader(props: HeaderProps) {
    switch (props.type) {
        case 'header1':
            return <Header1 {...props} />;
    }
}

function renderFooter(props: FooterProps) {
    switch (props.type) {
        case 'footer1':
            return <Footer1 {...props} />;
        case 'footer2':
            return <Footer2 {...props} />;
    }
}

export default function App({ header, sections, footer }: Props) {
    return (
        <div className="flex min-h-screen flex-col items-center justify-center bg-background">
            {renderHeader(header)}
            <div className="flex grow flex-col">
                {sections.map((section, index) => (
                    <Section key={index} {...section} />
                ))}
            </div>
            {renderFooter(footer)}
        </div>
    );
}
