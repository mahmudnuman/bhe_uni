<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <h2 class="text-center mb-4">Login</h2>
        <div class="card shadow-sm">
          <div class="card-body">
            <form @submit.prevent="login">
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input v-model="email" type="email" class="form-control" id="email" placeholder="Enter your email" required />
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input v-model="password" type="password" class="form-control" id="password" placeholder="Enter your password" required />
              </div>
              <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
            <p v-if="errorMessage" class="text-danger mt-3">{{ errorMessage }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      email: '',
      password: '',
      errorMessage: '',
    };
  },
  methods: {
    async login() {
      try {
        const response = await axios.post('http://127.0.0.1:8000/api/login', {
          email: this.email,
          password: this.password,
        });

        if (response.data.token && response.data.user) {
          // Store token and user data in localStorage
          localStorage.setItem('access_token', response.data.token);
          localStorage.setItem('user', JSON.stringify(response.data.user));

          // Redirect based on user role
          if (response.data.user.role === 'counselor') {
            this.$router.push('/counselor-dashboard');
          } else {
            this.$router.push('/dashboard');
          }
        } else {
          this.errorMessage = 'Invalid response from server';
        }
      } catch (error) {
        this.errorMessage = error.response?.data?.error || 'Login failed';
      }
    },
    logout() {
      // Clear stored data on logout
      localStorage.removeItem('access_token');
      localStorage.removeItem('user');
      this.$router.push('/login');
    }
  },
  mounted() {
    // Redirect logged-in users to the dashboard
    const storedUser = localStorage.getItem('user');
    if (storedUser) {
      const user = JSON.parse(storedUser);
      if (user.role === 'counselor') {
        this.$router.push('/counselor-dashboard');
      } else {
        this.$router.push('/dashboard');
      }
    }
  }
};
</script>

<style scoped>
/* Optional custom styles for the login page */
.container {
  max-width: 600px;
}
.card {
  border-radius: 10px;
}
</style>
