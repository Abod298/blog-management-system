<template>
  <div class="min-h-screen bg-gray-50 flex flex-col justify-center items-center px-4">
      <div class="max-w-md w-full bg-white p-8 rounded-lg shadow-md text-center">
          <div class="text-red-500 mb-6">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
          </div>
          <h1 class="text-3xl font-bold text-gray-800 mb-2">403 Yasaklı</h1>
          <p class="text-gray-600 mb-6">Bu kaynağa erişim izniniz yok.</p>
          <router-link 
              to="/"
              class="inline-block px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors"
          >
              Anasayfaya Dön
          </router-link>
      </div>
  </div>
</template>
