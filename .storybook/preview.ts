import { definePreview } from '@storybook/react-vite';
import '../resources/css/app.css';

export default definePreview({
    parameters: {
        controls: {
            matchers: {
                color: /(background|color)$/i,
                date: /Date$/i,
            },
        },
    },
});
