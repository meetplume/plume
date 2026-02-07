import TiptapExperiment from '../components/editor/PlumeEditor';

export default function App({ appName, title }) {
    return (
        <div>
            <div className="bg-blue-500"> Foo bar {appName || 'fooo'}</div>

            {title}

            <TiptapExperiment />
        </div>
    );
}
