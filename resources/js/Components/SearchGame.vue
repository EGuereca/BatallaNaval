<template>
  <div class="lobby-container">
    <!-- Header -->
    <div class="lobby-header">
      <h1 class="lobby-title">BATTLE COMMAND FINDER</h1>
      <div class="player-status">
        <div class="status-indicator active"></div>
        <span class="player-name">COMMANDER_{{ playerName }}</span>
      </div>
    </div>

    <!-- Controls Panel -->
    <div class="controls-panel">
      <button @click="refreshGames" class="action-btn refresh-btn" :disabled="isLoading">
        <span class="btn-icon">🔄</span>
        {{ isLoading ? 'SCANNING...' : 'REFRESH GAMES' }}
      </button>
      
      <button @click="createGame" class="action-btn create-btn">
        <span class="btn-icon">⚡</span>
        CREATE NEW BATTLE
      </button>
      
      <div class="search-filter">
        <input 
          v-model="searchFilter" 
          type="text" 
          placeholder="FILTER BY COMMANDER NAME..."
          class="search-input"
        >
      </div>
    </div>

    <!-- Games List -->
    <div class="games-section">
      <div class="section-header">
        <h2>ACTIVE BATTLEFIELDS</h2>
        <div class="games-counter">
          <span class="counter-text">{{ filteredGames.length }} BATTLES AVAILABLE</span>
        </div>
      </div>

      <div class="games-list" v-if="filteredGames.length > 0">
        <div 
          v-for="game in filteredGames" 
          :key="game.id"
          class="game-card"
          :class="{ 'private-game': game.isPrivate, 'full-game': game.players >= game.maxPlayers }"
          @click="joinGame(game)"
        >
          <div class="game-header">
            <div class="game-title">
              <span class="game-name">{{ game.name }}</span>
              <div class="game-badges">
                <span v-if="game.players >= game.maxPlayers" class="badge full">❌ FULL</span>
              </div>
            </div>
            <div class="game-status" :class="getStatusClass(game.status)">
              {{ game.status }}
            </div>
          </div>

          <div class="game-info">
            <div class="info-row">
              <div class="info-item">
                <span class="info-label">COMMANDER:</span>
                <span class="info-value commander-name">{{ game.player1 }}</span>
              </div>
              <div class="info-item">
                <span class="info-label">PLAYERS:</span>
                <span class="info-value" v-if="game.player2 == null">1/2</span>
                <span class="info-value" v-else>2/2</span>
              </div>
            </div>
            
            <div class="info-row">
              <div class="info-item">
                <span class="info-label">MAP SIZE:</span>
                <span class="info-value">{{ 8 }}x{{ 8 }}</span>
              </div>
            </div>
            
            <div class="info-row">
              <div class="info-item">
                <span class="info-label">CREATED:</span>
                <span class="info-value">{{ formatTime(game.createdAt) }}</span>
              </div>
            </div>
          </div>

          <div class="game-footer">
            <div class="join-button" :class="{ disabled: game.player2 != null }">
              <span v-if="game.player2 != null">BATTLEFIELD FULL</span>
              <span v-else-if="game.isPrivate">ENTER ACCESS CODE</span>
              <span v-else>JOIN BATTLE</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="empty-state">
        <div class="empty-icon">🌌</div>
        <h3>{{token}}</h3>
        <p>{{ searchFilter ? 'Try adjusting your search filter' : 'Create a new battle to start commanding!' }}</p>
      </div>
    </div>

    <!-- Loading Overlay -->
    <div v-if="isLoading" class="loading-overlay">
      <div class="loading-spinner"></div>
      <div class="loading-text">SCANNING FOR BATTLES...</div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const playerName = ref('ALPHA_7')
const isLoading = ref(false)
const searchFilter = ref('')
const token = localStorage.getItem('token') ?? 'No jalo pa'

const games = ref([])

const filteredGames = computed(() => {
  if (!searchFilter.value) return games.value
  
  return games.value.filter(game => 
    game.name.toLowerCase().includes(searchFilter.value.toLowerCase()) ||
    game.player1.toLowerCase().includes(searchFilter.value.toLowerCase())
  )
})

const refreshGames = async () => {
  isLoading.value = true
  try {
    const response = await axios.get('/api/games', {
      headers: {
        Authorization: `Bearer ${token}`
      }
    })
    games.value = response.data.games.map(game => ({
      id: game.id,
      name: game.name,
      player1: game.player1,
      player2: game.player2,
      status: game.status,
      createdAt: new Date(game.created_at),
    }))
  } catch (e) {
    games.value = []
  }
  isLoading.value = false
}

const createGame = () => {
  console.log('Creating new game...')
  // Aquí iría la lógica para crear una nueva partida
}

const joinGame = (game) => {
  if (game.players >= game.maxPlayers) return
  
  console.log(`Joining game: ${game.name}`)
  // Aquí iría la lógica para unirse a la partida
}

const getStatusClass = (status) => {
  switch(status.toLowerCase()) {
    case 'waiting': return 'status-waiting'
    case 'in_progress': return 'status-active'
    case 'finished': return 'status-finished'
    default: return ''
  }
}

const getPingClass = (ping) => {
  if (ping < 50) return 'ping-good'
  if (ping < 100) return 'ping-medium'
  return 'ping-bad'
}

const formatTime = (date) => {
  const now = new Date()
  const diff = Math.floor((now - date) / 1000)
  
  if (diff < 60) return `${diff}s ago`
  if (diff < 3600) return `${Math.floor(diff / 60)}m ago`
  return `${Math.floor(diff / 3600)}h ago`
}

onMounted(() => {
  // Simular carga inicial
  refreshGames()
})
</script>

<style scoped>
.lobby-container {
  background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 50%, #0f0f0f 100%);
  min-height: 100vh;
  padding: 20px;
  font-family: 'Courier New', monospace;
  color: #00ff41;
  position: relative;
}

.lobby-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  padding: 20px;
  background: rgba(0, 255, 65, 0.1);
  border: 2px solid #00ff41;
  border-radius: 10px;
  backdrop-filter: blur(10px);
}

.lobby-title {
  font-size: 2.5rem;
  font-weight: bold;
  margin: 0;
  text-shadow: 0 0 20px #00ff41;
  letter-spacing: 3px;
}

.player-status {
  display: flex;
  align-items: center;
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

.player-name {
  font-size: 1.2rem;
  font-weight: bold;
  text-shadow: 0 0 10px #00ff41;
}

.controls-panel {
  display: flex;
  gap: 20px;
  align-items: center;
  margin-bottom: 30px;
  flex-wrap: wrap;
}

.action-btn {
  background: linear-gradient(45deg, #00ff41, #00cc33);
  border: none;
  padding: 12px 25px;
  font-size: 1rem;
  font-weight: bold;
  color: #000;
  border-radius: 5px;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(0, 255, 65, 0.4);
  display: flex;
  align-items: center;
  gap: 8px;
}

.action-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(0, 255, 65, 0.6);
}

.action-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-icon {
  font-size: 1.2rem;
}

.search-filter {
  flex: 1;
  min-width: 250px;
}

.search-input {
  width: 100%;
  padding: 12px 15px;
  background: rgba(0, 0, 0, 0.8);
  border: 2px solid #333;
  border-radius: 5px;
  color: #00ff41;
  font-family: 'Courier New', monospace;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.search-input:focus {
  outline: none;
  border-color: #00ff41;
  box-shadow: 0 0 15px rgba(0, 255, 65, 0.3);
}

.search-input::placeholder {
  color: #666;
}

.games-section {
  margin-bottom: 30px;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding-bottom: 10px;
  border-bottom: 2px solid #00ff41;
}

.section-header h2 {
  margin: 0;
  font-size: 1.8rem;
  text-shadow: 0 0 10px #00ff41;
}

.games-counter {
  color: #888;
  font-size: 1rem;
}

.games-list {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.game-card {
  background: rgba(0, 0, 0, 0.8);
  border: 2px solid #333;
  border-radius: 8px;
  padding: 20px;
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.game-card:hover {
  border-color: #00ff41;
  background: rgba(0, 255, 65, 0.05);
  box-shadow: 0 5px 20px rgba(0, 255, 65, 0.2);
  transform: translateY(-2px);
}

.game-card.private-game {
  border-left: 4px solid #ff9500;
}

.game-card.full-game {
  opacity: 0.6;
  cursor: not-allowed;
}

.game-card.full-game:hover {
  transform: none;
  border-color: #333;
  background: rgba(0, 0, 0, 0.8);
  box-shadow: none;
}

.game-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 15px;
}

.game-title {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.game-name {
  font-size: 1.4rem;
  font-weight: bold;
  color: #00ff41;
  text-shadow: 0 0 5px #00ff41;
}

.game-badges {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.badge {
  padding: 4px 8px;
  border-radius: 3px;
  font-size: 0.75rem;
  font-weight: bold;
}

.badge.full {
  background: #ff4444;
  color: #fff;
}

.game-status {
  padding: 6px 12px;
  border-radius: 4px;
  font-size: 0.9rem;
  font-weight: bold;
}

.status-waiting {
  background: rgba(0, 255, 65, 0.2);
  color: #00ff41;
  border: 1px solid #00ff41;
}

.status-active {
  background: rgba(255, 149, 0, 0.2);
  color: #ff9500;
  border: 1px solid #ff9500;
}

.status-finished {
  background: rgba(68, 68, 255, 0.2);
  color: #4444ff;
  border: 1px solid #4444ff;
}

.game-info {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-bottom: 15px;
}

.info-row {
  display: flex;
  justify-content: space-between;
  gap: 20px;
}

.info-item {
  display: flex;
  flex-direction: column;
  gap: 2px;
  flex: 1;
}

.info-label {
  font-size: 0.8rem;
  color: #888;
  font-weight: bold;
}

.info-value {
  font-size: 1rem;
  color: #00ff41;
}

.commander-name {
  color: #ffd700;
  font-weight: bold;
}

.ping.ping-good {
  color: #00ff41;
}

.ping.ping-medium {
  color: #ff9500;
}

.ping.ping-bad {
  color: #ff4444;
}

.game-footer {
  border-top: 1px solid #333;
  padding-top: 15px;
}

.join-button {
  background: linear-gradient(45deg, #00ff41, #00cc33);
  color: #000;
  padding: 10px 20px;
  border-radius: 5px;
  text-align: center;
  font-weight: bold;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.join-button.disabled {
  background: #666;
  color: #333;
  cursor: not-allowed;
}

.empty-state {
  text-align: center;
  padding: 60px 20px;
  color: #666;
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 20px;
}

.empty-state h3 {
  font-size: 1.5rem;
  margin-bottom: 10px;
  color: #888;
}

.loading-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.8);
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.loading-spinner {
  width: 50px;
  height: 50px;
  border: 3px solid #333;
  border-top: 3px solid #00ff41;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 20px;
}

.loading-text {
  font-size: 1.2rem;
  color: #00ff41;
  text-shadow: 0 0 10px #00ff41;
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
  .lobby-header {
    flex-direction: column;
    gap: 15px;
    text-align: center;
  }
  
  .lobby-title {
    font-size: 2rem;
  }
  
  .controls-panel {
    flex-direction: column;
    align-items: stretch;
  }
  
  .info-row {
    flex-direction: column;
    gap: 10px;
  }
  
  .game-header {
    flex-direction: column;
    gap: 10px;
  }
}
</style>