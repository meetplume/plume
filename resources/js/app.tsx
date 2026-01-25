import { createRoot } from 'react-dom/client';
import '../css/app.css';
import App from './pages/documentation';

const appName = import.meta.env.VITE_APP_NAME || 'Plume';

const container = document.getElementById('app');
if (!container) throw new Error("Root container 'app' not found");

const root = createRoot(container);

const props = { appName };

root.render(<App {...props} />);
