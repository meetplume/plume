import TiptapExperiment from '../components/editor/PlumeEditor';

export default function App({ appName }: { appName: string }) {
    return (
        <div>
            <div className="bg-blue-500"> Foo bar {appName}</div>

            <TiptapExperiment />
        </div>
    );
}
