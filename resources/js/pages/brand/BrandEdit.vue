<template>
  <AppLayout :breadcrumbs="[{ title: 'Edit Brand', href: `/brands/${brand.id}/edit` }]">
    <div class="p-8">
      <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Edit Brand</h1>
        
        <form @submit.prevent="handleSubmit" class="space-y-6 bg-white dark:bg-gray-800 p-6 rounded-xl shadow">
          <!-- Nama Brand -->
          <div class="space-y-2">
            <label for="nama_brand" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Nama Brand</label>
            <input 
              type="text" 
              id="nama_brand" 
              v-model="form.nama_brand" 
              required 
              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 placeholder-gray-500 dark:placeholder-gray-400"
              placeholder="Masukkan nama brand"
            />
            <div v-if="errors.nama_brand" class="text-red-500 text-sm">{{ errors.nama_brand }}</div>
          </div>

          <!-- Pemilik -->
          <div class="space-y-2">
            <label for="pemilik" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Pemilik</label>
            <input 
              type="text" 
              id="pemilik" 
              v-model="form.pemilik" 
              required 
              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 placeholder-gray-500 dark:placeholder-gray-400"
              placeholder="Masukkan nama pemilik"
            />
            <div v-if="errors.pemilik" class="text-red-500 text-sm">{{ errors.pemilik }}</div>
          </div>

          <!-- Deskripsi -->
          <div class="space-y-2">
            <label for="deskripsi" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Deskripsi</label>
            <textarea 
              id="deskripsi" 
              v-model="form.deskripsi" 
              rows="3"
              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 placeholder-gray-500 dark:placeholder-gray-400"
              placeholder="Masukkan deskripsi brand (opsional)"
            ></textarea>
            <div v-if="errors.deskripsi" class="text-red-500 text-sm">{{ errors.deskripsi }}</div>
          </div>

          <!-- Action Buttons -->
          <div class="flex gap-3 pt-4">
            <button 
              type="button" 
              @click="$inertia.visit('/brand-list')"
              class="flex-1 px-6 py-3 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 font-medium hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200"
            >
              Batal
            </button>
            <button 
              type="submit" 
              :disabled="processing"
              class="flex-1 px-6 py-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium hover:from-blue-700 hover:to-indigo-700 shadow-lg transform hover:scale-105 transition-all duration-200 disabled:opacity-50"
            >
              {{ processing ? 'Menyimpan...' : 'Update Brand' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';

interface Brand {
  id: number;
  nama_brand: string;
  pemilik: string;
  deskripsi?: string;
  created_at: string;
  updated_at: string;
}

const props = defineProps<{
  brand: Brand;
  errors?: Record<string, string>;
}>();

const form = useForm({
  nama_brand: props.brand.nama_brand,
  pemilik: props.brand.pemilik,
  deskripsi: props.brand.deskripsi || '',
});

function handleSubmit() {
  form.put(`/brands/${props.brand.id}`, {
    onSuccess: () => {
      // Redirect will be handled by the controller
    }
  });
}
</script>
