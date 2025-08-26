<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Pengaturan Logo" />
    
    <div class="p-6">
      <div class="max-w-4xl mx-auto">
        <!-- Page Header -->
        <div class="mb-8">
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Pengaturan Logo</h1>
          <p class="mt-2 text-gray-600 dark:text-gray-400">Kelola logo utama dan favicon aplikasi</p>
        </div>

        <!-- Flash Messages -->
        <div v-if="($page.props.flash as any)?.success" class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
          <div class="flex items-center">
            <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span class="text-green-800 dark:text-green-200">{{ ($page.props.flash as any).success }}</span>
          </div>
        </div>

        <div v-if="($page.props.flash as any)?.error" class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
          <div class="flex items-center">
            <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <span class="text-red-800 dark:text-red-200">{{ ($page.props.flash as any).error }}</span>
          </div>
        </div>

        <!-- Toast Notifications -->
        <div 
          v-if="showNotification" 
          class="fixed top-4 right-4 z-50 transition-all duration-300 transform"
          :class="notificationClass"
        >
          <div class="flex items-center gap-3 p-4 rounded-lg shadow-lg border max-w-md">
            <div class="flex-shrink-0">
              <svg v-if="notificationType === 'success'" class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <svg v-else-if="notificationType === 'error'" class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <svg v-else class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="flex-1">
              <p class="font-medium" :class="notificationTextClass">{{ notificationTitle }}</p>
              <p class="text-sm" :class="notificationDescClass">{{ notificationMessage }}</p>
            </div>
            <button 
              @click="hideNotification"
              class="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Settings Grid -->
        <div class="grid gap-8 lg:grid-cols-2">
          <!-- Logo Upload -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <div class="flex items-center gap-3 mb-6">
              <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
              </div>
              <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Logo Utama</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Logo yang ditampilkan di aplikasi</p>
              </div>
            </div>

            <!-- Current Logo Preview -->
            <div v-if="settings.logo" class="mb-6">
              <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Logo Saat Ini:</p>
              <div class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <img 
                  :src="`/storage/${settings.logo}`" 
                  alt="Current Logo" 
                  class="h-16 w-auto object-contain bg-white rounded"
                >
                <div class="flex-1">
                  <p class="text-sm text-gray-600 dark:text-gray-400">{{ settings.logo }}</p>
                  <button 
                    @click="deleteLogo('logo')"
                    class="mt-2 text-sm text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-200 font-medium"
                  >
                    Hapus Logo
                  </button>
                </div>
              </div>
            </div>

            <!-- Logo Upload Form -->
            <form @submit.prevent="updateSettings" enctype="multipart/form-data">
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Upload Logo Baru
                </label>
                <div class="flex items-center justify-center w-full">
                  <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600">
                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                      <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                      </svg>
                      <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">
                        <span class="font-semibold">Klik untuk upload</span> logo
                      </p>
                      <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, SVG (MAX. 2MB)</p>
                    </div>
                    <input @change="handleLogoChange" type="file" class="hidden" accept="image/*" />
                  </label>
                </div>
                <p v-if="logoFile" class="mt-2 text-sm text-green-600 dark:text-green-400">
                  File dipilih: {{ logoFile.name }}
                </p>
              </div>
            </form>
          </div>

          <!-- Favicon Upload -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <div class="flex items-center gap-3 mb-6">
              <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg">
                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                </svg>
              </div>
              <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Favicon</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Icon yang ditampilkan di tab browser</p>
              </div>
            </div>

            <!-- Current Favicon Preview -->
            <div v-if="settings.favicon" class="mb-6">
              <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Favicon Saat Ini:</p>
              <div class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <img 
                  :src="`/storage/${settings.favicon}`" 
                  alt="Current Favicon" 
                  class="h-8 w-8 object-contain bg-white rounded"
                >
                <div class="flex-1">
                  <p class="text-sm text-gray-600 dark:text-gray-400">{{ settings.favicon }}</p>
                  <button 
                    @click="deleteLogo('favicon')"
                    class="mt-2 text-sm text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-200 font-medium"
                  >
                    Hapus Favicon
                  </button>
                </div>
              </div>
            </div>

            <!-- Favicon Upload Form -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Upload Favicon Baru
              </label>
              <div class="flex items-center justify-center w-full">
                <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600">
                  <div class="flex flex-col items-center justify-center pt-5 pb-6">
                    <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                    </svg>
                    <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">
                      <span class="font-semibold">Klik untuk upload</span> favicon
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">PNG, ICO, JPG (MAX. 1MB)</p>
                  </div>
                  <input @change="handleFaviconChange" type="file" class="hidden" accept="image/*,.ico" />
                </label>
              </div>
              <p v-if="faviconFile" class="mt-2 text-sm text-green-600 dark:text-green-400">
                File dipilih: {{ faviconFile.name }}
              </p>
            </div>
          </div>
        </div>

        <!-- App Name Settings -->
        <div class="mt-8 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
          <div class="flex items-center gap-3 mb-6">
            <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
              <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Nama Aplikasi</h3>
              <p class="text-sm text-gray-600 dark:text-gray-400">Nama yang ditampilkan di aplikasi</p>
            </div>
          </div>

          <div class="max-w-md">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Nama Aplikasi
            </label>
            <input 
              v-model="appName"
              type="text" 
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
              placeholder="Masukkan nama aplikasi"
            >
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-8 flex gap-4">
          <button 
            @click="updateSettings"
            :disabled="loading"
            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-medium rounded-lg transition-colors flex items-center gap-2"
          >
            <svg v-if="loading" class="animate-spin w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ loading ? 'Menyimpan...' : 'Simpan Pengaturan' }}
          </button>

          <button 
            @click="resetForm"
            class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors"
          >
            Reset
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

interface Settings {
  logo: string;
  favicon: string;
  app_name: string;
}

const props = defineProps<{
  settings: Settings;
}>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Profile Settings', href: '#' },
  { title: 'Appearance', href: '#' },
  { title: 'Pengaturan Logo', href: '#' }
];

const logoFile = ref<File | null>(null);
const faviconFile = ref<File | null>(null);
const appName = ref(props.settings.app_name);
const loading = ref(false);

// Notification state
const showNotification = ref(false);
const notificationType = ref<'success' | 'error' | 'info'>('success');
const notificationTitle = ref('');
const notificationMessage = ref('');

// Notification computed classes
const notificationClass = computed(() => {
  return {
    'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800': notificationType.value === 'success',
    'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800': notificationType.value === 'error',
    'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800': notificationType.value === 'info'
  };
});

const notificationTextClass = computed(() => {
  return {
    'text-green-800 dark:text-green-200': notificationType.value === 'success',
    'text-red-800 dark:text-red-200': notificationType.value === 'error',
    'text-blue-800 dark:text-blue-200': notificationType.value === 'info'
  };
});

const notificationDescClass = computed(() => {
  return {
    'text-green-600 dark:text-green-400': notificationType.value === 'success',
    'text-red-600 dark:text-red-400': notificationType.value === 'error',
    'text-blue-600 dark:text-blue-400': notificationType.value === 'info'
  };
});

// Notification methods
const showNotificationMessage = (type: 'success' | 'error' | 'info', title: string, message: string) => {
  notificationType.value = type;
  notificationTitle.value = title;
  notificationMessage.value = message;
  showNotification.value = true;
  
  // Auto hide after 5 seconds
  setTimeout(() => {
    hideNotification();
  }, 5000);
};

const hideNotification = () => {
  showNotification.value = false;
};

const handleLogoChange = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files[0]) {
    logoFile.value = target.files[0];
    showNotificationMessage(
      'info', 
      'Logo Dipilih', 
      `File ${target.files[0].name} siap untuk diupload`
    );
  }
};

const handleFaviconChange = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files[0]) {
    faviconFile.value = target.files[0];
    showNotificationMessage(
      'info', 
      'Favicon Dipilih', 
      `File ${target.files[0].name} siap untuk diupload`
    );
  }
};

const updateSettings = async () => {
  // Check if there are any changes
  if (!logoFile.value && !faviconFile.value && appName.value === props.settings.app_name) {
    showNotificationMessage(
      'info', 
      'Tidak Ada Perubahan', 
      'Tidak ada perubahan yang perlu disimpan'
    );
    return;
  }
  
  loading.value = true;
  
  // Show loading notification
  showNotificationMessage('info', 'Menyimpan...', 'Sedang memproses pengaturan logo');
  
  try {
    const formData = new FormData();
    
    if (logoFile.value) {
      formData.append('logo', logoFile.value);
    }
    
    if (faviconFile.value) {
      formData.append('favicon', faviconFile.value);
    }
    
    if (appName.value) {
      formData.append('app_name', appName.value);
    }

    router.post('/settings/logo', formData, {
      preserveScroll: true,
      onSuccess: (page) => {
        logoFile.value = null;
        faviconFile.value = null;
        
        // Reset file inputs
        const logoInput = document.querySelector('input[type="file"]:first-of-type') as HTMLInputElement;
        const faviconInput = document.querySelector('input[type="file"]:last-of-type') as HTMLInputElement;
        if (logoInput) logoInput.value = '';
        if (faviconInput) faviconInput.value = '';
        
        // Show success notification
        showNotificationMessage(
          'success', 
          'Berhasil Disimpan!', 
          'Pengaturan logo telah berhasil diperbarui'
        );
      },
      onError: (errors) => {
        // Show error notification
        const errorMessage = Object.values(errors).flat().join(', ') || 'Terjadi kesalahan saat menyimpan';
        showNotificationMessage(
          'error', 
          'Gagal Menyimpan', 
          errorMessage
        );
      }
    });
  } catch (error) {
    console.error('Error updating settings:', error);
    showNotificationMessage(
      'error', 
      'Error', 
      'Terjadi kesalahan yang tidak terduga'
    );
  } finally {
    loading.value = false;
  }
};

const deleteLogo = (type: 'logo' | 'favicon') => {
  if (confirm(`Yakin ingin menghapus ${type}?`)) {
    showNotificationMessage('info', 'Menghapus...', `Sedang menghapus ${type}`);
    
    router.delete('/settings/logo', {
      data: { type },
      preserveScroll: true,
      onSuccess: () => {
        showNotificationMessage(
          'success', 
          'Berhasil Dihapus!', 
          `${type.charAt(0).toUpperCase() + type.slice(1)} telah berhasil dihapus`
        );
      },
      onError: (errors) => {
        const errorMessage = Object.values(errors).flat().join(', ') || 'Terjadi kesalahan saat menghapus';
        showNotificationMessage(
          'error', 
          'Gagal Menghapus', 
          errorMessage
        );
      }
    });
  }
};

const resetForm = () => {
  logoFile.value = null;
  faviconFile.value = null;
  appName.value = props.settings.app_name;
  
  // Reset file inputs
  const inputs = document.querySelectorAll('input[type="file"]') as NodeListOf<HTMLInputElement>;
  inputs.forEach(input => input.value = '');
  
  // Show reset notification
  showNotificationMessage(
    'info', 
    'Form Direset', 
    'Semua perubahan telah dibatalkan'
  );
};
</script>
