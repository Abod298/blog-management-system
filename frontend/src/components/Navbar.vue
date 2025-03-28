
<template>
  <nav class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">

        <div class="flex items-center">
          <router-link to="/" class="flex-shrink-0">
            <span class="text-xl font-bold text-gray-800">MyBlog</span>
          </router-link>
          <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
            <router-link
              v-for="item in allNavItems"
              :key="item.name"
              :to="item.path"
              class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
              active-class="border-blue-500 text-gray-900"
            >
              {{ item.name }}
            </router-link>
          </div>
        </div>

        <div class="flex items-center">
          <div v-if="!authStore.isAuthenticated" class="flex space-x-4">
            <router-link
              to="/login"
              class="text-gray-500 hover:text-gray-700 px-3 py-2 text-sm font-medium"
            >
              Login
            </router-link>
            <router-link
              to="/register"
              class="bg-blue-600 text-white px-3 py-2 rounded-md text-sm font-medium hover:bg-blue-700"
            >
              Register
            </router-link>
          </div>

          <div v-else class="ml-4 relative">
            <div>
              <button
                @click="toggleUserMenu"
                class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                id="user-menu"
                aria-haspopup="true"
              >
                <span class="sr-only">Open user menu</span>
                <span class="mr-2 text-gray-700">{{ authStore.user.name + ' ' + authStore.user.last_name}}</span>
                <Avatar>
                  <AvatarImage v-if="authStore.user.avatar" :src="authStore.user.avatar" />
                  <AvatarFallback>
                    {{ authStore.user.name.charAt(0)+authStore.user.last_name.charAt(0) }}
                  </AvatarFallback>
                </Avatar>
              </button>
            </div>


            <transition
              enter-active-class="transition ease-out duration-100"
              enter-from-class="transform opacity-0 scale-95"
              enter-to-class="transform opacity-100 scale-100"
              leave-active-class="transition ease-in duration-75"
              leave-from-class="transform opacity-100 scale-100"
              leave-to-class="transform opacity-0 scale-95"
            >
              <div
                v-show="isUserMenuOpen"
                class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 z-50"
                role="menu"
                aria-orientation="vertical"
                aria-labelledby="user-menu"
              >
                <router-link
                  to="/profile"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  role="menuitem"
                  @click="isUserMenuOpen = false"
                >
                  Your Profile
                </router-link>
                <button
                  @click="handleLogout"
                  class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  role="menuitem"
                >
                  Sign out
                </button>
              </div>
            </transition>
          </div>
        </div>
      </div>
    </div>
    <div class="sm:hidden">
      <div class="pt-2 pb-3 space-y-1">
        <router-link
          v-for="item in allNavItems.value"
          :key="item.name"
          :to="item.path"
          class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium"
          active-class="bg-blue-50 border-blue-500 text-blue-700"
        >
          {{ item.name }}
        </router-link>
      </div>
    </div>
  </nav>
</template>

<script setup lang="ts">
import { useAuthStore } from '@/stores/authStore'
import { usePermissionsStore } from '@/stores/permissionsStore'
import { Avatar, AvatarImage, AvatarFallback } from '@/components/ui/avatar'
import { ref, onMounted, onBeforeUnmount,computed } from 'vue'


const permissionsStore = usePermissionsStore()
const authStore = useAuthStore()
const isAuthenticated = computed(() => authStore.isAuthenticated)
const isUserMenuOpen = ref(false)


const mainNavItems = [
  { name: 'Blog', path: '/' },
  { name: 'Kategoriler', path: '/categories' },
]

const navItems = [
  { name: 'Yazılarım', path: '/my-posts', authOnly: true, permission: 'create-posts' },
  { name: 'Yorumlarım', path: '/my-comments' , authOnly: true, permission: 'create-comments' },
  { name: 'Yorumları Onaylama', path: '/comments-confirmation', authOnly: true, permission: 'confirm-comments' },
  { name: 'Kullancılar', path: '/users', authOnly: true, permission: 'access-users' },
]


const allNavItems = computed(() => [
  ...mainNavItems,
  ...navItems.filter(item => {
    if (item.authOnly && !isAuthenticated.value) return false
    if (item.permission && !permissionsStore.hasPermission(item.permission)) return false
    return true
  })
])
const toggleUserMenu = () => {
  isUserMenuOpen.value = !isUserMenuOpen.value
}

const handleLogout = () => {
  authStore.logout()
  isUserMenuOpen.value = false
}

const handleClickOutside = (event: MouseEvent) => {
  const target = event.target as HTMLElement
  if (!target.closest('#user-menu') && !target.closest('.user-menu-dropdown')) {
    isUserMenuOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

