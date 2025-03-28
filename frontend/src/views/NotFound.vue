<template>
    <div
        class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex flex-col justify-center items-center px-4">
        <div class="max-w-md w-full bg-white p-8 rounded-xl shadow-lg text-center">
            <div class="text-indigo-500 mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-gray-800 mb-3">404 Not Found</h1>
            <p class="text-gray-600 mb-6">Oops! The page you're looking for doesn't exist.</p>
            <div class="flex justify-center gap-4">
                <router-link to="/"
                    class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors">
                    Home
                </router-link>
                <button @click="$router.go(-1)"
                    class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors">
                    Go Back
                </button>
            </div>
        </div>
    </div>
</template>