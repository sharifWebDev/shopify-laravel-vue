import { getSessionToken } from "@shopify/app-bridge-utils";
import { createApp } from '@shopify/app-bridge'; 

// App Bridge configuration
const appBridgeConfig = {
    apiKey: process.env.SHOPIFY_API_KEY,   
    host: new URLSearchParams(window.location.search).get('host'),
    forceRedirect: true,
    shopOrigin: new URLSearchParams(window.location.search).get('shop'),  
};
 
const app = createApp(appBridgeConfig);
 
async function retrieveToken() {
    try {
         
        const sessionToken = await getSessionToken(app); 
        const bearer = `Bearer ${sessionToken}`;
 
        if (window.axios) {
            window.axios.defaults.headers.common['Authorization'] = bearer;
        }
 
        window.sessionToken = sessionToken;
    } catch (error) {
        console.error("Error retrieving session token:", error);
    }
}

export { app, retrieveToken };
