<script setup>
import { Avatar, AvatarImage, AvatarFallback } from '@/components/ui/avatar'
import { Badge } from '@/components/ui/badge'
import { CheckCircle, Clock } from 'lucide-vue-next'
import { useAuthStore } from '@/stores/authStore'

const authStore = useAuthStore()

const props = defineProps({
  comment: {
    type: Object,
    required: true,
    default: () => ({
      body: '',
      post: {},
      user: {
        id: '',
        name: '',
        last_name: '',
        avatar: ''
      },
      created_at: '',
      updated_at: '',
      confirmed_by: '',
      deleted_at: ''
    })
  }
})
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('tr-TR', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
const handleDelete = async () => {
  try {
    const response = await axios.delete(`/api/comments/${props.comment.id}`) 
    if (response.status === 200) {
      toast({
        title: 'Yorum Silindi',
        description: 'Yorum başarıyla silindi.',
        variant: 'success'
      })
      window.location.reload();
    }
  } catch (error) {
    toast({
      title: 'Hata',
      description: 'Yorum silinirken bir hata oluştu.',
      variant: 'destructive'
    })
    console.error('Failed to delete comment:', error)
  }
}
</script>

<template>
  <div class="flex gap-4" v-if="authStore.user.id == comment.user.id || comment.confirmed_by">
    <Avatar class="h-10 w-10">
      <AvatarImage v-if="comment.user.avatar" :src="comment.user.avatar" />
      <AvatarFallback>
        {{ comment.user.name.charAt(0) }}{{ comment.user.last_name.charAt(0) }}
      </AvatarFallback>
    </Avatar>

    <div class="flex-1 space-y-2">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
          <span class="font-medium">
            {{ comment.user.name }} {{ comment.user.last_name }}
          </span>

          <Badge variant="outline" class="h-5 px-1.5 py-0 text-xs" v-if="comment.confirmed_by">
            <CheckCircle class="h-3 w-3 mr-1" />
            {{ comment.confirmed_by }} Onayladı
          </Badge>
          <Badge variant="outline" v-else-if="comment.deleted_at" class="h-5 px-1.5 py-0 text-xs text-red-400">
            Silinmiş
          </Badge>
          <Badge variant="outline" class="h-5 px-1.5 py-0 text-xs text-red-300" v-else>
            Onay Bekliyor , Sizden Başka Kimse Göremez.
          </Badge>
        </div>

        <div class="flex items-center gap-1 text-sm text-muted-foreground">
          <Clock class="h-3 w-3" />
          <span>{{ formatDate(comment.created_at) }}</span>
        </div>
      </div>

      <div class="prose prose-sm max-w-none dark:prose-invert" v-html="comment.body"></div>
    </div>
  </div>
</template>