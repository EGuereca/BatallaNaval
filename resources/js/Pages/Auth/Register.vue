<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <div class="register-container">
        <!-- Background Effects -->
        <div class="bg-effects">
            <div class="grid-overlay"></div>
            <div class="scanner-line"></div>
        </div>

        <div class="register-panel">
            <div class="register-header">
                <div class="system-logo">
                    <div class="logo-icon">⚡</div>
                    <div class="logo-text">
                        <h1>BATTLESHIP</h1>
                        <span class="subtitle">COMMAND SYSTEM</span>
                    </div>
                </div>
                <div class="system-status">
                    <div class="status-indicator active"></div>
                    <span class="status-text">SYSTEM ONLINE</span>
                </div>
            </div>

            <form @submit.prevent="submit" class="register-form">
                <div class="form-section">
                    <h2 class="section-title">REGISTRO DE COMANDANTE</h2>
                    
                    <div class="form-group">
                        <label for="name">NOMBRE DEL COMANDANTE</label>
                        <input
                            id="name"
                            type="text"
                            class="form-input"
                            v-model="form.name"
                            required
                            autofocus
                            autocomplete="name"
                        >
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div class="form-group">
                        <label for="email">EMAIL DE CONTACTO</label>
                        <input
                            id="email"
                            type="email"
                            class="form-input"
                            v-model="form.email"
                            required
                            autocomplete="username"
                        >
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div class="form-group">
                        <label for="password">CONTRASEÑA</label>
                        <input
                            id="password"
                            type="password"
                            class="form-input"
                            v-model="form.password"
                            required
                            autocomplete="new-password"
                        >
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">CONFIRMAR CONTRASEÑA</label>
                        <input
                            id="password_confirmation"
                            type="password"
                            class="form-input"
                            v-model="form.password_confirmation"
                            required
                            autocomplete="new-password"
                        >
                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
                    </div>
                </div>

                <div class="form-actions">
                    <Link
                        :href="route('login')"
                        class="action-link"
                    >
                        ¿YA TIENES UNA CUENTA?
                    </Link>

                    <button 
                        class="submit-btn"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        REGISTRAR COMANDANTE
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
.register-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 50%, #0a0a0a 100%);
    color: #00ff41;
    font-family: 'Courier New', monospace;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
    position: relative;
    overflow: hidden;
}

.bg-effects {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 0;
}

.grid-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: 
        linear-gradient(rgba(0, 255, 65, 0.1) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0, 255, 65, 0.1) 1px, transparent 1px);
    background-size: 20px 20px;
}

.scanner-line {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 2px;
    background: #00ff41;
    box-shadow: 0 0 20px #00ff41;
    animation: scan 3s linear infinite;
}

@keyframes scan {
    0% { top: 0; }
    100% { top: 100%; }
}

.register-panel {
    background: rgba(0, 0, 0, 0.8);
    border: 2px solid #00ff41;
    border-radius: 10px;
    padding: 30px;
    width: 100%;
    max-width: 500px;
    position: relative;
    z-index: 1;
    backdrop-filter: blur(10px);
}

.register-header {
    text-align: center;
    margin-bottom: 30px;
}

.system-logo {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
    margin-bottom: 20px;
}

.logo-icon {
    font-size: 2.5rem;
    animation: pulse 2s infinite;
}

.logo-text {
    text-align: left;
}

.logo-text h1 {
    font-size: 2rem;
    margin: 0;
    text-shadow: 0 0 10px #00ff41;
    letter-spacing: 2px;
}

.subtitle {
    font-size: 1rem;
    opacity: 0.8;
}

.system-status {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.status-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #00ff41;
    box-shadow: 0 0 15px #00ff41;
    animation: pulse 2s infinite;
}

.status-text {
    font-size: 0.9rem;
    opacity: 0.8;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.register-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-section {
    background: rgba(0, 255, 65, 0.05);
    border: 1px solid #00ff41;
    border-radius: 8px;
    padding: 20px;
}

.section-title {
    font-size: 1.2rem;
    margin: 0 0 20px 0;
    text-align: center;
    color: #00ff41;
    text-shadow: 0 0 10px #00ff41;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-size: 0.9rem;
    color: #00ff41;
}

.form-input {
    width: 100%;
    padding: 10px;
    background: rgba(0, 0, 0, 0.8);
    border: 1px solid #333;
    border-radius: 5px;
    color: #00ff41;
    font-family: 'Courier New', monospace;
    transition: all 0.3s ease;
}

.form-input:focus {
    outline: none;
    border-color: #00ff41;
    box-shadow: 0 0 10px rgba(0, 255, 65, 0.3);
}

.form-actions {
    display: flex;
    flex-direction: column;
    gap: 15px;
    align-items: center;
}

.action-link {
    color: #00ff41;
    text-decoration: none;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.action-link:hover {
    text-shadow: 0 0 10px #00ff41;
}

.submit-btn {
    width: 100%;
    padding: 12px;
    background: #00ff41;
    color: #000;
    border: none;
    border-radius: 5px;
    font-family: 'Courier New', monospace;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
}

.submit-btn:hover:not(:disabled) {
    background: #00cc33;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 255, 65, 0.4);
}

.submit-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

@media (max-width: 640px) {
    .register-panel {
        padding: 20px;
    }

    .logo-text h1 {
        font-size: 1.5rem;
    }

    .subtitle {
        font-size: 0.8rem;
    }
}
</style>
