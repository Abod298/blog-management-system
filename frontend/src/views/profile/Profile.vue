<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { defineForm, field, isValidForm, toObject } from 'vue-yup-form'
import * as yup from 'yup'
import { vMask } from 'vue-the-mask'
import { useAuthStore } from '@/stores/authStore'

const router = useRouter()
const isLoading = ref(false)
const avatarPreview = ref('')
const fileInput = ref<HTMLInputElement | null>(null)
const submitted = ref(false)

// Form definition with validation
const form = defineForm({
  name: field('', yup.string().label('ADI').required()),
  last_name: field('', yup.string().label('SYADI').required()),
  email: field('', yup.string().email('Hatali Eposta').label('Email').required()),
  phone: field('', yup.string().matches(/^\+?[\d\s-]{10,}$/, "Hatali Telefon Numarasi")),
})

// Fetch user data
const fetchUserData = async () => {
  try {
    const response = await axios.get('/api/user')
    const user = response.data
    form.name.$value = user.name
    form.last_name.$value = user.last_name
    form.email.$value = user.email
    form.phone.$value = user.phone
    if (user.avatar) {
      avatarPreview.value = user.avatar
    }
  } catch (error) {
    console.error('Error fetching user data:', error)
  }
}

const handleAvatarChange = (e: Event) => {
  const target = e.target as HTMLInputElement
  const file = target.files?.[0]
  if (file) {
    const reader = new FileReader()
    reader.onload = (event) => {
      avatarPreview.value = event.target?.result as string
    }
    reader.readAsDataURL(file)
  }
}


const triggerFileInput = () => {
  fileInput.value?.click()
}

// Update profile
const updateProfile = async () => {
  submitted.value = true
  isLoading.value = true

  if (!isValidForm(form)) {
    window.scrollTo(0, 0)
    isLoading.value = false
    return
  }

  try {
    const formData = new FormData()
    formData.append('_method', 'PUT');
    formData.append('name', form.name.$value)
    formData.append('last_name', form.last_name.$value)
    formData.append('email', form.email.$value)
    formData.append('phone', form.phone.$value)
    if (fileInput.value?.files?.[0]) {
      formData.append('avatar', fileInput.value.files[0])
    }
    
    const response = await axios.post(`/user/profile-information`, formData ,{
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    });

    if (response.data.status === 'verification-link-sent') {
      console.log('Verification email sent')
    }

    if (response.data.avatar) {
      avatarPreview.value = response.data.avatar_url
    }


    useAuthStore().fetchUser();

  } catch (error) {
    console.error('Error updating profile:', error)
    if (axios.isAxiosError(error) && error.response?.status === 422) {
      // Handle validation errors from server
      Object.entries(error.response.data.errors).forEach(([field, messages]) => {
        if (field in form) {
          form[field as keyof typeof form].$error = {
            message: (messages as string[])[0]
          }
        }
      })
    }
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchUserData()
})
</script>

<template>
  <div class="max-w-2xl mx-auto p-6">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-2xl font-bold text-gray-900">Profil Bilgisi</h1>
    </div>

    <form @submit.prevent="updateProfile" class="space-y-6">

      <div class="flex flex-col items-center space-y-4">
        <div class="relative">
          <img 
            :src="avatarPreview || 'https://placehold.co/150'" 
            class="w-32 h-32 rounded-full object-cover border-2 border-gray-200"
            alt="Profil Avatarı"
          >
          <button
            type="button"
            @click="triggerFileInput"
            class="absolute bottom-0 right-0 bg-primary-500 text-white p-2 rounded-full hover:bg-primary-600 transition"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M4 5a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V7a2 2 0 00-2-2h-1.586a1 1 0 01-.707-.293l-1.121-1.121A2 2 0 0011.172 3H8.828a2 2 0 00-1.414.586L6.293 4.707A1 1 0 015.586 5H4zm6 9a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
            </svg>
          </button>
        </div>
        <input 
          type="file" 
          ref="fileInput"
          @change="handleAvatarChange"
          accept="image/*"
          class="hidden"
        >
        <p class="text-sm text-gray-500">Avatarınızı değiştirmek için simgeye tıklayın</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
            {{ form.name.$label }}
          </label>
          <input
            id="name"
            v-model="form.name.$value"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
            :class="{ 'border-red-500': submitted && form.name.$error }"
          >
          <span v-if="submitted && form.name.$error" class="mt-1 text-sm text-red-600">
            {{ form.name.$error.message }}
          </span>
        </div>

        <div>
          <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">
            {{ form.last_name.$label }}
          </label>
          <input
            id="last_name"
            v-model="form.last_name.$value"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
            :class="{ 'border-red-500': submitted && form.last_name.$error }"
          >
          <span v-if="submitted && form.last_name.$error" class="mt-1 text-sm text-red-600">
            {{ form.last_name.$error.message }}
          </span>
        </div>
      </div>

      <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
          {{ form.email.$label }}
        </label>
        <input
          id="email"
          v-model="form.email.$value"
          type="email"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
          :class="{ 'border-red-500': submitted && form.email.$error }"
        >
        <span v-if="submitted && form.email.$error" class="mt-1 text-sm text-red-600">
          {{ form.email.$error.message }}
        </span>
      </div>

      <div>
        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
          Telefon
        </label>
        <input
          id="phone"
          v-model="form.phone.$value"
          v-mask="'(###) ###-####'"
          type="tel"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
          :class="{ 'border-red-500': submitted && form.phone.$error }"
          placeholder="(123) 456-7890"
        >
        <span v-if="submitted && form.phone.$error" class="mt-1 text-sm text-red-600">
          {{ form.phone.$error.message }}
        </span>
      </div>

      <div class="flex justify-end">
        <button
          type="submit"
          class="inline-flex items-center px-4 py-2 border text-indigo-500 border-transparent text-sm font-medium rounded-md shadow-sm bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
          :disabled="isLoading"
        >
          <span v-if="isLoading" class="flex items-center">
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Kaydediliyor...
          </span>
          <span v-else>Değişiklikleri Kaydet</span>
        </button>
      </div>
    </form>
  </div>
</template>
