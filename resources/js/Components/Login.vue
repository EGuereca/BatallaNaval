<template>
  <div class="login-container">
    <!-- Background Effects -->
    <div class="bg-effects">
      <div class="grid-overlay"></div>
      <div class="scanner-line"></div>
    </div>

    <!-- Main Login Panel -->
    <div class="login-panel">
      <!-- Header -->
      <div class="login-header">
        <div class="system-logo">
          <div class="logo-icon">⚡</div>
          <div class="logo-text">
            <h1>BATTLESHIP</h1>
            <span class="subtitle">COMMAND SYSTEM</span>
          </div>
        </div>
        <div class="system-status">
          <div class="status-indicator" :class="{ active: systemOnline }"></div>
          <span class="status-text">{{ systemOnline ? 'SYSTEM ONLINE' : 'SYSTEM OFFLINE' }}</span>
        </div>
      </div>

      <!-- Login Form -->
      <div class="login-form-container">
        <div class="form-header">
          <h2>COMMANDER ACCESS</h2>
          <p class="access-level">CLASSIFIED CLEARANCE REQUIRED</p>
        </div>

        <form @submit.prevent="handleLogin" class="login-form">
          <!-- Email Field -->
          <div class="input-group">
            <label for="email" class="input-label">
              <span class="label-text">COMMANDER ID</span>
              <span class="label-required">*</span>
            </label>
            <div class="input-wrapper">
              <div class="input-icon">📧</div>
              <input
                id="email"
                v-model="credentials.email"
                type="email"
                class="form-input"
                :class="{ 
                  'error': errors.email,
                  'success': credentials.email && !errors.email && credentials.email.includes('@')
                }"
                placeholder="commander@battleship.mil"
                required
                @blur="validateEmail"
                @input="clearError('email')"
              />
              <div class="input-status" v-if="credentials.email && !errors.email && credentials.email.includes('@')">
                ✓
              </div>
            </div>
            <div v-if="errors.email" class="error-message">
              <span class="error-icon">⚠</span>
              {{ errors.email }}
            </div>
          </div>

          <!-- Password Field -->
          <div class="input-group">
            <label for="password" class="input-label">
              <span class="label-text">ACCESS CODE</span>
              <span class="label-required">*</span>
            </label>
            <div class="input-wrapper">
              <div class="input-icon">🔐</div>
              <input
                id="password"
                v-model="credentials.password"
                :type="showPassword ? 'text' : 'password'"
                class="form-input"
                :class="{ 
                  'error': errors.password,
                  'success': credentials.password && credentials.password.length >= 6
                }"
                placeholder="Enter your access code..."
                required
                @blur="validatePassword"
                @input="clearError('password')"
              />
              <button
                type="button"
                class="password-toggle"
                @click="togglePassword"
              >
                {{ showPassword ? '👁️' : '🔒' }}
              </button>
            </div>
            <div v-if="errors.password" class="error-message">
              <span class="error-icon">⚠</span>
              {{ errors.password }}
            </div>
          </div>

          <!-- Remember Me -->
          <div class="checkbox-group">
            <label class="checkbox-label">
              <input
                v-model="rememberMe"
                type="checkbox"
                class="checkbox-input"
              />
              <span class="checkbox-custom"></span>
              <span class="checkbox-text">REMEMBER THIS TERMINAL</span>
            </label>
          </div>

          <!-- Login Button -->
          <button
            type="submit"
            class="login-btn"
            :class="{ 'loading': isLoading }"
            :disabled="isLoading || !isFormValid"
          >
            <span v-if="isLoading" class="loading-spinner"></span>
            <span class="btn-text">
              {{ isLoading ? 'ACCESSING SYSTEM...' : 'INITIATE ACCESS' }}
            </span>
            <span v-if="!isLoading" class="btn-icon">🚀</span>
          </button>

          <!-- Error Display -->
          <div v-if="loginError" class="login-error">
            <div class="error-header">
              <span class="error-icon">❌</span>
              <span class="error-title">ACCESS DENIED</span>
            </div>
            <p class="error-description">{{ loginError }}</p>
          </div>
        </form>

        <!-- Footer Links -->
        <div class="form-footer">
          <div class="footer-links">
            <a href="#" class="footer-link" @click.prevent="forgotPassword">
              <span class="link-icon">🔑</span>
              Forgot Access Code?
            </a>
            <a href="#" class="footer-link" @click.prevent="createAccount">
              <span class="link-icon">⚡</span>
              Request Commander Access
            </a>
          </div>
          
          <div class="security-notice">
            <span class="notice-icon">🛡️</span>
            <span class="notice-text">SECURE CONNECTION ESTABLISHED</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Demo Credentials -->
    <div class="demo-panel">
      <h3>DEMO CREDENTIALS</h3>
      <div class="demo-item" @click="fillDemoCredentials">
        <strong>Email:</strong> commander@battleship.mil<br>
        <strong>Password:</strong> demo123
        <span class="demo-hint">Click to fill</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

// Reactive data
const credentials = ref({
  email: '',
  password: ''
})

const errors = ref({
  email: '',
  password: ''
})

const rememberMe = ref(false)
const showPassword = ref(false)
const isLoading = ref(false)
const loginError = ref('')
const systemOnline = ref(true)

// Computed properties
const isFormValid = computed(() => {
  return credentials.value.email && 
         credentials.value.password && 
         !errors.value.email && 
         !errors.value.password &&
         credentials.value.email.includes('@') &&
         credentials.value.password.length >= 6
})

// Methods
const validateEmail = () => {
  const email = credentials.value.email
  if (!email) {
    errors.value.email = 'Email is required'
  } else if (!email.includes('@')) {
    errors.value.email = 'Invalid email format'
  } else if (!email.includes('.')) {
    errors.value.email = 'Invalid domain format'
  } else {
    errors.value.email = ''
  }
}

const validatePassword = () => {
  const password = credentials.value.password
  if (!password) {
    errors.value.password = 'Access code is required'
  } else if (password.length < 6) {
    errors.value.password = 'Access code must be at least 6 characters'
  } else {
    errors.value.password = ''
  }
}

const clearError = (field) => {
  errors.value[field] = ''
  loginError.value = ''
}

const togglePassword = () => {
  showPassword.value = !showPassword.value
}

const handleLogin = async () => {
  validateEmail()
  validatePassword()
  
  if (!isFormValid.value) return

  isLoading.value = true
  loginError.value = ''

  try {
    await new Promise(resolve => setTimeout(resolve, 2000))
    
    if (credentials.value.email === 'commander@battleship.mil' && 
        credentials.value.password === 'demo123') {
      
      console.log('Login successful!', {
        email: credentials.value.email,
        rememberMe: rememberMe.value
      })

      const submit = () => {}
      
      // Here you would redirect to the main app
      // router.push('/lobby') or emit an event

      
    } else {
      loginError.value = 'Invalid Commander ID or Access Code. Please verify your credentials and try again.'
    }
    
  } catch (error) {
    loginError.value = 'System error occurred. Please try again later.'
  } finally {
    isLoading.value = false
  }
}

const forgotPassword = () => {
  console.log('Forgot password clicked')
  // Handle forgot password logic
}

const createAccount = () => {
  console.log('Create account clicked')
  // Handle account creation logic
}

const fillDemoCredentials = () => {
  credentials.value.email = 'commander@battleship.mil'
  credentials.value.password = 'demo123'
  errors.value.email = ''
  errors.value.password = ''
}

// Lifecycle
onMounted(() => {
  // Simulate system startup
  setTimeout(() => {
    systemOnline.value = true
  }, 1000)
})
</script>

<style scoped>
.login-container {
  background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 50%, #0f0f0f 100%);
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  font-family: 'Courier New', monospace;
  color: #00ff41;
  position: relative;
  overflow: hidden;
}

.bg-effects {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  pointer-events: none;
}

.grid-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-image: 
    linear-gradient(rgba(0, 255, 65, 0.1) 1px, transparent 1px),
    linear-gradient(90deg, rgba(0, 255, 65, 0.1) 1px, transparent 1px);
  background-size: 50px 50px;
  animation: grid-move 20s linear infinite;
}

.scanner-line {
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 2px;
  background: linear-gradient(90deg, transparent, #00ff41, transparent);
  animation: scanner 3s ease-in-out infinite;
}

@keyframes grid-move {
  0% { transform: translate(0, 0); }
  100% { transform: translate(50px, 50px); }
}

@keyframes scanner {
  0% { left: -100%; }
  100% { left: 100%; }
}

.login-panel {
  background: rgba(0, 0, 0, 0.9);
  border: 2px solid #00ff41;
  border-radius: 12px;
  padding: 40px;
  max-width: 500px;
  width: 100%;
  box-shadow: 0 0 50px rgba(0, 255, 65, 0.3);
  backdrop-filter: blur(10px);
  position: relative;
  z-index: 1;
}

.login-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 40px;
  padding-bottom: 20px;
  border-bottom: 1px solid #333;
}

.system-logo {
  display: flex;
  align-items: center;
  gap: 15px;
}

.logo-icon {
  font-size: 3rem;
  color: #00ff41;
  text-shadow: 0 0 20px #00ff41;
}

.logo-text h1 {
  margin: 0;
  font-size: 1.8rem;
  font-weight: bold;
  text-shadow: 0 0 10px #00ff41;
}

.subtitle {
  font-size: 0.9rem;
  color: #888;
  display: block;
  margin-top: 2px;
}

.system-status {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 5px;
}

.status-indicator {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: #666;
  transition: all 0.3s ease;
}

.status-indicator.active {
  background: #00ff41;
  box-shadow: 0 0 15px #00ff41;
  animation: pulse 2s infinite;
}

.status-text {
  font-size: 0.8rem;
  color: #888;
}

.form-header {
  text-align: center;
  margin-bottom: 30px;
}

.form-header h2 {
  margin: 0 0 10px 0;
  font-size: 1.6rem;
  text-shadow: 0 0 10px #00ff41;
}

.access-level {
  color: #ff9500;
  font-size: 0.9rem;
  margin: 0;
  font-weight: bold;
}

.input-group {
  margin-bottom: 25px;
}

.input-label {
  display: flex;
  align-items: center;
  gap: 5px;
  margin-bottom: 8px;
  font-size: 0.9rem;
  font-weight: bold;
  color: #00ff41;
}

.label-required {
  color: #ff4444;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  left: 15px;
  font-size: 1.2rem;
  z-index: 2;
}

.form-input {
  width: 100%;
  padding: 15px 15px 15px 50px;
  background: rgba(0, 0, 0, 0.8);
  border: 2px solid #333;
  border-radius: 6px;
  color: #00ff41;
  font-family: 'Courier New', monospace;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.form-input:focus {
  outline: none;
  border-color: #00ff41;
  box-shadow: 0 0 15px rgba(0, 255, 65, 0.3);
}

.form-input.success {
  border-color: #00ff41;
}

.form-input.error {
  border-color: #ff4444;
}

.form-input::placeholder {
  color: #666;
}

.input-status {
  position: absolute;
  right: 15px;
  color: #00ff41;
  font-size: 1.2rem;
}

.password-toggle {
  position: absolute;
  right: 15px;
  background: none;
  border: none;
  color: #888;
  cursor: pointer;
  font-size: 1.2rem;
  transition: color 0.3s ease;
}

.password-toggle:hover {
  color: #00ff41;
}

.error-message {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: 8px;
  color: #ff4444;
  font-size: 0.85rem;
}

.error-icon {
  font-size: 1rem;
}

.checkbox-group {
  margin-bottom: 30px;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 12px;
  cursor: pointer;
  font-size: 0.9rem;
}

.checkbox-input {
  display: none;
}

.checkbox-custom {
  width: 18px;
  height: 18px;
  border: 2px solid #333;
  border-radius: 3px;
  position: relative;
  transition: all 0.3s ease;
}

.checkbox-input:checked + .checkbox-custom {
  background: #00ff41;
  border-color: #00ff41;
}

.checkbox-input:checked + .checkbox-custom::after {
  content: '✓';
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: #000;
  font-weight: bold;
  font-size: 12px;
}

.login-btn {
  width: 100%;
  background: linear-gradient(45deg, #00ff41, #00cc33);
  border: none;
  padding: 18px 25px;
  font-size: 1.1rem;
  font-weight: bold;
  color: #000;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(0, 255, 65, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  margin-bottom: 20px;
}

.login-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(0, 255, 65, 0.6);
}

.login-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.loading-spinner {
  width: 20px;
  height: 20px;
  border: 2px solid #333;
  border-top: 2px solid #000;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

.login-error {
  background: rgba(255, 68, 68, 0.1);
  border: 1px solid #ff4444;
  border-radius: 6px;
  padding: 15px;
  margin-bottom: 20px;
}

.error-header {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 8px;
}

.error-title {
  font-weight: bold;
  color: #ff4444;
}

.error-description {
  margin: 0;
  color: #ff4444;
  font-size: 0.9rem;
}

.form-footer {
  border-top: 1px solid #333;
  padding-top: 20px;
}

.footer-links {
  display: flex;
  justify-content: space-between;
  margin-bottom: 15px;
  flex-wrap: wrap;
  gap: 10px;
}

.footer-link {
  color: #888;
  text-decoration: none;
  font-size: 0.9rem;
  display: flex;
  align-items: center;
  gap: 5px;
  transition: color 0.3s ease;
}

.footer-link:hover {
  color: #00ff41;
}

.security-notice {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  color: #888;
  font-size: 0.8rem;
}

.demo-panel {
  position: fixed;
  top: 20px;
  right: 20px;
  background: rgba(0, 0, 0, 0.9);
  border: 1px solid #333;
  border-radius: 6px;
  padding: 15px;
  font-size: 0.8rem;
  max-width: 250px;
}

.demo-panel h3 {
  margin: 0 0 10px 0;
  color: #00ff41;
  font-size: 0.9rem;
}

.demo-item {
  cursor: pointer;
  padding: 8px;
  border-radius: 4px;
  transition: background 0.3s ease;
  position: relative;
}

.demo-item:hover {
  background: rgba(0, 255, 65, 0.1);
}

.demo-hint {
  display: block;
  color: #666;
  font-size: 0.7rem;
  margin-top: 5px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

@keyframes pulse {
  0% { opacity: 1; }
  50% { opacity: 0.5; }
  100% { opacity: 1; }
}

/* Responsive Design */
@media (max-width: 768px) {
  .login-panel {
    padding: 30px 20px;
    margin: 10px;
  }
  
  .login-header {
    flex-direction: column;
    gap: 15px;
    text-align: center;
  }
  
  .footer-links {
    flex-direction: column;
    gap: 15px;
  }
  
  .demo-panel {
    position: static;
    margin-top: 20px;
    max-width: none;
  }
}
</style>