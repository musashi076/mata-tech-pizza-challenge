import { defineStore, acceptHMRUpdate } from 'pinia'

import { api } from 'boot/axios'
import { useAuthStore } from './auth-store'

export const useSalesStore = defineStore('sales', {
  state: () => ({
    dailySales: [],
    monthlySales: [],
    totalDailySales: 0,
    isLoading: false,
    error: null,
  }),

  actions: {
    async fetchDailyOrdersWithDetails(date) {
      this.isLoading = true
      this.error = null

      const authStore = useAuthStore()

      if (!authStore.token) {
        this.error = 'Authentication token not available. Please log in.'
        this.isLoading = false
        console.error(this.error)
        return
      }

      try {
        const url = `/sales/daily?date=${date}`
        const response = await api.get(url, {
          headers: {
            Authorization: `Bearer ${authStore.token}`,
          },
        })

        this.dailySales = response.data.orders
        this.totalDailySales = response.data.total_sales

        console.log(`Daily sales for ${date} fetched successfully.`, response.data)
      } catch (error) {
        this.error =
          'Failed to fetch daily sales: ' + (error.response?.data?.message || error.message)
        console.error(this.error, error.response || error)
        if (error.response?.status === 401) {
          authStore.logout()
        }
        this.dailySales = []
        this.totalDailySales = 0
      } finally {
        this.isLoading = false
      }
    },

    async fetchMonthlySales() {
      this.isLoading = true
      this.error = null

      const authStore = useAuthStore()

      try {
        const response = await api.get('/sales/monthly', {
          headers: {
            Authorization: `Bearer ${authStore.token}`,
          },
        })
        this.monthlySales = response.data
      } catch (err) {
        this.error =
          'Failed to fetch monthly sales data. ' + (err.response?.data?.message || err.message)
      } finally {
        this.isLoading = false
      }
    },

    clearDailySales() {
      this.dailySales = []
      this.monthlySales = []
      this.totalDailySales = 0
      this.isLoading = false
      this.error = null
    },
  },
})

if (import.meta.hot) {
  import.meta.hot.accept(acceptHMRUpdate(useSalesStore, import.meta.hot))
}
