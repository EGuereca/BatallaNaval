<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import CyberpunkBackground from '@/Components/CyberpunkBackground.vue';
import SystemHeader from '@/Components/SystemHeader.vue';
import CyberpunkButton from '@/Components/CyberpunkButton.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Register" />

    <CyberpunkBackground>
        <div class="register-panel">
            <SystemHeader />

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
                    <CyberpunkButton
                        :to="route('login')"
                        icon="🔑"
                    >
                        ¿YA TIENES UNA CUENTA?
                    </CyberpunkButton>

                    <CyberpunkButton
                        type="submit"
                        icon="⚡"
                        :disabled="form.processing"
                    >
                        REGISTRAR COMANDANTE
                    </CyberpunkButton>
                </div>
            </form>
        </div>
    </CyberpunkBackground>
</template>

<style scoped>
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
    margin: 2rem auto;
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

@media (max-width: 640px) {
    .register-panel {
        padding: 20px;
        margin: 1rem;
    }
}
</style>
