<template>
  <div class="max-w-4xl mx-auto p-6">
    <Card>
      <CardHeader>
        <CardTitle>Yeni Gönderi Oluştur</CardTitle>
        <CardDescription>Aşağıdaki bilgileri doldurarak yeni bir blog gönderisi oluşturun</CardDescription>
      </CardHeader>
      <CardContent>
        <form @submit.prevent="handleSubmit" class="space-y-6">
          <div>
            <Label for="title">Başlık</Label>
            <Input
              id="title"
              v-model="form.title.$value"
              @input="generateSlug"
              :error="form.title.$error"
              placeholder="Gönderi başlığını girin"
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
              placeholder="Gönderi URL'si"
              class="mt-1"
              readonly
            />
            <p class="text-sm text-muted-foreground mt-1">
              Bu, başlığınızdan otomatik olarak oluşturulacaktır
            </p>
          </div>

          <div>
            <Label>Yayın Durumu</Label>
            <div class="mt-2">
              <RadioGroup v-model="form.status.$value" class="flex space-x-4">
                <div class="flex items-center space-x-2">
                  <RadioGroupItem id="draft" value="draft" />
                  <Label for="draft">Taslak</Label>
                </div>
                <div class="flex items-center space-x-2">
                  <RadioGroupItem id="publish" value="publish" />
                  <Label for="publish">Hemen Yayınla</Label>
                </div>
              </RadioGroup>
            </div>
          </div>

          <div>
            <Label>İçerik</Label>
            <QuillEditor
              v-model:content="form.body.$value"
              contentType="html"
              theme="snow"
              toolbar="essential"
              class="mt-1 h-64"
            />
            <p v-if="isSubmitting && form.body.$error" class="text-sm text-red-500 mt-1">
              {{ form.body.$error.message }}
            </p>
          </div>

          <div>
            <Label>Kategoriler</Label>
            <VSelect
              v-model="form.categories.$value"
              multiple
              :options="categories"
              label="title"
              :reduce="category => category.id"
              placeholder="Kategorileri seçin"
              class="mt-1"
            />
            <p v-if="isSubmitting && form.categories.$error" class="text-sm text-red-500 mt-1">
              {{ form.categories.$error.message }}
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
                  Tavsiye edilen boyut: 1200x630px
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

          <div class="flex justify-end gap-4">
            <Button type="button" variant="outline" @click="resetForm">
              Sıfırla
            </Button>
            <Button type="submit" :disabled="isSubmitting">
              <Loader2 v-if="isSubmitting" class="mr-2 h-4 w-4 animate-spin" />
              {{ isSubmitting ? 'Oluşturuluyor...' : 'Gönderi Oluştur' }}
            </Button>
          </div>
        </form>
      </CardContent>
    </Card>
  </div>
</template>

  
  <script setup lang="ts">
  import { ref, computed, onMounted } from 'vue'
  import { QuillEditor } from '@vueup/vue-quill'
  import '@vueup/vue-quill/dist/vue-quill.snow.css'
  import VSelect from 'vue-select'
  import 'vue-select/dist/vue-select.css'
  import { useAuthStore } from '@/stores/authStore'
  import { slugify } from '@/utils/helpers'
  import { defineForm, field, isValidForm } from 'vue-yup-form'
  import * as yup from 'yup'
  import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
  } from '@/components/ui/card'
  import { Button } from '@/components/ui/button'
  import { Input } from '@/components/ui/input'
  import { Label } from '@/components/ui/label'
  import { Loader2, Trash2, Upload } from 'lucide-vue-next'
  import axios from 'axios'
  import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group'
  import { useRouter } from 'vue-router'
  
  const authStore = useAuthStore()
  const fileInput = ref<HTMLInputElement | null>(null)
  const dragOver = ref(false)
  const isSubmitting = ref(false)
  
  const categories = ref<{ id: number; title: string }[]>([])
  const router = useRouter 
  // Form with validation
  const form = defineForm({
    title: field(
      '',
      yup.string().label('Title').required().max(200)
    ),
    slug: field(
      '',
      yup.string().label('Slug').required().max(250)
    ),
    body: field(
      '',
      yup.string().label('Content').required()
    ),
    categories: field(
      [] as string[],
      yup.array().label('Categories').min(1, 'Select at least one category')
    ),
    image: field(
      null as File | null,
      yup
        .mixed()
        .label('Featured Image')
        .test('fileSize', 'Image is too large (max 2MB)', (value) => {
          if (!value) return true
          return value.size <= 2000000
        })
        .test('fileType', 'Unsupported file format (JPEG, PNG only)', (value) => {
          if (!value) return true
          return ['image/jpeg', 'image/png'].includes(value.type)
        })
    ),
    status: field(
      'draft',
      yup.string().label('Status').oneOf(['draft', 'publish'])
    ),
    published_at: field(
      new Date().toISOString().slice(0, 16),
      yup.string().label('Publish Date').when('status', {
        is: 'publish',
        then: (schema) => schema.required('Publish date is required'),
        otherwise: (schema) => schema.notRequired()
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
    form.body.$value = ''
    form.categories.$value = []
    form.image.$value = null
    form.status.$value = 'draft'
    form.published_at.$value = new Date().toISOString().slice(0, 16)
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
      formData.append('body', form.body.$value)
      formData.append('status', form.status.$value)
      
      if (form.status.$value === 'publish') {
        formData.append('published_at', form.published_at.$value)
      }
      
      form.categories.$value.forEach(cat => formData.append('categories[]', cat))
      
      if (form.image.$value) {
        formData.append('image', form.image.$value)
      }
  
      const response = await axios.post('/api/posts', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
  
      resetForm()
      router.push({name : 'my-posts'})
    } catch (error) {
      console.error('Error creating post:', error)
    } finally {
      isSubmitting.value = false
    }
  }
  
  onMounted(async () => {
    try {
      const response = await axios.get('/api/categories')
      categories.value = response.data.map((category: any) => ({
        id: category.id,
        title: category.title
      }))
    } catch (error) {
      console.error('Error fetching categories:', error)
    }
  })
  </script>
  
  <style>
  .v-select {
    @apply rounded-md border border-input bg-background text-sm ring-offset-background;
  }
  .v-select .vs__dropdown-toggle {
    @apply border-none p-0;
  }
  .v-select .vs__selected {
    @apply bg-primary text-primary-foreground rounded-md;
  }
  .v-select .vs__search {
    @apply text-sm;
  }
  </style>