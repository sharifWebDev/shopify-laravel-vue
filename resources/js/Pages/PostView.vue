<template>
  <div class="post-view">
    <Header />
    <main class="content">
      <h2>{{ post.title }}</h2>
      <p>{{ post.body }}</p>
      <p>Status: {{ post.status ? 'Active' : 'Inactive' }}</p>
    </main>
    <Footer />
  </div>
</template>

<script>
import Header from '../components/Header.vue';
import Footer from '../components/Footer.vue';

export default {
  name: 'PostView',
  components: { Header, Footer },
  props: {
    post: Object,
  },
};
</script>

<style scoped>
/* Add any custom styles here */
</style>
