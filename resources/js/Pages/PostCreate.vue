<template>
  <div class="post-form">
    <Header />
    <main class="form-content">
      <h2>Create Post</h2>
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
        <button type="submit">Save Post</button>
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
  name: 'PostCreate',
  components: { Header, Footer },
  data() {
    return {
      form: {
        title: '',
        body: '',
        status: true,
      },
    };
  },
  methods: {
    submitForm() {
      router.post(this.$props.action, this.form);
    },
  },
};
</script>

<style scoped>
/* Add any custom styles here */
</style>
