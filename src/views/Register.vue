<template>
  <div class="container py-5">
    <h2>Register</h2>
    <form @submit.prevent="register" class="mt-4" style="max-width:400px">
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input v-model="email" type="email" class="form-control" required />
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input v-model="password" type="password" class="form-control" required minlength="6" />
      </div>
      <div v-if="error" class="text-danger mb-3">{{ error }}</div>
      <button type="submit" class="btn btn-success" :disabled="loading">
        <span v-if="loading">Registering...</span>
        <span v-else>Register</span>
      </button>
    </form>
  </div>
</template>

<script>
export default {
  data() {
    return {
      email: '',
      password: '',
      error: '',
      loading: false
    };
  },
  methods: {
    async register() {
      this.error = '';
      this.loading = true;
      
      // Client-side validation
      if (!this.email || !this.password) {
        this.error = 'Email and password are required';
        this.loading = false;
        return;
      }
      
      if (this.password.length < 6) {
        this.error = 'Password must be at least 6 characters';
        this.loading = false;
        return;
      }

      try {
        const response = await fetch('register.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            email: this.email,
            password: this.password
          })
        });

        const data = await response.json();

        if (!response.ok) {
          throw new Error(data.message || 'Registration failed');
        }

        if (data.success) {
          localStorage.setItem('userEmail', this.email);
          this.$root.isLoggedIn = true;
          this.$router.push({ name: 'Home' });
        } else {
          this.error = data.message || 'Registration failed';
        }
      } catch (error) {
        this.error = error.message || 'Server error occurred';
        console.error('Registration error:', error);
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>