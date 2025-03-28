import { defineStore } from 'pinia'
import axios from 'axios'

export const usePermissionsStore = defineStore('permissions', {
  state: () => ({
    list: []
  }),
  actions: {
    async fetchPermissions() {
      const { data } = await axios.get('/api/user/permissions')
      this.list = data
    }
  },
  getters: {
    hasPermission: (state) => (permission) => state.list.includes(permission)
  }
})
