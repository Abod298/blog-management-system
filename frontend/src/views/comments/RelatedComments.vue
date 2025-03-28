<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { useToast } from '@/components/ui/toast/use-toast'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Avatar, AvatarImage, AvatarFallback } from '@/components/ui/avatar'
import { Loader2, CheckCircle, Clock, Trash2, ArrowRight } from 'lucide-vue-next'

const router = useRouter()
const { toast } = useToast()

interface Comment {
  id: number
  body: string
  created_at: string
  deleted_at: string | null
  confirmed_by: string | null
  confirmed_at: string | null
  post: {
    id: number
    title: string
    slug: string
  }
}

const comments = ref<Comment[]>([])
const isLoading = ref(false)
const isDeleting = ref<number | null>(null)

const fetchUserComments = async () => {
  isLoading.value = true
  try {
    const response = await axios.get('/api/get-related-comments')
    comments.value = response.data
  } catch (error) {
    toast({
      title: 'Hata',
      description: 'Yorumlar yüklenirken bir sorun oluştu',
      variant: 'destructive'
    })
  } finally {
    isLoading.value = false
  }
}

const deleteComment = async (commentId: number) => {
  isDeleting.value = commentId
  try {
    await axios.delete(`/api/comments/${commentId}`)
    
    toast({
      title: 'Başarılı',
      description: 'Yorum başarıyla silindi'
    })
    
    // Update the comment's deleted_at status
    comments.value = comments.value.map(c => 
      c.id === commentId ? { ...c, deleted_at: new Date().toISOString() } : c
    )
  } catch (error) {
    toast({
      title: 'Hata',
      description: 'Yorum silinirken bir hata oluştu',
      variant: 'destructive'
    })
  } finally {
    isDeleting.value = null
  }
}

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('tr-TR', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const navigateToPost = (postSlug: string) => {
  router.push({ name: 'show-post', params: { slug: postSlug } })
}

onMounted(() => {
  fetchUserComments()
})
</script>

<template>
  <div class="container mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Yorumlarım</h1>
      <Badge variant="outline" class="px-3 py-1">
        {{ comments.length }} yorum
      </Badge>
    </div>

    <!-- Loading state -->
    <div v-if="isLoading && comments.length === 0" class="flex justify-center py-12">
      <Loader2 class="h-8 w-8 animate-spin" />
    </div>

    <!-- Empty state -->
    <div v-else-if="comments.length === 0" class="text-center py-12 border rounded-lg">
      <CheckCircle class="mx-auto h-12 w-12 text-green-500 mb-4" />
      <h3 class="text-lg font-medium">Henüz yorum yapmamışsınız</h3>
      <p class="text-muted-foreground">Yazılara yorum yapmaya ne dersiniz?</p>
    </div>

    <!-- Comments list -->
    <div v-else class="space-y-6">
      <div 
        v-for="comment in comments" 
        :key="comment.id" 
        class="border rounded-lg p-6"
        :class="{ 'opacity-70': comment.deleted_at }"
      >
        <div class="flex justify-between items-start mb-4">
          <div>
            <div class="flex items-center gap-2 mb-2">
              <Badge 
                variant="outline" 
                class="h-6"
                :class="comment.confirmed_by ? 'text-green-600' : 'text-red-600'"
              >
                <CheckCircle v-if="comment.confirmed_by" class="h-4 w-4 mr-1" />
                <Clock v-else class="h-4 w-4 mr-1" />
                {{ comment.confirmed_by ? `${comment.confirmed_by} tarafından onaylandı` : 'Onay bekliyor (sizden başka kimse göremez)' }}
              </Badge>
              <span class="text-sm text-muted-foreground">
                {{ formatDate(comment.created_at) }}
                <span v-if="comment.deleted_at"> • Silinmiş</span>
              </span>
            </div>
            
            <div 
              class="prose prose-sm max-w-none" 
              v-html="comment.body"
              :class="{ 'line-through': comment.deleted_at }"
            ></div>
          </div>
          
          <!-- Show "Silinmiş" badge instead of delete button if soft-deleted -->
          <Badge 
            v-if="comment.deleted_at" 
            variant="outline" 
            class="text-gray-500 h-8 px-3"
          >
            Silinmiş
          </Badge>
          <Button 
            v-else
            @click="deleteComment(comment.id)"
            variant="ghost" 
            size="sm"
            class="text-red-600 hover:text-red-800"
            :disabled="isDeleting === comment.id"
          >
            <Trash2 v-if="isDeleting !== comment.id" class="h-4 w-4" />
            <Loader2 v-else class="h-4 w-4 animate-spin" />
          </Button>
        </div>
        
        <div class="flex items-center justify-between pt-4 border-t">
          <span class="text-sm text-muted-foreground">
            Gönderi: {{ comment.post.title }}
          </span>
          <Button 
            @click="navigateToPost(comment.post.slug)"
            variant="link" 
            size="sm"
            class="text-primary hover:text-primary/90"
          >
            Gönderiye git <ArrowRight class="h-4 w-4 ml-1" />
          </Button>
        </div>
      </div>
    </div>
  </div>
</template>