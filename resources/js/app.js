import { createApp, h, ref } from "vue";
import { createInertiaApp } from "@inertiajs/inertia-vue3";
import DashboardPage from "./Pages/DashboardPage.vue";
import Token from "./Pages/Auth/Token.vue";
import { app as appBridge, retrieveToken } from "./appBridgeSetup.js";

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.vue", { eager: true });
        const page = pages[`./Pages/${name}.vue`];

        if (!page) {
            throw new Error(`Page not found: ./Pages/${name}.vue`);
        }

        // Set default layout
        page.default.layout = page.default.layout || ((page) => h(DashboardPage, { default: () => page }));

        return page;
    },
    setup({ el, App, props }) {
        // Create the Vue app
        const app = createApp({ render: () => h(App, props) });

        // Reactive properties
        const showToken = ref(false);
        const shopDomain = ref("");
        const host = ref("");
        const target = ref("");

        // Define a method to showt the Token componen
        const navigateToToken = (domain, hostVal, targetVal) => {
            shopDomain.value = domain;
            host.value = hostVal;
            target.value = targetVal;
            showToken.value = true;
        };

        // Make navigateToToken globally available
        app.config.globalProperties.$navigateToToken = navigateToToken;

        // Retrieve session token
        retrieveToken(); 

        // Mount the app to the DOM
        app.mount(el);

        // Conditionally render Token component
        if (showToken.value) {
            return h(Token, {
                shopDomain: shopDomain.value,
                host: host.value,
                target: target.value,
            });
        }
    },
    progress: {
        color: "#4B5563",
    },
});
