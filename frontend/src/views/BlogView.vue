<template>
  <div class="container py-1">
      <div class="bg-white p-4 rounded-lg shadow-sm mb-6">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

              <div>
                  <Label for="title-search">Başlığa Göre Ara</Label>
                  <Input
                      id="title-search"
                      v-model="filters.title"
                      placeholder="Yazı başlığına göre filtrele..."
                      class="w-full rounded-xl"
                  />
              </div>

              <div>
                  <Label for="author-search">Yazara Göre Ara</Label>
                  <Input
                      id="author-search"
                      v-model="filters.author"
                      placeholder="Yazar adına göre filtrele..."
                      class="w-full rounded-xl"
                  />
              </div>

              <div>
                  <Label>Tarih Aralığı</Label>
                  <div class="flex gap-2">
                      <Popover>
                          <PopoverTrigger as-child>
                              <Button
                                  variant="outline"
                                  class="w-full justify-start text-left font-normal rounded-xl"
                              >
                                  <CalendarIcon class="mr-2 h-4 w-4" />
                                  {{ filters.startDate ? formatDate(filters.startDate) : 'Başlangıç tarihi' }}
                              </Button>
                          </PopoverTrigger>
                          <PopoverContent class="w-auto p-0">
                              <Calendar v-model="filters.startDate"  />
                          </PopoverContent>
                      </Popover>
                      <Popover>
                          <PopoverTrigger as-child>
                              <Button
                                  variant="outline"
                                  class="w-full justify-start text-left font-normal rounded-xl"
                              >
                                  <CalendarIcon class="mr-2 h-4 w-4" />
                                  {{ filters.endDate ? formatDate(filters.endDate) : 'Bitiş tarihi' }}
                              </Button>
                          </PopoverTrigger>
                          <PopoverContent class="w-auto p-0">
                              <Calendar v-model="filters.endDate" />
                          </PopoverContent>
                      </Popover>
                  </div>
              </div>
          </div>
      </div>

      <div v-if="loading" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
          <PostCardSkeleton v-for="n in 6" :key="n" />
      </div>

      <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
          <PostCard
              v-for="post in filteredPosts"
              :key="post.id"
              :post="post"
          />
      </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { Calendar as CalendarIcon } from 'lucide-vue-next'
import AppLayout from '@/layouts/AppLayout.vue'
import PostCard from '@/components/PostCard.vue'
import PostCardSkeleton from '@/components/PostCardSkeleton.vue'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Button } from '@/components/ui/button'
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover'
import { Calendar } from '@/components/ui/calendar'
import {Pagination} from '@/components/ui/pagination'
import axios from 'axios'




const loading = ref(false)
const allPosts = ref<Array<{ 
  id: number;
  title: string;
  body: string;
  slug: string;
  image: string | null;
  published_at: string;
  created_at: string;
  updated_at: string;
  author: {
    id: number;
    name: string;
    last_name: string;
    avatar: string | null;
  };
  categories: string[];
  comments: Array<{
    id: number;
    body: string;
    user: string;
    avatar: string | null;
  }>;
}>>([])


const filters = ref({
  title: '',
  author: '',
  startDate: null as Date | null,
  endDate: null as Date | null
})

const filteredPosts = computed(() => {
  return allPosts.value.filter(post => {

    const titleMatch = filters.value.title === '' || 
      post.title.toLowerCase().includes(filters.value.title.toLowerCase())
    
    const authorMatch = filters.value.author === '' || 
      post.author.name.toLowerCase().includes(filters.value.author.toLowerCase())
    
    let dateMatch = true
    if (filters.value.startDate) {
      const postDate = new Date(post.created_at)
      const startDate = new Date(filters.value.startDate)
      dateMatch = dateMatch && postDate >= startDate
    }
    if (filters.value.endDate) {
      const postDate = new Date(post.created_at)
      const endDate = new Date(filters.value.endDate) 
      dateMatch = dateMatch && postDate <= endDate
    }

    return titleMatch && authorMatch && dateMatch
  })
})

const formatDate = (date: Date | string) => {
  const d = new Date(date)
  return d.toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric' 
  })
}

onMounted(async () => {

  loading.value = true
  try {
    const response = await axios.get('/api/posts')
    allPosts.value = response.data
  } finally {
    loading.value = false
  }
})
</script>