import './style.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import VueTheMask from 'vue-the-mask'

import './plugins/echo';
import App from './App.vue'
import router from './router'
import { useAuthStore } from '@/stores/authStore'
import { usePermissionsStore } from '@/stores/permissionsStore'
import '@/plugins/axios'

const app = createApp(App)
app.use(createPinia())
const authStore = useAuthStore()
const permissionsStore = usePermissionsStore()
await authStore.fetchUser().catch(() => {})
if (authStore.isAuthenticated) {
  await permissionsStore.fetchPermissions()
}
app.use(VueTheMask)
app.use(router)
app.mount('#app')
