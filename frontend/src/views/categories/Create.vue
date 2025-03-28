<template>
  <div class="max-w-4xl mx-auto p-6">
    <Card>
      <CardHeader>
        <CardTitle>Yeni Kategori Oluştur</CardTitle>
        <CardDescription>Aşağıdaki bilgileri doldurarak yeni bir kategori oluşturun</CardDescription>
      </CardHeader>
      <CardContent>
        <form @submit.prevent="handleSubmit" class="space-y-6">
          <!-- Başlık Alanı -->
          <div>
            <Label for="title">Başlık</Label>
            <Input
              id="title"
              v-model="form.title.$value"
              @input="generateSlug"
              :error="form.title.$error"
              placeholder="Kategori başlığını girin"
              class="mt-1"
            />
            <p v-if="isSubmitting && form.title.$error" class="text-sm text-red-500 mt-1">
              {{ form.title.$error.message }}
            </p>
          </div>

          <div>
            <Label for="slug">Slug</Label>
            <Input
              id="slug"
              v-model="form.slug.$value"
              placeholder="Kategori URL slug'ı"
              class="mt-1"
              readonly
            />
            <p class="text-sm text-muted-foreground mt-1">
              Bu, başlığınızdan otomatik olarak oluşturulacaktır
            </p>
          </div>

          <div>
            <Label for="description">Açıklama</Label>
            <Textarea
              id="description"
              v-model="form.description.$value"
              :error="form.description.$error"
              placeholder="Kategori açıklamasını girin"
              class="mt-1"
              rows="3"
            />
            <p v-if="isSubmitting && form.description.$error" class="text-sm text-red-500 mt-1">
              {{ form.description.$error.message }}
            </p>
          </div>

          <div>
            <Label>Öne Çıkan Görsel</Label>
            <div
              @click="openFileDialog"
              @drop.prevent="handleDrop"
              @dragover.prevent="dragOver = true"
              @dragleave="dragOver = false"
              :class="[ 
                'mt-1 border-2 border-dashed rounded-md p-6 text-center cursor-pointer', 
                dragOver ? 'border-blue-500 bg-blue-50' : 'border-gray-300', 
                isSubmitting && form.image.$error ? 'border-red-500' : '' 
              ]"
            >
              <input
                type="file"
                ref="fileInput"
                @change="handleFileChange"
                class="hidden"
                accept="image/*"
              />
              <div v-if="!form.image.$value">
                <Upload class="mx-auto h-12 w-12 text-gray-400" />
                <p class="mt-2 text-sm text-gray-600">
                  Görseli buraya sürükleyip bırakın veya seçmek için tıklayın
                </p>
                <p class="text-xs text-gray-500 mt-1">
                  Tavsiye edilen boyut: 800x800px
                </p>
              </div>
              <div v-else class="flex items-center justify-center">
                <img
                  :src="imagePreview"
                  class="max-h-48 rounded-md"
                  alt="Önizleme"
                />
                <Button
                  type="button"
                  variant="ghost"
                  @click.stop="removeImage"
                  class="ml-4"
                >
                  <Trash2 class="h-4 w-4 text-red-500" />
                </Button>
              </div>
            </div>
            <p v-if="isSubmitting && form.image.$error" class="text-sm text-red-500 mt-1">
              {{ form.image.$error.message }}
            </p>
          </div>

          <!-- Gönder Butonu -->
          <div class="flex justify-end gap-4">
            <Button type="button" variant="outline" @click="resetForm">
              Sıfırla
            </Button>
            <Button type="submit" :disabled="isSubmitting">
              <Loader2 v-if="isSubmitting" class="mr-2 h-4 w-4 animate-spin" />
              {{ isSubmitting ? 'Oluşturuluyor...' : 'Kategori Oluştur' }}
            </Button>
          </div>
        </form>
      </CardContent>
    </Card>
  </div>
</template>

  
  <script setup lang="ts">
  import { ref, computed } from 'vue'
  import { defineForm, field, isValidForm } from "vue-yup-form"
  import * as yup from "yup"
  import { useAuthStore } from '@/stores/authStore'
  import { slugify } from '@/utils/helpers'
  import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
  } from '@/components/ui/card'
  import { Button } from '@/components/ui/button'
  import { Input } from '@/components/ui/input'
  import { Textarea } from '@/components/ui/textarea'
  import { Label } from '@/components/ui/label'
  import { Loader2, Trash2, Upload } from 'lucide-vue-next'
  import axios from 'axios'
  
  const authStore = useAuthStore()
  const fileInput = ref<HTMLInputElement | null>(null)
  const dragOver = ref(false)
  const isSubmitting = ref(false)
  

  const form = defineForm({
    title: field(
      '',
      yup.string().label('Title').required().max(100)
    ),
    slug: field(
      '',
      yup.string().label('Slug').required().max(150)
    ),
    description: field(
      '',
      yup.string().label('Description').max(500)
    ),
    image: field(
      null,
      yup
        .mixed()
        .label('Image')
        .test('fileSize', 'Image is too large (max 2MB)', (value) => {
          if (!value) return true // Optional field
          return value.size <= 2000000
        })
        .test('fileType', 'Unsupported file format', (value) => {
          if (!value) return true // Optional field
          return ['image/jpeg', 'image/png', 'image/gif'].includes(value.type)
        })
    )
  })
  
  const imagePreview = computed(() => {
    if (form.image.$value) {
      return URL.createObjectURL(form.image.$value)
    }
    return null
  })
  
  const generateSlug = () => {
    if (form.title.$value) {
      const timestamp = new Date().getTime().toString().slice(-4)
      const username = authStore.user?.name || 'user'
      form.slug.$value = `${slugify(form.title.$value)}-${username}-${timestamp}`
    } else {
      form.slug.$value = ''
    }
  }
  
  const openFileDialog = () => {
    fileInput.value?.click()
  }
  
  const handleFileChange = (e: Event) => {
    const input = e.target as HTMLInputElement
    if (input.files && input.files[0]) {
      form.image.$value = input.files[0]
    }
  }
  
  const handleDrop = (e: DragEvent) => {
    dragOver.value = false
    if (e.dataTransfer?.files && e.dataTransfer.files[0]) {
      form.image.$value = e.dataTransfer.files[0]
    }
  }
  
  const removeImage = () => {
    form.image.$value = null
  }
  
  const resetForm = () => {
    form.title.$value = ''
    form.slug.$value = ''
    form.description.$value = ''
    form.image.$value = null
  }
  
  const handleSubmit = async () => {
    isSubmitting.value = true
    if (!isValidForm(form)) {
      window.scrollTo(0, 0);
      return;
    }
  
    
    try {
      const formData = new FormData()
      formData.append('title', form.title.$value)
      formData.append('slug', form.slug.$value)
      formData.append('description', form.description.$value)
      if (form.image.$value) {
        formData.append('image', form.image.$value)
      }
  
      const response = await axios.post('/api/categories', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
  
      // Reset form on success
      resetForm()
    } catch (error) {
      console.error('Error creating category:', error)
      // Handle API errors here
    } finally {
      isSubmitting.value = false
    }
  }
  </script>
  
  <style>
  /* Add any custom styles here */
  </style>