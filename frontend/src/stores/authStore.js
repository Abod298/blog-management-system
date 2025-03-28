import { defineStore } from 'pinia'
import axios from 'axios'
import router from '@/router'
import { usePermissionsStore } from './permissionsStore'

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        isAuthenticated: false,
        isLoading: false
    }),
    actions: {
        async login(credentials) {
            this.isLoading = true
            try {
                const response = await axios.post(
                    '/login',
                    credentials,
                )
                await usePermissionsStore().fetchPermissions()
                await this.fetchUser()
                return router.push('/')
            } catch (error) {
                console.error('Login failed:', error.response?.data || error)
                throw error
            } finally {
                this.isLoading = false
            }
        },
        async register(credentials) {
            this.isLoading = true
            try {
                const response = await axios.post(
                    '/register',
                    credentials,
                )
                await this.fetchUser()
                return router.push('/')
            } catch (error) {
                console.error('Register failed:', error.response?.data || error)
                throw error
            } finally {
                this.isLoading = false
            }
        },

        async fetchUser() {
            try {
                const { data } = await axios.get('/api/user')
                this.user = data
                this.isAuthenticated = true
                return data
            } catch (error) {
                console.warn("Failed to fetch user:", error.message);
            }
        },

        async logout() {
            try {
              await axios.post('/logout')
              this.$reset()
            } catch (error) {
              console.error('Logout failed:', error.response?.data || error)
              throw error
            }
          }
    }
})

