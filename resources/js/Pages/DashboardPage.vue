<template>
  <div class="dashboard-layout">
    <Header />
    <main class="main-content">
      <h2>All Posts</h2>
      <button @click="allPost">Refresh</button>
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
          <tr v-for="post in debugPosts" :key="post.id">
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
import { Head } from '@inertiajs/inertia-vue3';
 
export default {
  name: 'DashboardPage',
  components: { Header, Footer, Head, Head ,},
  
  props: {
    posts: {
      type: Array,
      required: true,
    },
    host: {
      type: String,
      required: true,
    },
},

computed: {
  debugPosts() {
    console.log("Posts data:", this.posts);
    return this.posts;
  },
},

   
  methods: {
    async createPost() {
       router.visit('/posts/create');
},
 
  allPost() {
    const data = {
      //host take from app host
      host: this.host, 
      //token take from app
      token: window.sessionToken,
      //session take from app
      session: window.sessionToken,
      //shop take from app
      shop: "1st-store-app.myshopify.com",
    }; 
    
    router.visit('/posts', { data }); 
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
