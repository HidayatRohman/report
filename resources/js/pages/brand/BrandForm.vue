<template>
  <AppLayout :breadcrumbs="[{ title: 'Input Brand', href: '/brand-input' }]">
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <div class="grid auto-rows-min gap-4 md:grid-cols-3">
        <div class="relative h-[300px] overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border col-span-3">
          <div class="p-8">
            <div class="flex items-center justify-between mb-6">
              <div>
                <h1 class="text-2xl font-bold mb-2 text-gray-900 dark:text-white">Input Brand</h1>
                <p class="text-gray-600 dark:text-gray-300">Kelola brand dan logo perusahaan Anda</p>
              </div>
              <button 
                @click="openDialog" 
                class="px-6 py-3 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium hover:from-blue-700 hover:to-indigo-700 shadow-lg transform hover:scale-105 transition-all duration-200 flex items-center gap-2"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Tambah Brand
              </button>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 border-l-4 border-blue-500">
                <div class="flex items-center">
                  <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Total Brand</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ brands.length }}</p>
                  </div>
                  <div class="p-2 bg-blue-100 dark:bg-blue-800 rounded-full">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                  </div>
                </div>
              </div>
              <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 border-l-4 border-green-500">
                <div class="flex items-center">
                  <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Brand Aktif</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ brands.length }}</p>
                  </div>
                  <div class="p-2 bg-green-100 dark:bg-green-800 rounded-full">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                  </div>
                </div>
              </div>
              <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 border-l-4 border-yellow-500">
                <div class="flex items-center">
                  <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">CV Partner</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">1</p>
                  </div>
                  <div class="p-2 bg-yellow-100 dark:bg-yellow-800 rounded-full">
                    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 515.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 919.288 0M15 7a3 3 0 11-6 0 3 3 0 616 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Brand List Table -->
        <div class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border col-span-3">
          <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Daftar Brand</h2>
            <div class="overflow-x-auto">
              <table class="w-full text-left border-collapse">
                <thead>
                  <tr class="border-b border-gray-200 dark:border-gray-700">
                    <th class="pb-3 text-sm font-medium text-gray-600 dark:text-gray-300">Logo</th>
                    <th class="pb-3 text-sm font-medium text-gray-600 dark:text-gray-300">Nama Brand</th>
                    <th class="pb-3 text-sm font-medium text-gray-600 dark:text-gray-300">Nama CV</th>
                    <th class="pb-3 text-sm font-medium text-gray-600 dark:text-gray-300">Deskripsi</th>
                    <th class="pb-3 text-sm font-medium text-gray-600 dark:text-gray-300">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="brand in brands" :key="brand.id" class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                    <td class="py-3">
                      <img 
                        v-if="brand.logo_path" 
                        :src="`/storage/${brand.logo_path}`" 
                        alt="Logo" 
                        class="h-10 w-10 rounded object-cover"
                      />
                      <div v-else class="h-10 w-10 rounded bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                      </div>
                    </td>
                    <td class="py-3 text-sm text-gray-900 dark:text-white">{{ brand.nama_brand }}</td>
                    <td class="py-3 text-sm text-gray-600 dark:text-gray-300">{{ brand.pemilik }}</td>
                    <td class="py-3 text-sm text-gray-600 dark:text-gray-300">{{ brand.deskripsi || '-' }}</td>
                    <td class="py-3">
                      <div class="flex gap-2">
                        <button 
                          @click="editBrand(brand)" 
                          class="px-3 py-1 rounded text-xs bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition-colors"
                        >
                          Edit
                        </button>
                        <button 
                          @click="deleteBrand(brand)" 
                          class="px-3 py-1 rounded text-xs bg-red-100 text-red-700 hover:bg-red-200 transition-colors"
                        >
                          Hapus
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
              <div v-if="brands.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                Belum ada data brand. Klik "Tambah Brand" untuk menambahkan brand baru.
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Brand Dialog -->
      <BrandDialog 
        :open="dialogOpen" 
        :isEdit="editingBrand !== null" 
        :brand="editingBrand" 
        @submit="handleDialogSubmit" 
        @close="closeDialog" 
      />

      <!-- Success Notification -->
      <div v-if="showNotification" class="fixed top-4 right-4 z-50 transform transition-all duration-300">
        <div class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-lg shadow-lg flex items-center gap-3">
          <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <span class="font-medium">{{ notificationMessage }}</span>
          <button @click="hideNotification" class="ml-2 text-green-700 hover:text-green-900">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, onMounted } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import BrandDialog from './BrandDialog.vue';

interface Brand {
  id: number;
  nama_brand: string;
  pemilik: string;
  deskripsi?: string;
  logo_path?: string;
  created_at: string;
  updated_at: string;
}

const props = defineProps<{
  brands?: Brand[];
}>();

const page = usePage();
const brands = ref<Brand[]>(props.brands || []);
const dialogOpen = ref(false);
const editingBrand = ref<Brand | null>(null);
const showNotification = ref(false);
const notificationMessage = ref('');

// Check for flash message when component mounts
onMounted(() => {
  const flashMessage = (page.props.flash as any)?.success;
  if (flashMessage) {
    showSuccessNotification(flashMessage as string);
  }
});

function openDialog() {
  editingBrand.value = null;
  dialogOpen.value = true;
}

function closeDialog() {
  dialogOpen.value = false;
  editingBrand.value = null;
}

function showSuccessNotification(message: string) {
  notificationMessage.value = message;
  showNotification.value = true;
  // Auto hide after 3 seconds
  setTimeout(() => {
    hideNotification();
  }, 3000);
}

function hideNotification() {
  showNotification.value = false;
  notificationMessage.value = '';
}

function handleDialogSubmit(data: { nama_brand: string; pemilik: string; deskripsi?: string; logo?: File }) {
  const formData = new FormData();
  formData.append('nama_brand', data.nama_brand);
  formData.append('pemilik', data.pemilik);
  if (data.deskripsi) {
    formData.append('deskripsi', data.deskripsi);
  }
  if (data.logo) {
    formData.append('logo', data.logo);
  }

  if (editingBrand.value) {
    // Update existing brand
    formData.append('_method', 'PUT');
    router.post(`/brands/${editingBrand.value.id}`, formData, {
      onSuccess: () => {
        closeDialog();
        showSuccessNotification('Brand berhasil diperbarui!');
        // Refresh page to show updated data
        setTimeout(() => {
          router.visit('/brand-input', { preserveState: false });
        }, 1000);
      },
      onError: () => {
        showSuccessNotification('Terjadi kesalahan saat memperbarui brand.');
      }
    });
  } else {
    // Create new brand
    router.post('/brand-input', formData, {
      onSuccess: () => {
        closeDialog();
        showSuccessNotification('Brand berhasil ditambahkan!');
        // Refresh page to show new data
        setTimeout(() => {
          router.visit('/brand-input', { preserveState: false });
        }, 1000);
      },
      onError: () => {
        showSuccessNotification('Terjadi kesalahan saat menambahkan brand.');
      }
    });
  }
}

function editBrand(brand: Brand) {
  editingBrand.value = { ...brand };
  dialogOpen.value = true;
}

function deleteBrand(brand: Brand) {
  if (confirm(`Apakah Anda yakin ingin menghapus brand "${brand.nama_brand}"?`)) {
    router.delete(`/brands/${brand.id}`, {
      onSuccess: () => {
        showSuccessNotification('Brand berhasil dihapus!');
        // Refresh page to show updated data
        setTimeout(() => {
          router.visit('/brand-input', { preserveState: false });
        }, 1000);
      },
      onError: () => {
        showSuccessNotification('Terjadi kesalahan saat menghapus brand.');
      }
    });
  }
}
</script>