<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import { Button } from '@/components/ui/button'
import { Avatar, AvatarImage, AvatarFallback } from '@/components/ui/avatar'
import { Badge } from '@/components/ui/badge'
import { useToast } from '@/components/ui/toast/use-toast'
import { Loader2 } from 'lucide-vue-next'

const router = useRouter()
const { toast } = useToast()

interface User {
  id: number
  name: string
  last_name: string
  email: string
  phone: string
  role: string
  avatar?: string
}

const users = ref<User[]>([])
const isLoading = ref(false)
const error = ref('')

const fetchUsers = async () => {
  isLoading.value = true
  try {
    const response = await axios.get('/api/users')
    users.value = response.data
  } catch (err) {
    error.value = 'Failed to load users'
    toast({
      title: 'Error',
      description: 'Could not fetch users',
      variant: 'destructive',
    })
  } finally {
    isLoading.value = false
  }
}

const navigateToEdit = (userId: number) => {
  router.push(`/users/${userId}/edit`)
}

onMounted(() => {
  fetchUsers()
})
</script>

<template>
  <div class="container mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">User Management</h1>
    </div>

    <div v-if="isLoading && users.length === 0" class="flex justify-center py-12">
      <Loader2 class="h-8 w-8 animate-spin" />
    </div>

    <div v-else-if="error" class="bg-red-50 border-l-4 border-red-500 p-4 rounded mb-6">
      <p class="text-red-700">{{ error }}</p>
      <Button @click="fetchUsers" variant="ghost" size="sm" class="mt-2 text-red-600">
        Retry
      </Button>
    </div>

    <div v-else class="border rounded-lg">
      <Table>
        <TableHeader>
          <TableRow>
            <TableHead>User</TableHead>
            <TableHead>Contact</TableHead>
            <TableHead>Role</TableHead>
            <TableHead class="text-right">Actions</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="user in users" :key="user.id">
            <TableCell>
              <div class="flex items-center gap-4">
                <Avatar class="h-10 w-10">
                  <AvatarImage :src="user.avatar" />
                  <AvatarFallback>
                    {{ user.name.charAt(0) }}{{ user.last_name.charAt(0) }}
                  </AvatarFallback>
                </Avatar>
                <div>
                  <p class="font-medium">{{ user.name }} {{ user.last_name }}</p>
                  <p class="text-sm text-muted-foreground">{{ user.email }}</p>
                </div>
              </div>
            </TableCell>
            <TableCell>
              <p>{{ user.email }}</p>
              <p class="text-sm text-muted-foreground">{{ user.phone }}</p>
            </TableCell>
            <TableCell>
              <Badge class="text-yellow-400" :variant="user.role === 'admin' ? 'default' : 'outline'">
                {{ user.role }}
              </Badge>
            </TableCell>
            <TableCell class="text-right">
              <Button @click="navigateToEdit(user.id)"  variant="outline" size="sm">
                Edit Role
              </Button>
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>
  </div>
</template>