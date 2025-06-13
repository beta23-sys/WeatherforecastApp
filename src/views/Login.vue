<template>
  <div class="container py-5">
    <h2>Login</h2>
    <form @submit.prevent="login" class="mt-4" style="max-width:400px">
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input v-model="email" type="email" class="form-control" required />
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input v-model="password" type="password" class="form-control" required />
      </div>
      <div v-if="error" class="text-danger mb-3">{{ error }}</div>
      <button type="submit" class="btn btn-primary" :disabled="loading">
        {{ loading ? 'Logging in…' : 'Login' }}
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
    async login() {
      this.error = '';
      if (!this.email || !this.password) return;
      this.loading = true;
      try {
        const res = await fetch('login.php', {
          method: 'POST',
          headers: {'Content-Type':'application/json'},
          body: JSON.stringify({
            email: this.email,
            password: this.password
          })
        });
        const data = await res.json();
        if (data.success) {
          localStorage.setItem('userEmail', this.email);
          // notify App.vue
          this.$root.isLoggedIn = true;
          // redirect to originally intended page
          const dest = this.$route.query.redirect || '/';
          this.$router.replace(dest);
        } else {
          this.error = data.message;
        }
      } catch {
        this.error = 'Server error. Try again.';
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>
