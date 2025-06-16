<template>
  <div class="battleship-container">
    <!-- Header Controls -->
    <div class="control-panel">
      <h1 class="game-title">BATTLESHIP COMMAND</h1>
      <button @click="toggleActiveBoard" class="toggle-btn">
        SWITCH TO {{ activeBoard === 'player' ? 'ENEMY' : 'PLAYER' }} GRID
      </button>
    </div>
    
    <!-- Game Boards -->
    <div class="boards-container">
      <!-- Player Board -->
      <div class="board-section">
        <div class="board-header" :class="{ active: activeBoard === 'player' }">
          <h2>FRIENDLY GRID</h2>
          <div class="status-indicator" :class="{ active: activeBoard === 'player' }"></div>
        </div>
        
        <div class="board-wrapper" :class="{ active: activeBoard === 'player' }">
          <div class="coordinates top">
            <div class="coord-cell"></div>
            <div v-for="i in 8" :key="i" class="coord-cell">{{ getColumnLabel(i-1) }}</div>
          </div>
          
          <div class="board-content">
            <div class="coordinates left">
              <div v-for="i in 8" :key="i" class="coord-cell">{{ i }}</div>
            </div>
            
            <div class="grid">
              <div v-for="(row, rowIndex) in playerBoard" :key="rowIndex" class="row">
                <div 
                  v-for="(cell, colIndex) in row" 
                  :key="colIndex"
                  :class="getCellClass('player', rowIndex, colIndex, cell)"
                  @click="handleCellClick('player', rowIndex, colIndex)"
                >
                  <div class="cell-inner">
                    <div v-if="cell === 1" class="hit-marker">⚡</div>
                    <div v-else-if="cell === 2" class="miss-marker">○</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Enemy Board -->
      <div class="board-section">
        <div class="board-header" :class="{ active: activeBoard === 'enemy' }">
          <h2>ENEMY GRID</h2>
          <div class="status-indicator" :class="{ active: activeBoard === 'enemy' }"></div>
        </div>
        
        <div class="board-wrapper" :class="{ active: activeBoard === 'enemy' }">
          <div class="coordinates top">
            <div class="coord-cell"></div>
            <div v-for="i in 8" :key="i" class="coord-cell">{{ getColumnLabel(i-1) }}</div>
          </div>
          
          <div class="board-content">
            <div class="coordinates left">
              <div v-for="i in 8" :key="i" class="coord-cell">{{ i }}</div>
            </div>
            
            <div class="grid">
              <div v-for="(row, rowIndex) in enemyBoard" :key="rowIndex" class="row">
                <div 
                  v-for="(cell, colIndex) in row" 
                  :key="colIndex"
                  :class="getCellClass('enemy', rowIndex, colIndex, cell)"
                  @click="handleCellClick('enemy', rowIndex, colIndex)"
                >
                  <div class="cell-inner">
                    <div v-if="cell === 1" class="hit-marker">💥</div>
                    <div v-else-if="cell === 2" class="miss-marker">○</div>
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

<script setup>
import { ref } from 'vue'

const activeBoard = ref('player') // 'player' o 'enemy'
const playerBoard = ref(Array(8).fill().map(() => Array(8).fill(0)))
const enemyBoard = ref(Array(8).fill().map(() => Array(8).fill(0)))

// Simular algunos disparos para mostrar los estados
playerBoard.value[2][3] = 1 // Hit
playerBoard.value[4][5] = 2 // Miss
enemyBoard.value[1][2] = 1 // Hit
enemyBoard.value[6][7] = 2 // Miss

const toggleActiveBoard = () => {
  activeBoard.value = activeBoard.value === 'player' ? 'enemy' : 'player'
}

const handleCellClick = (board, row, col) => {
  if (board === 'player' && activeBoard.value === 'player') {
    // Solo permitir clicks en el tablero activo
    console.log(`Player board clicked: ${row}, ${col}`)
  } else if (board === 'enemy' && activeBoard.value === 'enemy') {
    console.log(`Enemy board clicked: ${row}, ${col}`)
  }
}

const getCellClass = (board, row, col, value) => {
  const isActive = activeBoard.value === board
  let classes = ['cell']
  
  if (value === 1) classes.push('hit')
  else if (value === 2) classes.push('miss')
  else classes.push('empty')
  
  if (isActive) classes.push('active-board')
  else classes.push('inactive-board')
  
  return classes.join(' ')
}

const getColumnLabel = (index) => String.fromCharCode(65 + index) // A-H
</script>

<style scoped>
.battleship-container {
  background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 50%, #0f0f0f 100%);
  min-height: 100vh;
  padding: 20px;
  font-family: 'Courier New', monospace;
  color: #00ff41;
}

.control-panel {
  text-align: center;
  margin-bottom: 30px;
  padding: 20px;
  background: rgba(0, 255, 65, 0.1);
  border: 2px solid #00ff41;
  border-radius: 10px;
  backdrop-filter: blur(10px);
}

.game-title {
  font-size: 2.5rem;
  font-weight: bold;
  margin: 0 0 20px 0;
  text-shadow: 0 0 20px #00ff41;
  letter-spacing: 3px;
}

.toggle-btn {
  background: linear-gradient(45deg, #00ff41, #00cc33);
  border: none;
  padding: 15px 30px;
  font-size: 1.1rem;
  font-weight: bold;
  color: #000;
  border-radius: 5px;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(0, 255, 65, 0.4);
}

.toggle-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(0, 255, 65, 0.6);
}

.boards-container {
  display: flex;
  gap: 40px;
  justify-content: center;
  flex-wrap: wrap;
}

.board-section {
  flex: 1;
  min-width: 400px;
  max-width: 500px;
}

.board-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 20px;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid #333;
  border-radius: 8px 8px 0 0;
  transition: all 0.3s ease;
}

.board-header.active {
  background: rgba(0, 255, 65, 0.2);
  border-color: #00ff41;
  box-shadow: 0 0 20px rgba(0, 255, 65, 0.3);
}

.board-header h2 {
  margin: 0;
  font-size: 1.3rem;
  letter-spacing: 2px;
}

.status-indicator {
  width: 15px;
  height: 15px;
  border-radius: 50%;
  background: #666;
  transition: all 0.3s ease;
}

.status-indicator.active {
  background: #00ff41;
  box-shadow: 0 0 15px #00ff41;
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0% { opacity: 1; }
  50% { opacity: 0.5; }
  100% { opacity: 1; }
}

.board-wrapper {
  border: 2px solid #333;
  border-top: none;
  border-radius: 0 0 8px 8px;
  background: rgba(0, 0, 0, 0.7);
  padding: 20px;
  transition: all 0.3s ease;
}

.board-wrapper.active {
  border-color: #00ff41;
  background: rgba(0, 255, 65, 0.05);
  box-shadow: 0 0 30px rgba(0, 255, 65, 0.2);
}

.coordinates {
  display: flex;
}

.coordinates.top {
  margin-bottom: 5px;
}

.coordinates.left {
  flex-direction: column;
  margin-right: 5px;
}

.board-content {
  display: flex;
}

.coord-cell {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  font-size: 1rem;
  color: #00ff41;
  text-shadow: 0 0 5px #00ff41;
}

.grid {
  display: flex;
  flex-direction: column;
  border: 2px solid #00ff41;
  border-radius: 4px;
  overflow: hidden;
}

.row {
  display: flex;
}

.cell {
  width: 40px;
  height: 40px;
  border: 1px solid #333;
  cursor: pointer;
  transition: all 0.2s ease;
  position: relative;
  overflow: hidden;
}

.cell.active-board {
  border-color: #00ff41;
  background: rgba(0, 255, 65, 0.1);
}

.cell.active-board:hover {
  background: rgba(0, 255, 65, 0.2);
  box-shadow: inset 0 0 10px rgba(0, 255, 65, 0.3);
}

.cell.inactive-board {
  border-color: #555;
  background: rgba(100, 100, 100, 0.1);
  cursor: not-allowed;
  opacity: 0.6;
}

.cell.empty.active-board {
  background: linear-gradient(45deg, 
    rgba(0, 255, 65, 0.1) 0%, 
    rgba(0, 200, 50, 0.05) 100%);
}

.cell.hit {
  background: radial-gradient(circle, #ff4444, #cc0000);
  border-color: #ff4444;
  animation: hit-flash 0.5s ease-out;
}

.cell.miss {
  background: radial-gradient(circle, #4444ff, #0000cc);
  border-color: #4444ff;
}

@keyframes hit-flash {
  0% { background: #ffffff; }
  100% { background: radial-gradient(circle, #ff4444, #cc0000); }
}

.cell-inner {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}

.hit-marker {
  font-size: 1.5rem;
  color: #ffff00;
  text-shadow: 0 0 10px #ffff00;
  animation: hit-glow 1s ease-in-out infinite alternate;
}

.miss-marker {
  font-size: 1.2rem;
  color: #ffffff;
  opacity: 0.8;
}

@keyframes hit-glow {
  from { text-shadow: 0 0 10px #ffff00; }
  to { text-shadow: 0 0 20px #ffff00, 0 0 30px #ffaa00; }
}

/* Responsive Design */
@media (max-width: 768px) {
  .boards-container {
    flex-direction: column;
    gap: 20px;
  }
  
  .board-section {
    min-width: auto;
    max-width: none;
  }
  
  .game-title {
    font-size: 2rem;
  }
  
  .coord-cell, .cell {
    width: 35px;
    height: 35px;
  }
  
  .coord-cell {
    font-size: 0.9rem;
  }
}
</style>