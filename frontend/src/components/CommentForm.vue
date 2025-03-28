<script setup>
import { ref } from 'vue'
import { Button } from '@/components/ui/button'
import { Textarea } from '@/components/ui/textarea'
import { useToast } from '@/components/ui/toast/use-toast'
import { Avatar, AvatarImage, AvatarFallback } from '@/components/ui/avatar'
import axios from 'axios'

const { toast } = useToast()
const commentBody = ref('')


const emit = defineEmits(['submit'])

const handleSubmit = async () => {
  if (!commentBody.value.trim()) {
    toast({
      title: 'Hata',
      description: 'Lütfen yorum metni girin',
      variant: 'destructive'
    })
    return
  }
  emit('submit', commentBody.value)

  commentBody.value = ''
}
</script>

<template>
  <div class="space-y-4">
    <div class="flex items-center gap-3">
      <Avatar class="h-9 w-9">
        <AvatarImage src="" />
        <AvatarFallback>K</AvatarFallback>
      </Avatar>
      <h3 class="font-medium">Yorum Yap</h3>
    </div>
    
    <form @submit.prevent="handleSubmit" class="space-y-4">
      <Textarea
        v-model="commentBody"
        class="min-h-[120px]"
        placeholder="Düşüncelerinizi paylaşın..."
      />
      
      <div class="flex justify-end">
        <Button type="submit">
          Gönder
        </Button>
      </div>
    </form>
  </div>
</template>