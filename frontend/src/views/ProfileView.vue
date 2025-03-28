<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { useForm } from 'vue-yup-form'
import * as yup from 'yup'
import { vMask } from 'vue-mask'

const router = useRouter()
const isLoading = ref(false)
const avatarPreview = ref(null)
const fileInput = ref(null)


const user = ref({
  name: '',
  last_name: '',
  email: '',
  phone: '',
  avatar: null
})

const schema = yup.object().shape({
  name: yup.string().required('First name is required'),
  last_name: yup.string().required('Last name is required'),
  email: yup.string().email('Invalid email').required('Email is required'),
  phone: yup.string().matches(/^\(\d{3}\) \d{3}-\d{4}$/, 'Phone format: (123) 456-7890'),
  password: yup.string().min(8, 'Password must be at least 8 characters'),
  password_confirmation: yup.string()
    .oneOf([yup.ref('password'), null], 'Passwords must match')
})

const { errors, handleSubmit, defineField } = useForm({
  validationSchema: schema
})

const [name] = defineField('name')
const [last_name] = defineField('last_name')
const [email] = defineField('email')
const [phone] = defineField('phone')
const [password] = defineField('password')
const [password_confirmation] = defineField('password_confirmation')

// Fetch user data
const fetchUserData = async () => {
  try {
    const response = await axios.get('/api/user')
    user.value = response.data
    name.value = user.value.name
    last_name.value = user.value.last_name
    email.value = user.value.email
    phone.value = user.value.phone
    if (user.value.avatar) {
      avatarPreview.value = user.value.avatar
    }
  } catch (error) {
    console.error('Error fetching user data:', error)
  }
}

const handleAvatarChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    user.value.avatar = file
    const reader = new FileReader()
    reader.onload = (event) => {
      avatarPreview.value = event.target.result
    }
    reader.readAsDataURL(file)
  }
}

// Trigger file input click
const triggerFileInput = () => {
  fileInput.value.click()
}

// Update profile
const updateProfile = handleSubmit(async (values) => {
  isLoading.value = true
  
  try {
    const formData = new FormData()
    formData.append('name', values.name)
    formData.append('last_name', values.last_name)
    formData.append('email', values.email)
    formData.append('phone', values.phone)
    if (values.password) {
      formData.append('password', values.password)
      formData.append('password_confirmation', values.password_confirmation)
    }
    if (user.value.avatar) {
      formData.append('avatar', user.value.avatar)
    }

    const response = await axios.post('/user/profile-information', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })


    if (response.data.status === 'verification-link-sent') {
      console.log('Verification email sent')
    }

    user.value = response.data.user
    if (response.data.avatar) {
      avatarPreview.value = response.data.avatar
    }

  } catch (error) {
    console.error('Error updating profile:', error)
    if (error.response && error.response.status === 422) {
      // Handle validation errors from server
      errors.value = error.response.data.errors
    }
  } finally {
    isLoading.value = false
  }
})

onMounted(() => {
  fetchUserData()
})
</script>

<template>
  <div class="max-w-2xl mx-auto p-6">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-2xl font-bold text-gray-900">Profil Bilgileri</h1>
    </div>

    <form @submit.prevent="updateProfile" class="space-y-6">
      <div class="flex flex-col items-center space-y-4">
        <div class="relative">
          <img 
            :src="avatarPreview || 'https://via.placeholder.com/150'" 
            class="w-32 h-32 rounded-full object-cover border-2 border-gray-200"
            alt="Profil Avatarı"
          >
          <button
            type="button"
            @click="triggerFileInput"
            class="absolute bottom-0 right-0 bg-primary-500 text-white p-2 rounded-full hover:bg-primary-600 transition"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
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
          <label for="name" class="block text-sm font-medium text-gray-700">Ad</label>
          <input
            id="name"
            v-model="name"
            type="text"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
            :class="{ 'border-red-500': errors.name }"
          >
          <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
        </div>

        <div>
          <label for="last_name" class="block text-sm font-medium text-gray-700">Soyad</label>
          <input
            id="last_name"
            v-model="last_name"
            type="text"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
            :class="{ 'border-red-500': errors.last_name }"
          >
          <p v-if="errors.last_name" class="mt-1 text-sm text-red-600">{{ errors.last_name }}</p>
        </div>
      </div>

      <div>
        <label for="email" class="block text-sm font-medium text-gray-700">E-posta</label>
        <input
          id="email"
          v-model="email"
          type="email"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
          :class="{ 'border-red-500': errors.email }"
        >
        <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</p>
      </div>

      <div>
        <label for="phone" class="block text-sm font-medium text-gray-700">Telefon</label>
        <input
          id="phone"
          v-model="phone"
          v-mask="'(###) ###-####'"
          type="tel"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
          :class="{ 'border-red-500': errors.phone }"
          placeholder="(123) 456-7890"
        >
        <p v-if="errors.phone" class="mt-1 text-sm text-red-600">{{ errors.phone }}</p>
      </div>

      <div class="space-y-4 border-t pt-6">
        <h3 class="text-lg font-medium text-gray-900">Şifre Değiştir</h3>
        <p class="text-sm text-gray-500">Mevcut şifrenizi korumak için boş bırakın</p>

        <div>
          <label for="password" class="block text-sm font-medium text-gray-700">Yeni Şifre</label>
          <input
            id="password"
            v-model="password"
            type="password"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
            :class="{ 'border-red-500': errors.password }"
          >
          <p v-if="errors.password" class="mt-1 text-sm text-red-600">{{ errors.password }}</p>
        </div>

        <div>
          <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Şifreyi Onayla</label>
          <input
            id="password_confirmation"
            v-model="password_confirmation"
            type="password"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
            :class="{ 'border-red-500': errors.password_confirmation }"
          >
          <p v-if="errors.password_confirmation" class="mt-1 text-sm text-red-600">{{ errors.password_confirmation }}</p>
        </div>
      </div>

      <div class="flex justify-end">
        <button
          type="submit"
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
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
