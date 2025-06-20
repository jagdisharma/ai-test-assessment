<template>
  <div class="dashboard">
    <h1 class="dashboard__title">Smart Ticket Dashboard</h1>

    <div class="dashboard__cards">
      <div class="dashboard__card" v-for="(value, key) in stats.statusCounts" :key="key">
        <h3 class="dashboard__card-title">{{ key.replace('_', ' ').toUpperCase() }}</h3>
        <p class="dashboard__card-value">{{ value }}</p>
      </div>

      <div class="dashboard__card" v-for="(value, key) in stats.categoryCounts" :key="key">
        <h3 class="dashboard__card-title">{{ key ? key.toUpperCase() : 'UNCLASSIFIED' }}</h3>
        <p class="dashboard__card-value">{{ value }}</p>
      </div>
    </div>

    <div class="dashboard__chart">
      <canvas ref="chartCanvas"></canvas>
    </div>
  </div>
</template>

<script>
import { onMounted, ref } from 'vue'
import Chart from 'chart.js/auto'
import axios from 'axios'

export default {
  name: 'Dashboard',
  setup() {
    const stats = ref({
      statusCounts: {},
      categoryCounts: {},
    })

    const chartCanvas = ref(null)
    let chartInstance = null

    const fetchStats = async () => {
      const response = await axios.get('/api/stats')
      console.log('Fetched stats:', response.data)
      stats.value.statusCounts = response.data.status_counts
      stats.value.categoryCounts = response.data.category_counts
      renderChart()
    }

    const renderChart = () => {
      if (chartInstance) chartInstance.destroy()

      chartInstance = new Chart(chartCanvas.value, {
        type: 'pie',
        data: {
          labels: Object.keys(stats.value.categoryCounts),
          datasets: [{
            label: 'Tickets by Category',
            data: Object.values(stats.value.categoryCounts),
            backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#58D68D', '#AF7AC5', '#F5B041']
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: { position: 'right' }
          }
        }
      })
    }

    onMounted(fetchStats)

    return { stats, chartCanvas }
  }
}
</script>

<style scoped>
.dashboard {
  padding: 2rem;
  background-color: #f9f9f9;
  min-height: 100vh;
}

.dashboard__title {
  font-size: 2rem;
  font-weight: bold;
  margin-bottom: 2rem;
  color: #333;
}

.dashboard__cards {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.dashboard__card {
  background: #fff;
  padding: 1.25rem;
  border-radius: 1rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  text-align: center;
  transition: 0.3s ease;
}

.dashboard__card:hover {
  transform: scale(1.03);
}

.dashboard__card-title {
  font-size: 0.9rem;
  font-weight: 600;
  color: #666;
  margin-bottom: 0.5rem;
}

.dashboard__card-value {
  font-size: 1.5rem;
  font-weight: bold;
  color: #222;
}

.dashboard__chart {
  background: #fff;
  padding: 2rem;
  border-radius: 1rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}
</style>
