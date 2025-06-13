<template>
  <div class="min-vh-100 d-flex align-items-center justify-content-center bg-light">
    <div class="card shadow-lg border-0" style="width: 100%; max-width: 400px;">
      <div class="card-body p-4">
        <div class="text-center mb-4">
          <h2 class="card-title mb-2">Login</h2>
          <p class="text-muted">Sign in to your account</p>
        </div>
        
        <form @submit.prevent="login">
          <div class="mb-3">
            <label class="form-label fw-semibold">Email Address</label>
            <div class="input-group">
              <span class="input-group-text bg-transparent">
                <i class="fas fa-envelope text-muted"></i>
              </span>
              <input 
                v-model="email" 
                type="email" 
                class="form-control border-start-0"
                :class="{ 'is-invalid': error && !email }"
                placeholder="Enter your email"
                required 
              />
            </div>
          </div>
          
          <div class="mb-3">
            <label class="form-label fw-semibold">Password</label>
            <div class="input-group">
              <span class="input-group-text bg-transparent">
                <i class="fas fa-lock text-muted"></i>
              </span>
              <input 
                v-model="password" 
                :type="showPassword ? 'text' : 'password'" 
                class="form-control border-start-0 border-end-0"
                :class="{ 'is-invalid': error && !password }"
                placeholder="Enter your password"
                required 
              />
              <button 
                type="button" 
                class="btn btn-outline-secondary border-start-0"
                @click="showPassword = !showPassword"
              >
                <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
              </button>
            </div>
          </div>
          
          <div v-if="error" class="alert alert-danger d-flex align-items-center mb-3">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ error }}
          </div>
          
          <button type="submit" class="btn btn-primary w-100 py-2 mb-3" :disabled="loading">
            <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
            {{ loading ? 'Signing In...' : 'Sign In' }}
          </button>
        </form>
        
        <div class="text-center border-top pt-3">
          <span class="text-muted small">Don't have an account? </span>
          <router-link to="/register"  class="text-decoration-none fw-semibold">Register</router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      email: '',
      password: '',
      error: '',
      loading: false,
      showPassword: false
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