<template>
    <PolarisProvider>
      <AppBridgeProvider :host="host" :app="app" :Action="Action">
        <Frame>
          <Layout>
            <!-- Navigation Menu -->
            <Layout.Section>
              <NavigationMenu :items="navigationLinks" :matcher="matcher" />
            </Layout.Section>
  
            <!-- Content Slot -->
            <Layout.Section>
              <!-- The content will be injected here by Inertia.js -->
              <slot></slot>
            </Layout.Section>
          </Layout>
        </Frame>
      </AppBridgeProvider>
    </PolarisProvider>
  </template>
  
  <script>
  import { computed } from 'vue';
  import { Frame, Layout, NavigationMenu } from '@shopify/polaris';
  import { AppBridgeProvider, PolarisProvider } from '@/Providers/AppBridgeProvider';
  import { setupAppBridge } from '@/appBridgeSetup';
  import { Inertia } from '@inertiajs/inertia';
import { Action } from '@shopify/app-bridge/actions/AuthCode';
  
  export default {
    name: 'LayoutFrame',
    props: {
      host: {
        type: String,
        required: true,
      },
    },
    setup(props) {
      // Set up the AppBridge instance for the app
      const app = setupAppBridge(props.host);
  
      const navigationLinks = computed(() => [
        {
          label: 'Dashboard',
          destination: '/dashboard',
        },
        {
          label: 'Settings',
          destination: '/settings',
        },
        // Add more navigation links as necessary
      ]);
  
      const matcher = (link, location) => {
        return link.destination === location.pathname;
      };
  
      return {
        navigationLinks,
        matcher,
      };
    },
  };
  </script>
  
  <style scoped>
  /* Add custom styles here if necessary */
  </style>
  