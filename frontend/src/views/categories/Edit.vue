<template>
    <div class="max-w-4xl mx-auto p-6">
      <Card>
        <CardHeader>
          <CardTitle>Edit Category</CardTitle>
          <CardDescription>Update the category details below</CardDescription>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="handleSubmit" class="space-y-6">
            <!-- Title Field -->
            <div>
              <Label for="title">Title</Label>
              <Input
                id="title"
                v-model="form.title.$value"
                @input="generateSlug"
                :error="form.title.$error"
                placeholder="Enter category title"
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
                placeholder="Category URL slug"
                class="mt-1"
                readonly
              />
              <p class="text-sm text-muted-foreground mt-1">
                This will be automatically generated from your title
              </p>
            </div>
  
            <div>
              <Label for="description">Description</Label>
              <Textarea
                id="description"
                v-model="form.description.$value"
                :error="form.description.$error"
                placeholder="Enter category description"
                class="mt-1"
                rows="3"
              />
              <p v-if="isSubmitting && form.description.$error" class="text-sm text-red-500 mt-1">
                {{ form.description.$error.message }}
              </p>
            </div>
  
            <div>
              <Label>Featured Image</Label>
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
                <div v-if="!form.image.$value && !currentImage">
                  <Upload class="mx-auto h-12 w-12 text-gray-400" />
                  <p class="mt-2 text-sm text-gray-600">
                    Drag & drop an image here, or click to select
                  </p>
                  <p class="text-xs text-gray-500 mt-1">
                    Recommended size: 800x800px
                  </p>
                </div>
                <div v-else class="flex items-center justify-center">
                  <img
                    :src="imagePreview"
                    class="max-h-48 rounded-md"
                    alt="Preview"
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
  
            <!-- Submit Button -->
            <div class="flex justify-end gap-4">
              <Button type="button" variant="outline" @click="resetForm">
                Reset
              </Button>
              <Button type="submit" :disabled="isSubmitting">
                <Loader2 v-if="isSubmitting" class="mr-2 h-4 w-4 animate-spin" />
                {{ isSubmitting ? 'Updating...' : 'Update Category' }}
              </Button>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>
  </template>
  
  <script setup lang="ts">
  import { ref, computed, onMounted } from 'vue'
  import { useRoute } from 'vue-router'
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
  
  const route = useRoute()
  const authStore = useAuthStore()
  const fileInput = ref<HTMLInputElement | null>(null)
  const dragOver = ref(false)
  const isSubmitting = ref(false)
  const currentImage = ref<string | null>(null)
  const categoryId = ref<number | null>(null)
  
  // Define form with validation
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
    return currentImage.value || null
  })
  
  const generateSlug = () => {
    if (form.title.$value) {
      const timestamp = new Date().getTime().toString().slice(-4)
      const username = authStore.user?.username || 'user'
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
      currentImage.value = null // Clear the current image when new one is selected
    }
  }
  
  const handleDrop = (e: DragEvent) => {
    dragOver.value = false
    if (e.dataTransfer?.files && e.dataTransfer.files[0]) {
      form.image.$value = e.dataTransfer.files[0]
      currentImage.value = null // Clear the current image when new one is dropped
    }
  }
  
  const removeImage = () => {
    form.image.$value = null
    currentImage.value = null
  }
  
  const resetForm = () => {
    // Reset to initial values
    form.title.$value = initialFormData.title
    form.slug.$value = initialFormData.slug
    form.description.$value = initialFormData.description
    form.image.$value = null
    currentImage.value = initialFormData.imageUrl
  }
  
  let initialFormData = {
    title: '',
    slug: '',
    description: '',
    imageUrl: null as string | null
  }
  
  const fetchCategory = async () => {
    try {
      const response = await axios.get(`/api/categories/${route.params.slug}`)
      const category = response.data
      
      form.title.$value = category.title
      form.slug.$value = category.slug
      form.description.$value = category.description || ''
      currentImage.value = category.image || null
      categoryId.value = category.id
      

      initialFormData = {
        title: category.title,
        slug: category.slug,
        description: category.description || '',
        imageUrl: category.image || null
      }
    } catch (error) {
      console.error('Error fetching category:', error)

    }
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
   
      formData.append('_method', 'PUT')
  
      const response = await axios.post(`/api/categories/${categoryId.value}`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })

    } catch (error) {
      console.error('Error updating category:', error)
    } finally {
      isSubmitting.value = false
    }
  }
  
  onMounted(() => {
    fetchCategory()
  })
  </script>
  
  <style>
  /* Add any custom styles here */
  </style>