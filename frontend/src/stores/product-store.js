import { defineStore, acceptHMRUpdate } from 'pinia'
import { useAuthStore } from 'src/stores/auth-store'
import { api } from 'boot/axios'

export const useProductStore = defineStore('product', {
  state: () => ({
    isLoading: false,
    products: [],
    product: null,
    error: null,
    totalProducts: 0,
    lastPage: 0,
  }),

  getters: {
    getProducts: (state) => (state.products ? state.products : []),
    getProduct: (state) => (state.product ? state.product : null),
  },
  actions: {
    async fetchProducts(params = {}) {
      this.isLoading = true
      this.error = null

      //check token first
      const authStore = useAuthStore() // Get an instance of the auth store

      if (!authStore.token) {
        this.error = 'Authentication token not available. Please log in.'
        this.isLoading = false
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

        const url = `/products?${queryParams.toString()}`

        const response = await api.get(url, {
          headers: {
            Authorization: `Bearer ${authStore.token}`,
          },
        })

        this.products = response.data.data
        this.totalProducts = response.data.total
        this.lastPage = response.data.last_page
      } catch (error) {
        this.error = 'Failed to fetch products: ' + error.message
      } finally {
        this.isLoading = false
      }
    },

    clearProducts() {
      this.products = []
      this.product = null
      this.isLoading = false
      this.error = null
      this.totalProducts = 0
      this.lastPage = 0
    },
  },
})

if (import.meta.hot) {
  import.meta.hot.accept(acceptHMRUpdate(useProductStore, import.meta.hot))
}
