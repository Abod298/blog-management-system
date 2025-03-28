<script setup lang="ts">
import Navbar from '@/components/Navbar.vue'
</script>

<template>
  <div class="min-h-screen bg-gray-50 flex flex-col">
    <Navbar />
    
    <main class="py-4 flex-1">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="px-4 py-4 sm:px-0">

          <div class="mb-2">
            <h1 class="text-3xl font-bold text-gray-900">
              <slot name="header" />
            </h1>
          </div>
          

          <div class="bg-white shadow rounded-lg p-2">
            <router-view /> 
          </div>
        </div>
      </div>
    </main>
    
    <footer class="bg-white border-t border-gray-200">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <p class="text-center text-gray-500 text-sm">
          &copy; {{ new Date().getFullYear() }} ETERNA Blog (TEST). All rights reserved.
        </p>
      </div>
    </footer>
  </div>
</template>