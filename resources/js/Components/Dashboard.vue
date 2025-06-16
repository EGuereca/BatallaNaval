<template>
  <div class="dashboard-container">
    <!-- Header -->
    <div class="dashboard-header">
      <h1 class="title">COMANDO NAVAL - DASHBOARD</h1>
      <div class="status-bar">
        <span class="status-item">STATUS: OPERATIVO</span>
        <span class="status-item">{{ currentTime }}</span>
      </div>
    </div>

    <!-- Main Content -->
    <div class="dashboard-content">
      <!-- Stats Cards -->
      <div class="stats-grid">
        <div class="stat-card victories">
          <div class="stat-header">
            <span class="stat-icon">🏆</span>
            <span class="stat-label">VICTORIAS</span>
          </div>
          <div class="stat-value">{{ gameStats.wins }}</div>
          <div class="stat-percentage">{{ winPercentage }}%</div>
        </div>

        <div class="stat-card defeats">
          <div class="stat-header">
            <span class="stat-icon">💥</span>
            <span class="stat-label">DERROTAS</span>
          </div>
          <div class="stat-value">{{ gameStats.losses }}</div>
          <div class="stat-percentage">{{ lossPercentage }}%</div>
        </div>

        <div class="stat-card total">
          <div class="stat-header">
            <span class="stat-icon">🎯</span>
            <span class="stat-label">TOTAL BATALLAS</span>
          </div>
          <div class="stat-value">{{ totalGames }}</div>
          <div class="stat-percentage">100%</div>
        </div>
      </div>

      <!-- Charts and History Grid -->
      <div class="main-grid">
        <!-- Chart Section -->
        <div class="chart-section">
          <div class="section-header">
            <h2>ANÁLISIS TÁCTICO</h2>
          </div>
          <div class="chart-container">
            <canvas ref="chartCanvas" width="400" height="200"></canvas>
          </div>
        </div>

        <!-- Recent Battles -->
        <div class="history-section">
          <div class="section-header">
            <h2>HISTORIAL DE BATALLAS</h2>
          </div>
          <div class="battles-list">
            <div 
              v-for="(battle, index) in recentBattles" 
              :key="index"
              :class="['battle-item', battle.result]"
            >
              <div class="battle-status">
                <span :class="['status-dot', battle.result]"></span>
                <span class="battle-result">{{ battle.result.toUpperCase() }}</span>
              </div>
              <div class="battle-details">
                <div class="battle-date">{{ formatDate(battle.date) }}</div>
                <div class="battle-duration">Duración: {{ battle.duration }}</div>
                <div class="battle-score">Disparos: {{ battle.shots }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tactical Grid Background -->
    <div class="grid-overlay"></div>
  </div>
</template>

<script>
export default {
  name: 'BattleshipDashboard',
  data() {
    return {
      currentTime: '',
      gameStats: {
        wins: 15,
        losses: 8
      },
      recentBattles: [
        {
          result: 'victoria',
          date: new Date('2024-06-15T14:30:00'),
          duration: '4m 23s',
          shots: 32
        },
        {
          result: 'derrota',
          date: new Date('2024-06-15T13:45:00'),
          duration: '6m 12s',
          shots: 45
        },
        {
          result: 'victoria',
          date: new Date('2024-06-15T12:20:00'),
          duration: '3m 45s',
          shots: 28
        },
        {
          result: 'victoria',
          date: new Date('2024-06-14T16:30:00'),
          duration: '5m 18s',
          shots: 38
        },
        {
          result: 'derrota',
          date: new Date('2024-06-14T15:15:00'),
          duration: '7m 02s',
          shots: 52
        }
      ],
      chart: null
    }
  },
  computed: {
    totalGames() {
      return this.gameStats.wins + this.gameStats.losses;
    },
    winPercentage() {
      if (this.totalGames === 0) return 0;
      return Math.round((this.gameStats.wins / this.totalGames) * 100);
    },
    lossPercentage() {
      if (this.totalGames === 0) return 0;
      return Math.round((this.gameStats.losses / this.totalGames) * 100);
    }
  },
  mounted() {
    this.updateTime();
    this.timeInterval = setInterval(this.updateTime, 1000);
    this.initChart();
  },
  beforeUnmount() {
    if (this.timeInterval) {
      clearInterval(this.timeInterval);
    }
  },
  methods: {
    updateTime() {
      const now = new Date();
      this.currentTime = now.toLocaleTimeString('es-ES', { 
        hour12: false,
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
      });
    },
    formatDate(date) {
      return date.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
      });
    },
    initChart() {
      const canvas = this.$refs.chartCanvas;
      const ctx = canvas.getContext('2d');
      
      // Clear canvas
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      
      // Draw chart background
      ctx.fillStyle = 'rgba(0, 255, 136, 0.1)';
      ctx.fillRect(0, 0, canvas.width, canvas.height);
      
      // Chart data
      const wins = this.gameStats.wins;
      const losses = this.gameStats.losses;
      const total = wins + losses;
      
      if (total === 0) return;
      
      // Draw pie chart
      const centerX = canvas.width / 2;
      const centerY = canvas.height / 2;
      const radius = 80;
      
      let currentAngle = -Math.PI / 2;
      
      // Wins section
      const winAngle = (wins / total) * 2 * Math.PI;
      ctx.beginPath();
      ctx.moveTo(centerX, centerY);
      ctx.arc(centerX, centerY, radius, currentAngle, currentAngle + winAngle);
      ctx.closePath();
      ctx.fillStyle = '#00ff88';
      ctx.fill();
      ctx.strokeStyle = '#000';
      ctx.lineWidth = 2;
      ctx.stroke();
      
      currentAngle += winAngle;
      
      // Losses section
      const lossAngle = (losses / total) * 2 * Math.PI;
      ctx.beginPath();
      ctx.moveTo(centerX, centerY);
      ctx.arc(centerX, centerY, radius, currentAngle, currentAngle + lossAngle);
      ctx.closePath();
      ctx.fillStyle = '#ff4444';
      ctx.fill();
      ctx.strokeStyle = '#000';
      ctx.lineWidth = 2;
      ctx.stroke();
      
      // Center circle
      ctx.beginPath();
      ctx.arc(centerX, centerY, 25, 0, 2 * Math.PI);
      ctx.fillStyle = '#0a0a0a';
      ctx.fill();
      ctx.strokeStyle = '#00ff88';
      ctx.lineWidth = 2;
      ctx.stroke();
      
      // Legend
      ctx.font = '12px "Courier New", monospace';
      ctx.fillStyle = '#00ff88';
      ctx.fillText(`VICTORIAS: ${wins}`, 10, canvas.height - 30);
      ctx.fillStyle = '#ff4444';
      ctx.fillText(`DERROTAS: ${losses}`, 10, canvas.height - 10);
    },
    // Método para agregar nueva batalla (para usar desde el juego)
    addBattle(result, duration, shots) {
      const newBattle = {
        result: result,
        date: new Date(),
        duration: duration,
        shots: shots
      };
      
      this.recentBattles.unshift(newBattle);
      if (this.recentBattles.length > 5) {
        this.recentBattles.pop();
      }
      
      if (result === 'victoria') {
        this.gameStats.wins++;
      } else {
        this.gameStats.losses++;
      }
      
      this.$nextTick(() => {
        this.initChart();
      });
    }
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&display=swap');

.dashboard-container {
  min-height: 100vh;
  background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 50%, #0a0a0a 100%);
  color: #00ff88;
  font-family: 'Orbitron', 'Courier New', monospace;
  position: relative;
  overflow: hidden;
}

.grid-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-image: 
    linear-gradient(rgba(0, 255, 136, 0.1) 1px, transparent 1px),
    linear-gradient(90deg, rgba(0, 255, 136, 0.1) 1px, transparent 1px);
  background-size: 20px 20px;
  pointer-events: none;
  z-index: 0;
}

.dashboard-header {
  position: relative;
  z-index: 1;
  padding: 20px;
  border-bottom: 2px solid #00ff88;
  background: rgba(0, 0, 0, 0.8);
  backdrop-filter: blur(10px);
}

.title {
  font-size: 2.5rem;
  font-weight: 900;
  text-align: center;
  margin: 0;
  text-shadow: 0 0 20px #00ff88;
  letter-spacing: 3px;
}

.status-bar {
  display: flex;
  justify-content: space-between;
  margin-top: 10px;
  font-size: 0.9rem;
  opacity: 0.8;
}

.status-item {
  padding: 5px 10px;
  border: 1px solid #00ff88;
  background: rgba(0, 255, 136, 0.1);
}

.dashboard-content {
  position: relative;
  z-index: 1;
  padding: 20px;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.stat-card {
  background: rgba(0, 0, 0, 0.7);
  border: 2px solid;
  padding: 20px;
  text-align: center;
  position: relative;
  backdrop-filter: blur(10px);
}

.stat-card.victories {
  border-color: #00ff88;
  box-shadow: 0 0 20px rgba(0, 255, 136, 0.3);
}

.stat-card.defeats {
  border-color: #ff4444;
  box-shadow: 0 0 20px rgba(255, 68, 68, 0.3);
}

.stat-card.total {
  border-color: #4488ff;
  box-shadow: 0 0 20px rgba(68, 136, 255, 0.3);
}

.stat-header {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  margin-bottom: 15px;
}

.stat-icon {
  font-size: 1.5rem;
}

.stat-label {
  font-size: 0.9rem;
  font-weight: 700;
}

.stat-value {
  font-size: 3rem;
  font-weight: 900;
  margin-bottom: 10px;
}

.stat-percentage {
  font-size: 1.2rem;
  opacity: 0.8;
}

.main-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 30px;
}

@media (max-width: 768px) {
  .main-grid {
    grid-template-columns: 1fr;
  }
}

.chart-section, .history-section {
  background: rgba(0, 0, 0, 0.7);
  border: 2px solid #00ff88;
  padding: 20px;
  backdrop-filter: blur(10px);
}

.section-header {
  margin-bottom: 20px;
  padding-bottom: 10px;
  border-bottom: 1px solid #00ff88;
}

.section-header h2 {
  margin: 0;
  font-size: 1.2rem;
  font-weight: 700;
  text-align: center;
}

.chart-container {
  display: flex;
  justify-content: center;
  align-items: center;
}

.battles-list {
  max-height: 300px;
  overflow-y: auto;
}

.battle-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px;
  margin-bottom: 10px;
  border-left: 4px solid;
  background: rgba(0, 0, 0, 0.5);
}

.battle-item.victoria {
  border-left-color: #00ff88;
  background: rgba(0, 255, 136, 0.1);
}

.battle-item.derrota {
  border-left-color: #ff4444;
  background: rgba(255, 68, 68, 0.1);
}

.battle-status {
  display: flex;
  align-items: center;
  gap: 10px;
}

.status-dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  display: inline-block;
}

.status-dot.victoria {
  background: #00ff88;
  box-shadow: 0 0 10px #00ff88;
}

.status-dot.derrota {
  background: #ff4444;
  box-shadow: 0 0 10px #ff4444;
}

.battle-result {
  font-weight: 700;
  font-size: 0.9rem;
}

.battle-details {
  text-align: right;
  font-size: 0.8rem;
  opacity: 0.8;
}

.battle-date {
  margin-bottom: 2px;
}

/* Scrollbar personalizada */
.battles-list::-webkit-scrollbar {
  width: 8px;
}

.battles-list::-webkit-scrollbar-track {
  background: rgba(0, 0, 0, 0.3);
}

.battles-list::-webkit-scrollbar-thumb {
  background: #00ff88;
  border-radius: 4px;
}

.battles-list::-webkit-scrollbar-thumb:hover {
  background: #00cc66;
}

/* Animaciones */
.stat-card {
  animation: slideIn 0.5s ease-out;
}

.battle-item {
  animation: fadeIn 0.3s ease-out;
}

@keyframes slideIn {
  from {
    transform: translateY(20px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}
</style>