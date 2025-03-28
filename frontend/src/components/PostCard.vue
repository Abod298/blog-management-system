<template>
    <Card class="hover:shadow-lg transition-all h-full flex flex-col">
      <CardHeader class="p-0">
        <div class="relative aspect-video overflow-hidden rounded-t-lg">
          <img
            :src="post.image || 'https://placehold.co/800x450?text=Post'"
            :alt="post.title"
            class="object-cover w-full h-full"
          />
        </div>
      </CardHeader>
      <CardContent class="flex-grow p-6">
        <div class="flex items-center gap-2 mb-2">
          <Avatar class="h-8 w-8">
            <AvatarImage v-if="post.author.avatar" :src="post.author.avatar" />
            <AvatarFallback>{{ post.author.name.charAt(0) }}</AvatarFallback>
          </Avatar>
          <span class="text-sm font-medium">{{ post.author.name + ' ' + post.author.last_name }}</span>
        </div>
        <div v-if="post.categories && post.categories.length" class="flex flex-wrap gap-2 mb-3">
        <Badge 
          v-for="category in post.categories" 
          :key="category"
          variant="outline"
          class="text-xs"
        >
          {{ category.title }}
        </Badge>
      </div>
      <div class="mb-2">
        <Badge 
          v-if="!!!post.published_at"
          variant="outline"
          class="text-xs bg-slate-400"
        >
          Taslak 
        </Badge>
      </div>
        <CardTitle class="mb-2">{{ post.title }}</CardTitle>
        <div v-html="post.body" class="text-muted-foreground line-clamp-3 mb-4"></div>
        <div class="flex justify-between items-center text-sm">
          <div class="flex items-center gap-2">
            <MessageSquare class="h-4 w-4" />
            <span>{{ post.comments.length }} comments</span>
          </div>
          <span class="text-muted-foreground">
            {{ formatDate(post.created_at) }}
          </span>
        </div>
      </CardContent>
      <CardFooter class="p-6 pt-0">
        <Button v-if="post.author.id === authStore.user.id" variant="outline" class="w-full" as-child>
          <RouterLink :to="`/posts/edit/${post.slug}`">Düzenle</RouterLink>
        </Button>
        <Button variant="outline" class="w-full" as-child>
          <RouterLink :to="`/posts/show/${post.slug}`">İncele</RouterLink>
        </Button>
      </CardFooter>
    </Card>
  </template>
  
  <script setup lang="ts">
  import { Card, CardHeader, CardTitle, CardContent, CardFooter } from '@/components/ui/card'
  import { Button } from '@/components/ui/button'
  import { Avatar, AvatarImage, AvatarFallback } from '@/components/ui/avatar'
  import { MessageSquare } from 'lucide-vue-next'
  import { Badge } from '@/components/ui/badge'
  import {useAuthStore} from '@/stores/authStore'

  const authStore = useAuthStore()
  
  defineProps({
  post: {
    type: Object as PropType<{
      id: number
      title: string
      body: string
      slug: string
      image: string | null
      published_at: string
      created_at: string
      updated_at: string
      comments: Array<{
        id: number
        body: string
        user: string
        avatar: string | null
      }>
      categories: Array<string>
      author: {
        id: number
        name: string
        last_name: string
        avatar: string | null
      }
    }>,
    required: true
  }
});

  
  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    })
  }
  </script>