<template>
  <q-page padding>
    <div class="text-h4 q-mb-md">Dashboard Overview</div>

    <div v-if="salesStore.isLoading" class="q-pa-md text-center">
      <q-spinner-dots color="primary" size="3em" />
      <div class="q-mt-md">Loading sales data...</div>
    </div>

    <div v-else-if="salesStore.error" class="q-pa-md text-negative text-center">
      <q-icon name="error" size="2em" class="q-mb-sm" />
      <div>Error: {{ salesStore.error }}</div>
      <q-btn
        v-if="salesStore.error.includes('Authentication')"
        label="Go to Login"
        color="primary"
        class="q-mt-md"
        @click="goToLogin"
      />
    </div>

    <q-card v-else class="q-mt-md">
      <q-card-section>
        <div class="text-h6">Monthly Sales Revenue</div>
      </q-card-section>

      <q-card-section>
        <div class="chart-container">
          <Bar v-if="chartData.labels.length > 0" :data="chartData" :options="chartOptions" />
          <div v-else class="text-center q-pa-md">No monthly sales data available.</div>
        </div>
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script>
import { defineComponent, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useSalesStore } from 'src/stores/sales-store'

import { Bar } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

export default defineComponent({
  name: 'DashboardPage',
  components: {
    Bar,
  },
  setup() {
    const salesStore = useSalesStore()
    const router = useRouter()

    const chartData = computed(() => {
      const labels = salesStore.monthlySales.map((item) => item.sale_month)
      const data = salesStore.monthlySales.map((item) => item.monthly_revenue)

      return {
        labels: labels,
        datasets: [
          {
            label: 'Monthly Revenue ($)',
            backgroundColor: '#42A5F5',
            data: data,
          },
        ],
      }
    })

    const chartOptions = {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: true,
          position: 'top',
        },
        tooltip: {
          callbacks: {
            label: function (context) {
              let label = context.dataset.label || ''
              if (label) {
                label += ': '
              }
              if (context.parsed.y !== null) {
                label += new Intl.NumberFormat('en-US', {
                  style: 'currency',
                  currency: 'USD',
                }).format(context.parsed.y)
              }
              return label
            },
          },
        },
      },
      scales: {
        x: {
          title: {
            display: true,
            text: 'Month',
          },
        },
        y: {
          title: {
            display: true,
            text: 'Revenue ($)',
          },
          beginAtZero: true,
          ticks: {
            callback: function (value) {
              return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
              }).format(value)
            },
          },
        },
      },
    }

    const goToLogin = () => {
      router.push({ name: 'Login' })
    }

    onMounted(() => {
      salesStore.fetchMonthlySales()
    })

    return {
      salesStore,
      chartData,
      chartOptions,
      goToLogin,
    }
  },
})
</script>

<style lang="scss" scoped>
.chart-container {
  position: relative;
  height: 400px; /* Set a fixed height for the chart container */
  width: 100%;
}
</style>
