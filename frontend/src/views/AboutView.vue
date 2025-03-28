<template>
    <div class="about">
        <h1>{{user.name}}</h1>
    </div>
</template>
<script setup>
import {useAuthStore} from '@/stores/authStore'
const authStore = useAuthStore()
const user = authStore.user
</script>
<style>
@media (min-width: 1024px) {
    .about {
        min-height: 100vh;
        display: flex;
        align-items: center;
    }
}
</style>
