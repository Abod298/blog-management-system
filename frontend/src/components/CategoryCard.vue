<template>
  <Card class="hover:shadow-lg transition-all duration-300 cursor-pointer h-full">
    <CardHeader>
      <div class="relative aspect-video overflow-hidden rounded-lg mb-4">
        <img :src="category.image || 'https://placehold.co/600x400?text=Category'" :alt="category.title"
          class="object-cover w-full h-full" />
      </div>
      <CardTitle class="text-xl">{{ category.title }}</CardTitle>
    </CardHeader>
    <CardContent>
      <p class="text-muted-foreground line-clamp-2">{{ category.description }}</p>
    </CardContent>
    <CardFooter class="flex justify-between items-center">
      <Badge variant="secondary">
        {{ category.posts.length }} {{ category.posts.length === 1 ? 'Post' : 'Posts' }}
      </Badge>
      <Button variant="ghost" size="sm" as-child>
        <RouterLink :to="`/categories/edit/${category.slug}`">Düzenle</RouterLink>
      </Button>
      <Button
          v-if="permissionsStore.hasPermission('delete-categories')"
          variant="destructive"
          size="sm"
          class="text-red-600 hover:bg-red-400"
          @click="deleteCategory(category)"
        >
          Sil
        </Button>
    </CardFooter>
  </Card>
</template>

<script setup lang="ts">
import { Card, CardHeader, CardTitle, CardContent, CardFooter } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { usePermissionsStore } from '@/stores/permissionsStore'
import axios from 'axios'
import { RouterLink , useRouter } from 'vue-router'

const permissionsStore = usePermissionsStore()
const router = useRouter()
defineProps({
  category: {
    type: Object as PropType<{
      id: number
      title: string
      description: string
      image: string | null
      user: Object;
      posts: Array<Object>;
    }>,
    required: true
  }
})
const deleteCategory = async (category) => {
  if (confirm(`Are you sure you want to delete the category: ${category.title}?`)) {
    try {
      await axios.delete(`/api/categories/${category.id}`)
      router.push({ name: 'categories' })
    } catch (error) {
      console.error('Error deleting category:', error)
      alert('There was an error deleting the category.')
    }
  }
}
</script>