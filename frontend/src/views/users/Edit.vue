<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import { Button } from '@/components/ui/button'
import { Loader2 } from 'lucide-vue-next'
import { useToast } from '@/components/ui/toast/use-toast'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'

const route = useRoute()
const router = useRouter()
const { toast } = useToast()

interface User {
  id: number
  name: string
  last_name: string
  email: string
  role: string
  avatar?: string
}

const user = ref<User | null>(null)
const selectedRole = ref('')
const isLoading = ref(false)
const isSubmitting = ref(false)

const fetchUser = async () => {
  isLoading.value = true
  try {
    const response = await axios.get(`/api/users/${route.params.id}`)
    user.value = response.data
    selectedRole.value = response.data.role
  } catch (err) {
    toast({
      title: 'Error',
      description: 'Failed to load user data',
      variant: 'destructive',
    })
    router.back()
  } finally {
    isLoading.value = false
  }
}

const updateUserRole = async () => {
  isSubmitting.value = true
  try {
    const formData = new FormData()
    formData.append('role', selectedRole.value)
    formData.append('_method', 'PUT') 

    await axios.post(`/api/users/${route.params.id}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })

    toast({
      title: 'Success',
      description: 'User role updated successfully',
    })
    router.push('/users')
  } catch (err) {
    toast({
      title: 'Error',
      description: 'Failed to update user role',
      variant: 'destructive',
    })
  } finally {
    isSubmitting.value = false
  }
}

onMounted(() => {
  fetchUser()
})
</script>

<template>
  <div class="container mx-auto py-8 max-w-2xl">
    <div class="flex items-center mb-6">
      <Button @click="router.back()" variant="ghost" size="sm" class="mr-4">
        Back
      </Button>
      <h1 class="text-2xl font-bold">Edit User Role</h1>
    </div>

    <div v-if="isLoading" class="flex justify-center py-12">
      <Loader2 class="h-8 w-8 animate-spin" />
    </div>

    <div v-else-if="user" class="bg-white dark:bg-gray-900 rounded-lg border p-6">
      <div class="flex items-center gap-4 mb-6">
        <Avatar class="h-12 w-12">
          <AvatarImage :src="user.avatar" />
          <AvatarFallback>
            {{ user.name.charAt(0) }}{{ user.last_name.charAt(0) }}
          </AvatarFallback>
        </Avatar>
        <div>
          <h2 class="text-lg font-semibold">{{ user.name }} {{ user.last_name }}</h2>
          <p class="text-muted-foreground">{{ user.email }}</p>
        </div>
      </div>

      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium mb-1">Current Role</label>
          <p class="text-sm">{{ user.role }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Change Role</label>
          <Select v-model="selectedRole">
            <SelectTrigger class="w-[180px]">
              <SelectValue placeholder="Select role" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="1">Yönetici</SelectItem>
              <SelectItem value="2">Yazar</SelectItem>
              <SelectItem value="3">Kullancı</SelectItem>
            </SelectContent>
          </Select>
        </div>

        <div class="flex justify-end gap-2 pt-4">
          <Button @click="router.back()" variant="outline">Cancel</Button>
          <Button @click="updateUserRole" :disabled="isSubmitting">
            <Loader2 v-if="isSubmitting" class="h-4 w-4 animate-spin mr-2" />
            {{ isSubmitting ? 'Saving...' : 'Save Changes' }}
          </Button>
        </div>
      </div>
    </div>
  </div>
</template>