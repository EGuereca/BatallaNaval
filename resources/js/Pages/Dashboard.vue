<template>
    <div class="dashboard-container">
      <!-- Header -->
      <div class="dashboard-header">
        <h1 class="title">COMANDO NAVAL - DASHBOARD</h1>
        <div class="header-controls">
          <div class="status-bar">
            <span class="status-item">STATUS: OPERATIVO</span>
            <span class="status-item">{{ currentTime }}</span>
          </div>
          <div class="user-controls">
            <Link :href="route('profile.edit')" class="control-btn profile-btn">
              <span class="btn-icon">👤</span>
              PERFIL
            </Link>
            <form @submit.prevent="logout" class="inline">
              <button type="submit" class="control-btn logout-btn">
                <span class="btn-icon">🚪</span>
                CERRAR SESIÓN
              </button>
            </form>
          </div>
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
  
        <!-- Games History -->
        <div class="games-history">
          <div class="section-header">
            <h2>HISTORIAL DE BATALLAS</h2>
          </div>
          
          <div class="games-table">
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>JUGADOR 1</th>
                  <th>JUGADOR 2</th>
                  <th>ESTADO</th>
                  <th>GANADOR</th>
                  <th>FECHA</th>
                  <th>ACCIONES</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="game in games" :key="game.id">
                  <td>{{ game.id }}</td>
                  <td>{{ game.player1_name }}</td>
                  <td>{{ game.player2_name || 'Esperando...' }}</td>
                  <td>
                    <span :class="['status-badge', game.status]">
                      {{ getStatusText(game.status) }}
                    </span>
                  </td>
                  <td>{{ game.winner_name || '-' }}</td>
                  <td>{{ formatDate(game.created_at) }}</td>
                  <td>
                    <button 
                      v-if="game.status === 'finished'"
                      @click="showGameBoards(game.id)"
                      class="action-btn"
                    >
                      Ver Tableros
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
  
        <!-- Game Boards Modal -->
        <div v-if="showModal" class="modal">
          <div class="modal-content">
            <div class="modal-header">
              <h3>Tableros de la Partida #{{ selectedGameId }}</h3>
              <button @click="closeModal" class="close-btn">&times;</button>
            </div>
            <div class="modal-body">
              <div class="boards-container">
                <div class="board-section">
                  <h4>Tablero Jugador 1</h4>
                  <div class="board-grid">
                    <div v-for="(row, i) in player1Board" :key="i" class="board-row">
                      <div 
                        v-for="(cell, j) in row" 
                        :key="j"
                        :class="['board-cell', getCellClass(cell)]"
                      ></div>
                    </div>
                  </div>
                </div>
                <div class="board-section">
                  <h4>Tablero Jugador 2</h4>
                  <div class="board-grid">
                    <div v-for="(row, i) in player2Board" :key="i" class="board-row">
                      <div 
                        v-for="(cell, j) in row" 
                        :key="j"
                        :class="['board-cell', getCellClass(cell)]"
                      ></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  <!-- Main Content -->
  <script>
  import { Link } from '@inertiajs/vue3';
  import axios from 'axios';
  
  export default {
    name: 'BattleshipDashboard',
    components: {
      Link
    },
    data() {
      return {
        currentTime: '',
        gameStats: {
          wins: 0,
          losses: 0
        },
        games: [],
        showModal: false,
        selectedGameId: null,
        player1Board: [],
        player2Board: [],
        timeInterval: null
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
    methods: {
      updateTime() {
        const now = new Date();
        this.currentTime = now.toLocaleTimeString();
      },
      formatDate(date) {
        return new Date(date).toLocaleString();
      },
      getStatusText(status) {
        const statusMap = {
          'waiting': 'ESPERANDO',
          'playing': 'EN JUEGO',
          'finished': 'FINALIZADO'
        };
        return statusMap[status] || status;
      },
      getCellClass(cell) {
        switch(cell) {
          case 1: return 'ship';
          case 2: return 'hit';
          case 3: return 'miss';
          default: return 'empty';
        }
      },
      async fetchGames() {
        try {
          const response = await axios.get('/api/games');
          this.games = response.data;
          this.calculateStats();
        } catch (error) {
          console.error('Error fetching games:', error);
        }
      },
      calculateStats() {
        const userId = this.$page.props.auth.user.id;
        this.gameStats.wins = this.games.filter(game => 
          game.status === 'finished' && game.winner_id === userId
        ).length;
        this.gameStats.losses = this.games.filter(game => 
          game.status === 'finished' && game.winner_id !== userId && game.winner_id !== null
        ).length;
      },
      async showGameBoards(gameId) {
        try {
          const response = await axios.get(`/api/games/${gameId}`);
          const game = response.data;
          
          // Obtener los tableros de ambos jugadores
          const board1Response = await axios.get(`/api/games/${gameId}/board/1`);
          const board2Response = await axios.get(`/api/games/${gameId}/board/2`);
          
          this.player1Board = JSON.parse(board1Response.data.grid);
          this.player2Board = JSON.parse(board2Response.data.grid);
          this.selectedGameId = gameId;
          this.showModal = true;
        } catch (error) {
          console.error('Error fetching game boards:', error);
        }
      },
      closeModal() {
        this.showModal = false;
        this.selectedGameId = null;
        this.player1Board = [];
        this.player2Board = [];
      },
      logout() {
        this.$inertia.post(route('logout'));
      }
    },
    mounted() {
      this.updateTime();
      this.timeInterval = setInterval(this.updateTime, 1000);
      this.fetchGames();
    },
    beforeUnmount() {
      if (this.timeInterval) {
        clearInterval(this.timeInterval);
      }
    }
  }
  </script>
  
  <style scoped>
  .dashboard-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 50%, #0a0a0a 100%);
    color: #00ff88;
    font-family: 'Courier New', monospace;
    padding: 20px;
  }
  
  .dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding: 20px;
    background: rgba(0, 255, 65, 0.1);
    border: 2px solid #00ff41;
    border-radius: 10px;
  }
  
  .title {
    font-size: 2rem;
    margin: 0;
    text-shadow: 0 0 10px #00ff41;
  }
  
  .header-controls {
    display: flex;
    flex-direction: column;
    gap: 15px;
    align-items: flex-end;
  }
  
  .status-bar {
    display: flex;
    gap: 20px;
  }
  
  .status-item {
    font-size: 1.1rem;
  }
  
  .user-controls {
    display: flex;
    gap: 10px;
  }
  
  .control-btn {
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 8px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-family: 'Courier New', monospace;
    font-weight: bold;
    transition: all 0.3s ease;
    text-decoration: none;
  }
  
  .profile-btn {
    background: #00ff41;
    color: #000;
  }
  
  .logout-btn {
    background: #ff4444;
    color: #fff;
  }
  
  .control-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 255, 65, 0.4);
  }
  
  .btn-icon {
    font-size: 1.2rem;
  }
  
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
  }
  
  .stat-card {
    background: rgba(0, 0, 0, 0.8);
    border: 1px solid #00ff41;
    border-radius: 10px;
    padding: 20px;
    text-align: center;
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
  
  .stat-value {
    font-size: 2.5rem;
    font-weight: bold;
    margin: 10px 0;
  }
  
  .stat-percentage {
    font-size: 1.2rem;
    color: #00ff41;
  }
  
  .games-history {
    background: rgba(0, 0, 0, 0.8);
    border: 1px solid #00ff41;
    border-radius: 10px;
    padding: 20px;
  }
  
  .section-header {
    margin-bottom: 20px;
  }
  
  .section-header h2 {
    margin: 0;
    font-size: 1.5rem;
    color: #00ff41;
  }
  
  .games-table {
    width: 100%;
    overflow-x: auto;
  }
  
  table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
  }
  
  th, td {
    padding: 12px;
    border-bottom: 1px solid #333;
  }
  
  th {
    background: rgba(0, 255, 65, 0.1);
    font-weight: bold;
  }
  
  .status-badge {
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 0.9rem;
  }
  
  .status-badge.waiting {
    background: #ffd700;
    color: #000;
  }
  
  .status-badge.playing {
    background: #00ff41;
    color: #000;
  }
  
  .status-badge.finished {
    background: #ff4444;
    color: #fff;
  }
  
  .action-btn {
    background: #00ff41;
    color: #000;
    border: none;
    padding: 5px 10px;
    border-radius: 5px;
    cursor: pointer;
    font-family: 'Courier New', monospace;
    font-weight: bold;
  }
  
  .action-btn:hover {
    background: #00cc33;
  }
  
  /* Modal Styles */
  .modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
  }
  
  .modal-content {
    background: #1a1a2e;
    border: 2px solid #00ff41;
    border-radius: 10px;
    width: 90%;
    max-width: 1200px;
    max-height: 90vh;
    overflow-y: auto;
  }
  
  .modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #00ff41;
  }
  
  .modal-header h3 {
    margin: 0;
    color: #00ff41;
  }
  
  .close-btn {
    background: none;
    border: none;
    color: #00ff41;
    font-size: 1.5rem;
    cursor: pointer;
  }
  
  .modal-body {
    padding: 20px;
  }
  
  .boards-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
  }
  
  .board-section {
    text-align: center;
  }
  
  .board-section h4 {
    margin-bottom: 15px;
    color: #00ff41;
  }
  
  .board-grid {
    display: inline-grid;
    grid-template-columns: repeat(8, 1fr);
    gap: 2px;
    background: #333;
    padding: 2px;
    border: 1px solid #00ff41;
  }
  
  .board-row {
    display: contents;
  }
  
  .board-cell {
    width: 30px;
    height: 30px;
    background: #1a1a2e;
    border: 1px solid #333;
  }
  
  .board-cell.ship {
    background: #00ff41;
  }
  
  .board-cell.hit {
    background: #ff4444;
  }
  
  .board-cell.miss {
    background: #4444ff;
  }
  
  @media (max-width: 768px) {
    .dashboard-header {
      flex-direction: column;
      gap: 15px;
      text-align: center;
    }
    
    .stats-grid {
      grid-template-columns: 1fr;
    }
    
    .board-cell {
      width: 25px;
      height: 25px;
    }
    
    .header-controls {
      align-items: center;
    }
    
    .user-controls {
      flex-direction: column;
      width: 100%;
    }
    
    .control-btn {
      width: 100%;
      justify-content: center;
    }
  }
  </style>