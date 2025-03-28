
<template>
    <Card>
      <CardHeader>
        <Skeleton class="aspect-video w-full rounded-lg mb-4" />
        <Skeleton class="h-6 w-3/4 mb-2" />
      </CardHeader>
      <CardContent class="space-y-2">
        <Skeleton class="h-4 w-full" />
        <Skeleton class="h-4 w-5/6" />
      </CardContent>
      <CardFooter class="flex justify-between">
        <Skeleton class="h-6 w-16 rounded-full" />
        <Skeleton class="h-9 w-16 rounded-md" />
      </CardFooter>
    </Card>
  </template>
  
  <script setup lang="ts">
  import { Card, CardHeader, CardContent, CardFooter } from '@/components/ui/card'
  import { Skeleton } from '@/components/ui/skeleton'
  </script>