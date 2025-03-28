<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useToast } from '@/components/ui/toast/use-toast'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Avatar, AvatarImage, AvatarFallback } from '@/components/ui/avatar'
import { Loader2, CheckCircle, Clock, Trash2 } from 'lucide-vue-next'

const { toast } = useToast()

interface Comment {
  id: number
  body: string
  user: {
    id: number
    name: string
    last_name: string
    avatar?: string
  }
  created_at: string
  confirmed_by: string | null
}

const comments = ref<Comment[]>([])
const isLoading = ref(false)
const isProcessing = ref<{id: number, action: 'confirm' | 'delete'} | null>(null)

const fetchUnconfirmedComments = async () => {
  isLoading.value = true
  try {
    const response = await axios.get('/api/get-unconfirmed-comments')
    comments.value = response.data
  } catch (error) {
    toast({
      title: 'Error',
      description: 'Failed to load unconfirmed comments',
      variant: 'destructive'
    })
  } finally {
    isLoading.value = false
  }
}

const confirmComment = async (commentId: number) => {
  isProcessing.value = { id: commentId, action: 'confirm' }
  try {
    await axios.post(`/api/comments/${commentId}/confirm`)
    
    toast({
      title: 'Success',
      description: 'Comment confirmed successfully'
    })
    
    // Remove the confirmed comment from the list
    comments.value = comments.value.filter(c => c.id !== commentId)
  } catch (error) {
    toast({
      title: 'Error',
      description: 'Failed to confirm comment',
      variant: 'destructive'
    })
  } finally {
    isProcessing.value = null
  }
}

const deleteComment = async (commentId: number) => {
  isProcessing.value = { id: commentId, action: 'delete' }
  try {
    await axios.delete(`/api/comments/${commentId}`)
    
    toast({
      title: 'Success',
      description: 'Comment deleted successfully'
    })
    
    // Remove the deleted comment from the list
    comments.value = comments.value.filter(c => c.id !== commentId)
  } catch (error) {
    toast({
      title: 'Error',
      description: 'Failed to delete comment',
      variant: 'destructive'
    })
  } finally {
    isProcessing.value = null
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

onMounted(() => {
  fetchUnconfirmedComments()
})
</script>

<template>
  <div class="container mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Comment Moderation</h1>
      <Badge variant="outline" class="px-3 py-1">
        {{ comments.length }} pending
      </Badge>
    </div>

    <!-- Loading state -->
    <div v-if="isLoading && comments.length === 0" class="flex justify-center py-12">
      <Loader2 class="h-8 w-8 animate-spin" />
    </div>

    <!-- Empty state -->
    <div v-else-if="comments.length === 0" class="text-center py-12 border rounded-lg">
      <CheckCircle class="mx-auto h-12 w-12 text-green-500 mb-4" />
      <h3 class="text-lg font-medium">No comments awaiting moderation</h3>
      <p class="text-muted-foreground">All comments have been reviewed</p>
    </div>

    <!-- Comments list -->
    <div v-else class="space-y-6">
      <div v-for="comment in comments" :key="comment.id" class="border rounded-lg p-6">
        <div class="flex items-start gap-4">
          <Avatar class="h-10 w-10">
            <AvatarImage :src="comment.user.avatar" />
            <AvatarFallback>
              {{ comment.user.name.charAt(0) }}{{ comment.user.last_name.charAt(0) }}
            </AvatarFallback>
          </Avatar>
          
          <div class="flex-1">
            <div class="flex items-center justify-between">
              <div>
                <span class="font-medium">
                  {{ comment.user.name }} {{ comment.user.last_name }}
                </span>
                <Badge variant="outline" class="ml-2 h-5 px-1.5 py-0 text-xs text-red-500">
                  Awaiting Approval
                </Badge>
              </div>
              <div class="flex items-center gap-1 text-sm text-muted-foreground">
                <Clock class="h-3 w-3" />
                <span>{{ formatDate(comment.created_at) }}</span>
              </div>
            </div>
            
            <div class="mt-3 prose prose-sm max-w-none" v-html="comment.body"></div>
            
            <div class="mt-4 flex justify-end gap-2">
              <Button 
                @click="deleteComment(comment.id)"
                variant="destructive"
                :disabled="isProcessing?.id === comment.id"
              >
                <Trash2 
                  v-if="!(isProcessing?.id === comment.id && isProcessing?.action === 'delete')" 
                  class="h-4 w-4 mr-2" 
                />
                <Loader2 
                  v-if="isProcessing?.id === comment.id && isProcessing?.action === 'delete'" 
                  class="h-4 w-4 animate-spin mr-2" 
                />
                Delete
              </Button>
              
              <Button 
                @click="confirmComment(comment.id)"
                :disabled="isProcessing?.id === comment.id"
              >
                <CheckCircle 
                  v-if="!(isProcessing?.id === comment.id && isProcessing?.action === 'confirm')" 
                  class="h-4 w-4 mr-2" 
                />
                <Loader2 
                  v-if="isProcessing?.id === comment.id && isProcessing?.action === 'confirm'" 
                  class="h-4 w-4 animate-spin mr-2" 
                />
                Approve
              </Button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>