<template>
  <div class="dashboard">
    <h1 class="dashboard__title">Smart Ticket Dashboard</h1>

  <h2 class="dashboard__cards-heading">Tickets per status </h2>
    <div class="dashboard__cards">
    
      <div class="dashboard__card" v-for="(value, key) in stats.statusCounts" :key="'status-' + key">
        <h3 class="dashboard__card-title"><i class="pi pi-info-circle"></i> {{ key.replace('_', ' ').toUpperCase() }}</h3>
        <p class="dashboard__card-value">{{ value }}</p>
      </div>

    </div>

 <h2 class="dashboard__cards-heading" >Tickets per category </h2>
     <div class="dashboard__cards">
     
      <div class="dashboard__card" v-for="(value, key) in stats.categoryCounts" :key="'cat-' + key">
        <h3 class="dashboard__card-title"><i class="pi pi-tag"></i> {{ key ? key.toUpperCase() : 'UNCLASSIFIED' }}</h3>
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
          labels: Object.keys(stats.value.categoryCounts).map(key => key || 'Unslassified'),
          datasets: [{
            label: 'Tickets by Category',
            data: Object.values(stats.value.categoryCounts),
            backgroundColor: ['#C58940', '#E5BA73', '#FAEAB1', '#16a34a', '#FFD600', '#dc2626']
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
  padding: 2rem 0.5rem;
  background-color: transparent;
  min-height: 100vh;
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
}

.dashboard__title {
  font-size: 2rem;
  font-weight: bold;
  margin-bottom: 2rem;
  color: #C58940;
  text-align: center;
}

.dashboard__cards {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1.5rem;
  margin-bottom: 2rem;
  padding: 0 1rem;
}

.dashboard__card {
  background: linear-gradient(135deg, #FAEAB1 0%, #E5BA73 100%);
  padding: 1.25rem;
  border-radius: 1.2rem;
  box-shadow: 0 4px 16px rgba(197,137,64,0.10);
  text-align: center;
  transition: 0.3s ease;
  color: #C58940;
  font-weight: 700;
}

.dashboard__card:hover {
  transform: scale(1.03);
  box-shadow: 0 8px 32px rgba(197,137,64,0.13);
}

.dashboard__card-title {
  font-size: 1rem;
  font-weight: 700;
  color: #E5BA73;
  margin-bottom: 0.5rem;
}

.dashboard__card-value {
  font-size: 1.7rem;
  font-weight: bold;
  color: #C58940;
}

.dashboard__card-title i {
  margin-right: 0.5rem;
  font-size: 1.1rem;
  vertical-align: middle;
}

.dashboard__chart {
  background: #fff;
  padding: 2rem;
  border-radius: 1.2rem;
  box-shadow: 0 4px 16px rgba(197,137,64,0.10);
  max-height: 30rem;
  display: flex;
  justify-content: center;
}

.dashboard__cards-title {
    font-size: 1rem;
    font-weight: bold;
  }

@media (max-width: 1200px) {
  .dashboard__cards {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (max-width: 900px) {
  .dashboard__cards {
    grid-template-columns: repeat(2, 1fr);
  }
  .dashboard__title {
    font-size: 1.3rem;
  }
  .dashboard__card {
    padding: 1rem;
    border-radius: 0.7rem;
  }
  .dashboard__chart {
    padding: 1rem;
    border-radius: 0.7rem;
  }
}

@media (max-width: 600px) {
  .dashboard {
    width: 100%;
    max-width: 100%;
  }
  .dashboard__cards {
    grid-template-columns: 1fr;
    gap: 1rem;
    padding: 0;
  }
  .dashboard__chart {
    margin-top: 1rem;
  }
}
</style>
