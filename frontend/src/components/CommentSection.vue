<script setup>
import CommentCard from './CommentCard.vue'
import CommentForm from './CommentForm.vue'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card'
import { Separator } from '@/components/ui/separator'
import { Badge } from '@/components/ui/badge'

const props = defineProps({
  comments: {
    type: Array,
    default: () => []
  },
})
const emit = defineEmits(['add-comment'])

const handleSubmit = (commentBody) => {
  emit('add-comment', commentBody)
}
</script>

<template>
  <Card class="mt-12">
    <CardHeader>
      <CardTitle class="flex items-center gap-2">
        <span>Yorumlar</span>
        <Badge variant="outline" class="h-6 px-2 py-0">
          {{ comments.length }}
        </Badge>
      </CardTitle>
    </CardHeader>

    <CardContent class="space-y-6">
      <div v-if="comments.length === 0" class="text-center py-8 text-muted-foreground">
        Henüz yorum yok. İlk yorumu siz yapın!
      </div>
     
      <template v-else>
        <Separator />
        <div class="space-y-4">
          <CommentCard 
            v-for="comment in comments" 
            :key="comment.id" 
            :comment="comment" 
          />
        </div>
      </template>
      
      <Separator class="my-6" />
      
      <CommentForm @submit="handleSubmit" />
    </CardContent>
  </Card>
</template>