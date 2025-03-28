import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'
import { usePermissionsStore } from '@/stores/permissionsStore'
import { runMiddleware } from '@/middlewares/middlewareHandler'
import auth from '@/middlewares/auth'
import guest from '@/middlewares/guest'
import GuestLayout from '@/layouts/GuestLayout.vue'
import AppLayout from '@/layouts/AppLayout.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      component: GuestLayout,
      children: [
        {
          path: '',
          name: 'home',
          component: () => import('@/views/BlogView.vue')
        },
        {
            path: 'categories',
            name: 'categories',
            component: () => import('@/views/CategoriesView.vue')
          },
        {
          path: 'login',
          name: 'login',
          meta: { middleware: [guest] },
          component: () => import('@/views/LoginView.vue')
        },
        {
          path: 'register',
          name: 'register',
          meta: { middleware: [guest] },
          component: () => import('@/views/RegisterView.vue')
        }
      ]
    },


    {
      path: '/',
      component: AppLayout,
      meta: { middleware: [auth] }, 
      children: [
        {
          path: 'about',
          name: 'about',
          component: () => import('@/views/AboutView.vue')
        },
        {
          path: 'posts/create',
          name: 'posts-create',
          meta: { permissions: ['create-posts'] }, 
          component: () => import('@/views/posts/Create.vue')
        },
        {
          path: 'posts/edit/:slug',
          name: 'edit-post',
          meta: { permissions: ['edit-posts'] },
          component: () => import('@/views/posts/Edit.vue')
        },
        {
          path: '/posts/show/:slug',
          name: 'show-post',
          component: () => import('@/views/posts/Show.vue')
        },
        {
          path: 'categories/create',
          name: 'categories-create',
          meta: { permissions: ['create-categories'] },
          component: () => import('@/views/categories/Create.vue')
        },
        {
          path: 'categories/edit/:slug',
          name: 'categories-edit',
          meta: { permissions: ['edit-categories'] },
          component: () => import('@/views/categories/Edit.vue')
        },
        {
          path: 'profile',
          name: 'profile',
          component: () => import('@/views/profile/Profile.vue')
        },
        {
          path: 'my-comments',
          name: 'my-comments',
          component: () => import('@/views/comments/RelatedComments.vue')
        },
        {
          path: 'my-posts',
          name: 'my-posts',
          meta: { permissions: ['create-posts'] },
          component: () => import('@/views/posts/RelatedPosts.vue')
        },
        {
          path: 'my-comments',
          name: 'my-comments',
          component: () => import('@/views/comments/RelatedComments.vue')
        },
        {
          path: 'comments-confirmation',
          name: 'comments-confirmation',
          meta: { permissions: ['confirm-comments'] },
          component: () => import('@/views/comments/CommentsConfirmation.vue')
        },
        {
          path: 'users',
          name: 'users',
          meta: { permissions: ['access-users'] },
          component: () => import('@/views/users/Index.vue')
        },
        {
          path: 'users/:id/edit',
          name: 'edit-users',
          meta: { permissions: ['edit-users'] },
          component: () => import('@/views/users/Edit.vue')
        },
      ]
    },

    { 
      path: '/:pathMatch(.*)*', 
      name: 'NotFound', 
      component: () => import('@/views/NotFound.vue') 
    },
    { 
      path: '/forbidden', 
      name: 'forbidden', 
      component: () => import('@/views/Forbidden.vue') 
    }
  ]
})


router.beforeEach((to, from, next) => {
    if (!to.meta.middleware) {
        return next()
    }
    return runMiddleware(to.meta.middleware, to, from, next)
})

router.beforeEach(async (to) => {
    const permissionsStore = usePermissionsStore()
    if (to.meta.permissions) {
        const hasAll = to.meta.permissions.every(p =>
            permissionsStore.hasPermission && permissionsStore.hasPermission(p)
        )
        if (!hasAll) return 'forbidden'
    }
    return true
})


export default router
