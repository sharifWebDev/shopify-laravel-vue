<template>
  <div class="dashboard-layout">
    <Header />
    <main class="main-content">
      <h2>All Posts</h2>
      <button @click="createPost">Add Post</button>
      <table class="table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Body</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="post in displayedPosts" :key="post.id">
            <td>{{ post.id }}</td>
            <td>{{ post.title }}</td>
            <td>{{ post.body }}</td>
            <td>{{ post.status ? 'Active' : 'Inactive' }}</td>
            <td>
              <button @click="editPost(post.id)">Edit</button>
              <button @click="deletePost(post.id)">Delete</button>
              <button @click="viewPost(post.id)">View</button>
            </td>
          </tr>
        </tbody>
      </table>
    </main>
    <Footer />
  </div>
</template>

<script>
import Header from '../components/Header.vue';
import Footer from '../components/Footer.vue';
import { router } from '@inertiajs/vue3';

export default {
  name: 'DashboardPage',
  components: { Header, Footer },
  props: {
    posts: {
      type: Array,
      required: true,
    },
  },
  computed: {
    displayedPosts() { 
       if (!this.posts) {
          return [
              { id: 1, title: 'Post 1', body: 'This is the body of Post 1.', status: true },
              { id: 2, title: 'Post 2', body: 'This is the body of Post 2.', status: false },
              { id: 3, title: 'Post 3', body: 'This is the body of Post 3.', status: true },
              { id: 4, title: 'Post 4', body: 'This is the body of Post 4.', status: false },
              { id: 5, title: 'Post 5', body: 'This is the body of Post 5.', status: true }, 
            ];
       }
       return this.posts;
    },
  },
  methods: {
    createPost() {
      let url = '/posts/create';
      router.visit(url);
      // console.log('Creating a new post...');
      // router.visit('/posts/create' {
      //   data : {
      //     title: 'dsjhg'
      //   }
      // });
    },
    editPost(id) {
      router.visit(`/posts/${id}/edit`);
    },
    deletePost(id) {
      if (confirm('Are you sure you want to delete this post?')) {
        router.delete(`/posts/${id}`);
      }
    },
    viewPost(id) {
      router.visit(`/posts/${id}`);
    },
  },
};
</script>

<style scoped>
.table {
  width: 100%;
  border-collapse: collapse;
}
.table th, .table td {
  border: 1px solid #ddd;
  padding: 8px;
}
.table th {
  background-color: #f4f4f4;
}
button {
  margin-right: 5px;
  padding: 5px 10px;
  background-color:rgb(69, 148, 72);
  color: white;
  border: none;
  cursor: pointer;
}
</style>
