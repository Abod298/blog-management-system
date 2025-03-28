import axios from 'axios'
import { useAuthStore } from '@/stores/authStore'


axios.defaults.baseURL = 'http://localhost:8000'  // يمكنك تغيير العنوان الأساسي حسب الحاجة
axios.defaults.withCredentials = true
axios.defaults.headers.common['Accept'] = 'application/json'
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
const getCookie = (name) => {
  const value = document.cookie
    .split('; ')
    .find(row => row.startsWith(`${name}=`))
    ?.split('=')[1]
  return value ? decodeURIComponent(value) : null
}
axios.get('/sanctum/csrf-cookie')
axios.interceptors.request.use((config) => {
  config.headers['X-XSRF-TOKEN'] = getCookie('XSRF-TOKEN')
  return config
})
