import { defineStore, acceptHMRUpdate } from 'pinia'
import { api } from 'boot/axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    isAuthenticated: false,
    token: null,
    user: null,
  }),

  getters: {
    getUser: (state) => (state.user ? state.user : ''),
  },

  actions: {
    async login(email, password) {
      try {
        const response = await api.post('/login', {
          email: email,
          password: password,
        })

        if (response.data && response.data.access_token) {
          this.token = response.data.access_token
          this.user = response.data.user
          this.isAuthenticated = true

          localStorage.setItem('authToken', this.token)
          localStorage.setItem('authUser', JSON.stringify(this.user))

          console.log('Login successful:', this.user.email)
          return true
        }
        return false
      } catch (error) {
        console.error('Login failed:', error.response?.data || error.message)
        this.logout()
        return false
      }
    },

    async logout() {
      try {
        if (this.token) {
          await api.post('/logout', null, {
            headers: {
              Authorization: `Bearer ${this.token}`,
            },
          })
        }
      } catch (error) {
        console.error('Logout failed:', error.response?.data || error.message)
      } finally {
        this.isAuthenticated = false
        this.token = null
        this.user = null

        localStorage.removeItem('authToken')
        localStorage.removeItem('authUser')

        console.log('User logged out.')
      }
    },

    initializeAuth() {
      const storedToken = localStorage.getItem('authToken')
      const storedUser = localStorage.getItem('authUser')

      if (storedToken && storedUser) {
        try {
          this.token = storedToken
          this.user = JSON.parse(storedUser)
          this.isAuthenticated = true
        } catch (e) {
          console.error('Init Failed:', e)
          this.logout()
        }
      }
    },

    async checkTokenValidity() {
      if (!this.token) {
        this.isAuthenticated = false
        return false
      }
      try {
        await api.get('user', {
          headers: {
            Authorization: `Bearer ${this.token}`,
          },
        })
        this.isAuthenticated = true
        return true
      } catch (error) {
        if (error.response?.status === 401) {
          this.logout()
        }
        return false
      }
    },
  },
})

if (import.meta.hot) {
  import.meta.hot.accept(acceptHMRUpdate(useAuthStore, import.meta.hot))
}
