<template>
  <AppLayout :breadcrumbs="[{ title: 'Edit Brand', href: '/brand-edit' }]">
    <div class="p-8">
      <h1 class="text-2xl font-bold mb-4">Edit Brand</h1>
      <!-- Form edit brand, isi data dari backend/state -->
    </div>
  </AppLayout>
</template>
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
</script>
