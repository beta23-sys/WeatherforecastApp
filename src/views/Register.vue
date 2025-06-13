<template>
  <div class="min-vh-100 d-flex align-items-center justify-content-center bg-light">
    <div class="card shadow-lg border-0" style="width: 100%; max-width: 420px;">
      <div class="card-body p-4">
        <div class="text-center mb-4">
          <h2 class="card-title mb-2">Create Account</h2>
          <p class="text-muted">Sign up for a new account</p>
        </div>
        
        <form @submit.prevent="onRegister">
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
                autofocus
              />
            </div>
          </div>
          
          <div class="mb-3">
            <label class="form-label fw-semibold">Profile Name</label>
            <div class="input-group">
              <span class="input-group-text bg-transparent">
                <i class="fas fa-user text-muted"></i>
              </span>
              <input 
                v-model="profileName" 
                type="text" 
                class="form-control border-start-0"
                :class="{ 'is-invalid': error && !profileName }"
                placeholder="Enter your profile name"
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
            <div class="form-text">
              <small class="text-muted">Password must be at least 6 characters long</small>
            </div>
          </div>
          
          <div class="mb-3">
            <label class="form-label fw-semibold">Confirm Password</label>
            <div class="input-group">
              <span class="input-group-text bg-transparent">
                <i class="fas fa-lock text-muted"></i>
              </span>
              <input 
                v-model="confirmPassword" 
                :type="showConfirmPassword ? 'text' : 'password'" 
                class="form-control border-start-0 border-end-0"
                :class="{ 
                  'is-invalid': (error && !confirmPassword) || (confirmPassword && password !== confirmPassword),
                  'is-valid': confirmPassword && password === confirmPassword && password.length >= 6
                }"
                placeholder="Confirm your password"
                required 
              />
              <button 
                type="button" 
                class="btn btn-outline-secondary border-start-0"
                @click="showConfirmPassword = !showConfirmPassword"
              >
                <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
              </button>
            </div>
            <div v-if="confirmPassword && password !== confirmPassword" class="form-text">
              <small class="text-danger">Passwords do not match</small>
            </div>
            <div v-else-if="confirmPassword && password === confirmPassword && password.length >= 6" class="form-text">
              <small class="text-success">Passwords match</small>
            </div>
          </div>
          
          <div v-if="error" class="alert alert-danger d-flex align-items-center mb-3">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ error }}
          </div>
          
          <div v-if="success" class="alert alert-success d-flex align-items-center mb-3">
            <i class="fas fa-check-circle me-2"></i>
            {{ success }}
          </div>
          
          <button type="submit" class="btn btn-success w-100 py-2 mb-3" :disabled="loading || !isFormValid">
            <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
            {{ loading ? 'Creating Account...' : 'Create Account' }}
          </button>
        </form>
        
        <div class="text-center border-top pt-3">
          <span class="text-muted small">Already have an account? </span>
          <router-link to="/login" class="text-decoration-none fw-semibold">Sign In</router-link>
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
      profileName: '',
      password: '',
      confirmPassword: '',
      showPassword: false,
      showConfirmPassword: false,
      loading: false,
      error: '',
      success: ''
    };
  },
  computed: {
    isFormValid() {
      return this.email && 
             this.profileName && 
             this.password && 
             this.confirmPassword && 
             this.password === this.confirmPassword &&
             this.password.length >= 6;
    }
  },
  methods: {
    async onRegister() {
      this.error = '';
      this.success = '';
      
      // Validate form
      if (!this.email || !this.profileName || !this.password || !this.confirmPassword) {
        this.error = 'Please fill in all fields';
        return;
      }
      
      if (this.password !== this.confirmPassword) {
        this.error = 'Passwords do not match';
        return;
      }
      
      if (this.password.length < 6) {
        this.error = 'Password must be at least 6 characters long';
        return;
      }
      
      this.loading = true;
      
      try {
        // TODO: Replace with your actual API call
        const res = await fetch('register.php', {
          method: 'POST',
          headers: {'Content-Type':'application/json'},
          body: JSON.stringify({
            email: this.email,
            profileName: this.profileName,
            password: this.password
          })
        });
        
        const data = await res.json();
        
        if (data.success) {
          this.success = 'Account created successfully! Redirecting...';
          // Redirect to login page after 2 seconds
          setTimeout(() => {
            this.$router.push('/login');
          }, 2000);
        } else {
          this.error = data.message || 'Registration failed';
        }
      } catch (err) {
        this.error = 'Server error. Please try again.';
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>