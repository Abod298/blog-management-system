import router from '@/router'
import { useAuthStore } from '@/stores/authStore'

export default function auth({ next }) {
    if (!useAuthStore().isAuthenticated) {
        router.push('login')
        return
    }
    return next()
}
