<script setup>
import { Head, Link } from '@inertiajs/vue3';
import CyberpunkBackground from '@/Components/CyberpunkBackground.vue';
import SystemHeader from '@/Components/SystemHeader.vue';
import CyberpunkButton from '@/Components/CyberpunkButton.vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});
</script>

<template>
    <Head title="Welcome" />

    <CyberpunkBackground>
        <div class="welcome-content">
            <SystemHeader />

            <div class="auth-links" v-if="canLogin">
                <CyberpunkButton
                v-if="$page.props.auth.user"
                    :to="route('dashboard')"
                    icon="🎮"
            >
                    DASHBOARD
                </CyberpunkButton>

            <template v-else>
                <Link
                    :href="route('login')"
                        class="cyberpunk-button"
                >
                        <span class="button-icon">🔑</span>
                        INICIAR SESIÓN
                    </Link>

                <Link
                    v-if="canRegister"
                    :href="route('register')"
                        class="cyberpunk-button"
                >
                        <span class="button-icon">⚡</span>
                        REGISTRARSE
                    </Link>
            </template>
            </div>

            <div class="game-info">
                <div class="info-card">
                    <div class="card-icon">🎯</div>
                    <h2>OBJETIVO</h2>
                    <p>Hunde todos los barcos enemigos antes de que hundan los tuyos. Cada barco ocupa diferentes espacios en el tablero.</p>
                            </div>

                <div class="info-card">
                    <div class="card-icon">⚓</div>
                    <h2>FLOTA</h2>
                    <p>Comanda una flota de 5 barcos: Portaaviones (5), Acorazado (4), Crucero (3), Submarino (3) y Destructor (2).</p>
                        </div>

                <div class="info-card">
                    <div class="card-icon">🎲</div>
                    <h2>JUEGO</h2>
                    <p>Turnos alternados. Selecciona coordenadas para atacar. ¡Acierta y destruye la flota enemiga!</p>
                            </div>

                <div class="info-card">
                    <div class="card-icon">🏆</div>
                    <h2>VICTORIA</h2>
                    <p>El primer jugador en hundir todos los barcos enemigos gana la batalla. ¡Demuestra tu estrategia!</p>
                </div>
            </div>

            <div class="system-info">
                <div class="version-info">
                    <span class="info-label">SISTEMA</span>
                    <span class="info-value">v{{ laravelVersion }}</span>
                </div>
                <div class="version-info">
                    <span class="info-label">PHP</span>
                    <span class="info-value">v{{ phpVersion }}</span>
                </div>
            </div>
        </div>
    </CyberpunkBackground>
</template>

<style scoped>
.welcome-content {
    position: relative;
    z-index: 1;
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.auth-links {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-bottom: 3rem;
}

.cyberpunk-button {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: rgba(0, 255, 65, 0.1);
    border: 1px solid #00ff41;
    border-radius: 5px;
    color: #00ff41;
    text-decoration: none;
    font-weight: bold;
    font-family: 'Courier New', monospace;
    transition: all 0.3s ease;
}

.cyberpunk-button:hover {
    background: rgba(0, 255, 65, 0.2);
    transform: translateY(-2px);
    box-shadow: 0 0 20px rgba(0, 255, 65, 0.3);
}

.button-icon {
    font-size: 1.2rem;
}

.game-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.info-card {
    background: rgba(0, 0, 0, 0.8);
    border: 1px solid #00ff41;
    border-radius: 10px;
    padding: 1.5rem;
    text-align: center;
    transition: all 0.3s ease;
}

.info-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0 20px rgba(0, 255, 65, 0.2);
}

.card-icon {
    font-size: 2rem;
    margin-bottom: 1rem;
}

.info-card h2 {
    font-size: 1.2rem;
    margin-bottom: 1rem;
    color: #00ff41;
    text-shadow: 0 0 10px #00ff41;
}

.info-card p {
    font-size: 0.9rem;
    line-height: 1.5;
    opacity: 0.8;
}

.system-info {
    display: flex;
    justify-content: center;
    gap: 2rem;
    padding: 1rem;
    background: rgba(0, 0, 0, 0.5);
    border-top: 1px solid rgba(0, 255, 65, 0.2);
}

.version-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.info-label {
    font-size: 0.8rem;
    opacity: 0.6;
}

.info-value {
    font-size: 0.8rem;
    color: #00ff41;
}

@media (max-width: 768px) {
    .welcome-content {
        padding: 1rem;
    }

    .auth-links {
        flex-direction: column;
    }

    .game-info {
        grid-template-columns: 1fr;
    }

    .system-info {
        flex-direction: column;
        align-items: center;
        gap: 1rem;
    }
}
</style>
