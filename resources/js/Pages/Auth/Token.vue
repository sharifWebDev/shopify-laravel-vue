<template>
  <div class="layout-section">
    <div class="card">
      <div class="block-stack"> 
        <div v-if="loading" class="loading-text">Loading session token...</div>
         
        <div v-else class="token-display">
          <p>Session Token: {{ token }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { onMounted, ref } from 'vue';
import createApp from '@shopify/app-bridge';
import { getSessionToken } from '@shopify/app-bridge-utils';

export default {
  name: 'Token',
  props: {
    shopDomain: String,
    host: String,
    target: String,
  },
  setup(props) {
    console.log('props:', props);
    const app = ref(null);
    const token = ref('');
    const loading = ref(true);  

    onMounted(async () => {
      try {
        console.log('Initializing Shopify AppBridge...');

        app.value = createApp({
          apiKey: process.env.VITE_SHOPIFY_API_KEY,
          host: props.host,
        });

        if (app.value) {
          console.log('AppBridge Initialized');

          const sessionToken = await getSessionToken(app.value);
          token.value = sessionToken; // Store token in the ref for further debugging
          console.log('Session Token:', sessionToken);

          const separator = props.target.includes('?') ? '&' : '?';
          const hostParam = props.target.includes('host') ? '' : `&host=${props.host}`;
         
          const finalUrl = `${props.target}${separator}token=${sessionToken}${hostParam}`;
          console.log('Redirect URL:', finalUrl);

          window.location.href = finalUrl; 
        }
      } catch (error) {
        console.error('Error fetching session token:', error);
      } finally {
        loading.value = false;  // Set loading to false after operation is complete
      }
    });

    return {
      token,
      loading,  
    };
  },
};
</script>
 