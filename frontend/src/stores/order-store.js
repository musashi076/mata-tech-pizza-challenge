import { defineStore, acceptHMRUpdate } from 'pinia'

import { api } from 'boot/axios'
import { useAuthStore } from './auth-store'

export const useOrderStore = defineStore('order', {
  state: () => ({
    orders: [],
    order: null,
    isLoading: false,
    error: null,
    totalOrders: 0,
    lastPage: 0,
  }),

  actions: {
    async fetchOrders(params = {}) {
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
        const queryParams = new URLSearchParams()
        if (params.page) queryParams.append('page', params.page)
        if (params.per_page) queryParams.append('per_page', params.per_page)
        if (params.sort_by) queryParams.append('sort_by', params.sort_by)
        if (params.descending !== undefined)
          queryParams.append('descending', params.descending ? 'true' : 'false')
        if (params.filter) queryParams.append('filter', params.filter)

        const url = `/orders?${queryParams.toString()}`

        const response = await api.get(url, {
          headers: {
            Authorization: `Bearer ${authStore.token}`,
          },
        })

        this.orders = response.data.data
        this.totalOrders = response.data.total
        this.lastPage = response.data.last_page

        console.log('Orders fetched successfully:', this.orders)
      } catch (error) {
        this.error = 'Failed to fetch orders: ' + (error.response?.data?.message || error.message)
      } finally {
        this.isLoading = false
      }
    },

    clearOrders() {
      this.orders = []
      this.isLoading = false
      this.error = null
      this.totalOrders = 0
      this.lastPage = 0
    },
  },
})

if (import.meta.hot) {
  import.meta.hot.accept(acceptHMRUpdate(useOrderStore, import.meta.hot))
}
