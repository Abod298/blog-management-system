<template>
    <Card>
      <CardHeader class="p-0">
        <Skeleton class="aspect-video w-full rounded-t-lg" />
      </CardHeader>
      <CardContent class="p-6 space-y-4">
        <div class="flex items-center space-x-4">
          <Skeleton class="h-10 w-10 rounded-full" />
          <Skeleton class="h-4 w-[100px]" />
        </div>
        <Skeleton class="h-6 w-3/4" />
        <div class="space-y-2">
          <Skeleton class="h-4 w-full" />
          <Skeleton class="h-4 w-5/6" />
          <Skeleton class="h-4 w-4/6" />
        </div>
        <div class="flex justify-between">
          <Skeleton class="h-4 w-[100px]" />
          <Skeleton class="h-4 w-[80px]" />
        </div>
      </CardContent>
      <CardFooter class="p-6 pt-0">
        <Skeleton class="h-10 w-full rounded-md" />
      </CardFooter>
    </Card>
  </template>
  
  <script setup lang="ts">
  import { Card, CardHeader, CardContent, CardFooter } from '@/components/ui/card'
  import { Skeleton } from '@/components/ui/skeleton'
  </script>