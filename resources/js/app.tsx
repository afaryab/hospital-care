import '../css/app.css';

import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import { initializeTheme } from './hooks/use-appearance';
import CommandPaletteLayout from './components/kbar-wrapper';

import * as Sentry from "@sentry/react"; // e.g., @sentry/react
import { Replay } from "@sentry/replay";

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

Sentry.init({
  dsn: "https://ffe0fd42fcde6331e6444dd94fecb2a2@o4510901696593920.ingest.de.sentry.io/4510901699084368",
  // Setting this option to true will send default PII data to Sentry.
  // For example, automatic IP address collection on events
  sendDefaultPii: true,
  integrations: [
    Sentry.replayIntegration()
  ],
  // Session Replay
  replaysSessionSampleRate: 0.1, // This sets the sample rate at 10%. You may want to change it to 100% while in development and then sample at a lower rate in production.
  replaysOnErrorSampleRate: 1.0
});

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.tsx`,
            import.meta.glob('./pages/**/*.tsx'),
        ),
    setup({ el, App, props }) {
        const root = createRoot(el);

        if (props?.auth?.user) {
            Sentry.setUser({ id: props.auth.user.id, email: props.auth.user.email, org: appName });
        }

        root.render(
            <StrictMode>
                <CommandPaletteLayout>
                    <App {...props} />
                </CommandPaletteLayout>
            </StrictMode>,
        );
    },
    progress: {
        color: '#06df72',
    },
});

// This will set light / dark mode on load...
initializeTheme();
