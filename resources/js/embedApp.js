import '../css/tailwind.css';
import '../css/app.css';
import './bootstrap';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
        const page = pages[`./Pages/${name}.vue`];
        return page.default || page;
    },

    setup({ el, App, props }) {
        createApp({ render: () => h(App, props) }).mount(el);
    },

    progress: {
        color: '#29d', 
    },
});
