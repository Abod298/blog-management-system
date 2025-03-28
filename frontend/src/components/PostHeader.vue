<script setup>
import { CalendarDays, Clock, User, Tags } from 'lucide-vue-next'
import { Avatar, AvatarImage, AvatarFallback } from '@/components/ui/avatar'
import { Badge } from '@/components/ui/badge'

const props = defineProps({
  post: {
    type: Object,
    required: true
  }
})

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('tr-TR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}
</script>

<template>
  <article class="space-y-6">

    <div class="rounded-xl overflow-hidden border shadow-sm">
      <img 
        v-if="post.image"
        :src="post.image" 
        :alt="post.title" 
        class="w-full h-[400px] object-cover"
      />
    </div>
    

    <h1 class="text-3xl font-bold tracking-tight scroll-m-20">
      {{ post.title }}
    </h1>
    

    <div class="flex flex-wrap items-center gap-4 text-sm">
      <div class="flex items-center gap-2">
        <Avatar class="h-6 w-6">
          <AvatarImage v-if="post.author.avatar" :src="post.author.avatar" />
          <AvatarFallback>
            {{ post.author.name.charAt(0) }}{{ post.author.last_name.charAt(0) }}
          </AvatarFallback>
        </Avatar>
        <span>{{ post.author.name }} {{ post.author.last_name }}</span>
      </div>
      
      <div class="flex items-center gap-2 text-muted-foreground">
        <CalendarDays class="h-4 w-4" />
        <span>{{ formatDate(post.created_at) }}</span>
      </div>
      
      <div 
        class="flex items-center gap-2 text-muted-foreground" 
        v-if="post.updated_at !== post.created_at"
      >
        <Clock class="h-4 w-4" />
        <span>Güncellendi: {{ formatDate(post.updated_at) }}</span>
      </div>
      
      <div class="flex items-center gap-2" v-if="post.categories.length">
        <Tags class="h-4 w-4 text-muted-foreground" />
        <div class="flex gap-2">
          <Badge 
            variant="secondary"
            v-for="category in post.categories" 
            :key="category"
          >
            {{ category.title }}
          </Badge>
        </div>
      </div>
    </div>
    
    <div 
      class="prose prose-lg max-w-none dark:prose-invert prose-headings:font-semibold"
      v-html="post.body"
    ></div>
  </article>
</template>