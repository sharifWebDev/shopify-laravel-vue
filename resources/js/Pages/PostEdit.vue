<template>
  <div class="post-form">
    <Header />
    <main class="form-content">
      <h2>Edit Post</h2>
      <form @submit.prevent="submitForm">
        <div>
          <label for="title">Title</label>
          <input type="text" id="title" v-model="form.title" required />
        </div>
        <div>
          <label for="body">Body</label>
          <textarea id="body" v-model="form.body" required></textarea>
        </div>
        <div>
          <label for="status">Status</label>
          <select v-model="form.status" required>
            <option value="true">Active</option>
            <option value="false">Inactive</option>
          </select>
        </div>
        <button type="submit">Update Post</button>
      </form>
    </main>
    <Footer />
  </div>
</template>

<script>
import Header from '../components/Header.vue';
import Footer from '../components/Footer.vue';
import { router } from '@inertiajs/vue3';

export default {
  name: 'PostEdit',
  components: { Header, Footer },
  props: {
    post: Object,
    action: String,
    method: String,
  },
  data() {
    return {
      form: {
        title: this.post.title,
        body: this.post.body,
        status: this.post.status,
      },
    };
  },
  methods: {
    submitForm() {
      router[this.method.toLowerCase()](this.action, this.form);
    },
  },
};
</script>

<style scoped>
/* Add any custom styles here */
</style>
