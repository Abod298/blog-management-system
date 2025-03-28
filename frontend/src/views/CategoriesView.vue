<template>
    <div class="container py-2">
        <div class="flex flex-col gap-8">
            <div class="flex justify-between">
                <div class="flex flex-col gap-2">
                    <h1 class="text-3xl font-bold tracking-tight">Kategoriler</h1>
                    <p class="text-muted-foreground">
                        Mevcut tüm içerik kategorilerini keşfedin
                    </p>
                </div>

                <div v-if="permissionsStore.hasPermission('create-categories')" 
                    class="flex justify-center items-center ">
                    <router-link to="/categories/create"
                        class="px-6 py-1 bg-blue-600 text-white  rounded-3xl hover:bg-blue-700 transition-colors font-medium">
                        Kategori Ekle
                    </router-link>
                </div>
            </div>

            <div v-if="loading" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <CategoryCardSkeleton v-for="n in 6" :key="n" />
            </div>

            <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <CategoryCard v-for="category in categories" :key="category.id" :category="category"
                     />
            </div>
        </div>
    </div>
</template>


<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import CategoryCard from '@/components/CategoryCard.vue'
import CategoryCardSkeleton from '@/components/CategoryCardSkeleton.vue'
import { useRouter } from 'vue-router'
import { ref, onMounted } from 'vue'
import type { PropType } from 'vue'
import axios from 'axios'
import { usePermissionsStore } from '@/stores/permissionsStore'
const router = useRouter()

const categories = ref();

const loading = ref(false)
const permissionsStore = usePermissionsStore()

onMounted(async () => {
    loading.value = true
    try {
        const response = await axios.get('/api/categories')
        categories.value = response.data
    } finally {
        loading.value = false
    }
})

const navigateToCategory = (id: number) => {
    router.push(`/categories/${id}`)
}

</script>