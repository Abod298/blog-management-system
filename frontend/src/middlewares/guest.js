import router from '@/router'
import { useAuthStore } from '@/stores/authStore'
export default function guest({next,store}){
    if(useAuthStore().isAuthenticated){
        router.push('/')
        return
    }
    return next();
 }
