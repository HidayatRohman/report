<template>
  <AppLayout :breadcrumbs="[{ title: 'Input Transaksi', href: '/transaksi-input' }]">
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <div class="grid auto-rows-min gap-4 md:grid-cols-3">
        <div class="relative h-[300px] overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border col-span-3">
          <div class="p-8">
            <div class="flex items-center justify-between mb-6">
              <div>
                <h1 class="text-2xl font-bold mb-2 text-gray-900 dark:text-white">Input Transaksi</h1>
                <p class="text-gray-600 dark:text-gray-300">Kelola transaksi harian brand Anda</p>
              </div>
              <div class="flex gap-3">
                <button 
                  @click="triggerCsvImport" 
                  class="px-6 py-3 rounded-lg bg-gradient-to-r from-green-600 to-emerald-600 text-white font-medium hover:from-green-700 hover:to-emerald-700 shadow-lg transform hover:scale-105 transition-all duration-200 flex items-center gap-2"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                  </svg>
                  Import CSV
                </button>
                <button 
                  @click="openDialog" 
                  class="px-6 py-3 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium hover:from-blue-700 hover:to-indigo-700 shadow-lg transform hover:scale-105 transition-all duration-200 flex items-center gap-2"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                  </svg>
                  Tambah Transaksi
                </button>
              </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 border-l-4 border-green-500">
                <div class="flex items-center">
                  <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Total Transaksi</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ daftarTransaksi.length }}</p>
                  </div>
                  <div class="p-2 bg-green-100 dark:bg-green-800 rounded-full">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                  </div>
                </div>
              </div>
              <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 border-l-4 border-blue-500">
                <div class="flex items-center">
                  <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Total Nominal</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ formatRupiah(totalNominal) }}</p>
                  </div>
                  <div class="p-2 bg-blue-100 dark:bg-blue-800 rounded-full">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                  </div>
                </div>
              </div>
              <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 border-l-4 border-purple-500">
                <div class="flex items-center">
                  <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Hari Ini</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ transaksiHariIni.length }}</p>
                  </div>
                  <div class="p-2 bg-purple-100 dark:bg-purple-800 rounded-full">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="relative min-h-[200px] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border bg-white dark:bg-gray-800 p-8 mt-4">
        <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Daftar Transaksi</h2>
        <table class="w-full text-left border-collapse">
          <thead>
            <tr>
              <th class="border-b pb-2 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-600">Tanggal</th>
              <th class="border-b pb-2 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-600">Brand</th>
              <th class="border-b pb-2 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-600">Nominal</th>
              <th class="border-b pb-2 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-600">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, idx) in daftarTransaksi" :key="idx" class="hover:bg-gray-50 dark:hover:bg-gray-700">
              <td class="py-2 text-gray-900 dark:text-gray-100">{{ formatTanggal(item.tanggal) }}</td>
              <td class="py-2 text-gray-900 dark:text-gray-100">{{ item.brand }}</td>
              <td class="py-2 text-gray-900 dark:text-gray-100">{{ formatRupiah(item.nominal) }}</td>
              <td class="py-2">
                <button @click="editTransaksi(idx)" class="px-3 py-1 rounded bg-yellow-400 text-white hover:bg-yellow-500">Edit</button>
                <button @click="deleteTransaksi(idx)" class="px-3 py-1 rounded bg-red-500 text-white hover:bg-red-600 ml-2">Hapus</button>
              </td>
            </tr>
          </tbody>
        </table>
        <div v-if="daftarTransaksi.length === 0" class="text-gray-500 dark:text-gray-400 mt-4">Belum ada data transaksi.</div>
      </div>

      <!-- Dialog -->
      <TransaksiDialog 
        :open="dialogOpen" 
        :isEdit="editIdx !== null" 
        :transaksi="editIdx !== null ? daftarTransaksi[editIdx] : undefined"
        :brandList="activeBrandList"
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

      <!-- Hidden CSV Input -->
      <input 
        type="file" 
        ref="csvFileInput" 
        accept=".csv" 
        @change="handleCsvImport" 
        class="hidden"
      />
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed, onMounted } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import TransaksiDialog from './TransaksiDialog.vue';

interface Brand {
  id: number;
  nama_brand: string;
  pemilik: string;
}

interface Transaksi {
  id?: number;
  tanggal: string;
  brand: string;
  nominal: number;
  created_at?: string;
  updated_at?: string;
}

const props = defineProps<{
  brands?: Brand[];
  transaksis?: Transaksi[];
}>();

const page = usePage();
const daftarTransaksi = ref<Transaksi[]>(props.transaksis || []);
const dialogOpen = ref(false);
const editIdx = ref<number | null>(null);
const csvFileInput = ref<HTMLInputElement>();
const showNotification = ref(false);
const notificationMessage = ref('');

// Transform brands for dialog component
const activeBrandList = computed(() => {
  return (props.brands || []).map(brand => ({
    namaBrand: brand.nama_brand,
    namaCV: brand.pemilik
  }));
});

// Check for flash message when component mounts
onMounted(() => {
  const flashMessage = (page.props.flash as any)?.success;
  if (flashMessage) {
    showSuccessNotification(flashMessage as string);
  }
});

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

const totalNominal = computed(() => {
  return daftarTransaksi.value.reduce((total, item) => total + item.nominal, 0);
});

const transaksiHariIni = computed(() => {
  const today = new Date().toISOString().split('T')[0];
  return daftarTransaksi.value.filter(item => item.tanggal === today);
});

function openDialog() {
  editIdx.value = null;
  dialogOpen.value = true;
}

function closeDialog() {
  dialogOpen.value = false;
}

function handleDialogSubmit(data: { tanggal: string; brand: string; nominal: number }) {
  const formData = new FormData();
  formData.append('tanggal', data.tanggal);
  formData.append('brand', data.brand);
  formData.append('nominal', data.nominal.toString());

  if (editIdx.value !== null) {
    // Update existing transaksi
    const transaksi = daftarTransaksi.value[editIdx.value];
    if (transaksi && transaksi.id) {
      formData.append('_method', 'PUT');
      router.post(`/transaksis/${transaksi.id}`, formData, {
        onSuccess: () => {
          closeDialog();
          showSuccessNotification('Transaksi berhasil diperbarui!');
          // Refresh page to show updated data
          setTimeout(() => {
            router.visit('/transaksi-input', { preserveState: false });
          }, 1000);
        },
        onError: () => {
          showSuccessNotification('Terjadi kesalahan saat memperbarui transaksi.');
        }
      });
    }
  } else {
    // Create new transaksi
    router.post('/transaksi-input', formData, {
      onSuccess: () => {
        closeDialog();
        showSuccessNotification('Transaksi berhasil ditambahkan!');
        // Refresh page to show new data
        setTimeout(() => {
          router.visit('/transaksi-input', { preserveState: false });
        }, 1000);
      },
      onError: () => {
        showSuccessNotification('Terjadi kesalahan saat menambahkan transaksi.');
      }
    });
  }
}

function editTransaksi(idx: number) {
  editIdx.value = idx;
  dialogOpen.value = true;
}

function deleteTransaksi(idx: number) {
  const transaksi = daftarTransaksi.value[idx];
  if (transaksi && confirm(`Apakah Anda yakin ingin menghapus transaksi ${transaksi.brand} tanggal ${formatTanggal(transaksi.tanggal)}?`)) {
    if (transaksi.id) {
      router.delete(`/transaksis/${transaksi.id}`, {
        onSuccess: () => {
          showSuccessNotification('Transaksi berhasil dihapus!');
          // Refresh page to show updated data
          setTimeout(() => {
            router.visit('/transaksi-input', { preserveState: false });
          }, 1000);
        },
        onError: () => {
          showSuccessNotification('Terjadi kesalahan saat menghapus transaksi.');
        }
      });
    }
  }
}

function formatRupiah(amount: number): string {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR'
  }).format(amount);
}

function formatTanggal(tanggal: string): string {
  return new Date(tanggal).toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'long',
    year: 'numeric'
  });
}

function triggerCsvImport() {
  csvFileInput.value?.click();
}

function handleCsvImport(event: Event) {
  const target = event.target as HTMLInputElement;
  const file = target.files?.[0];
  
  if (!file) return;
  
  const reader = new FileReader();
  reader.onload = (e) => {
    const csv = e.target?.result as string;
    parseCSV(csv);
  };
  reader.readAsText(file);
}

function parseCSV(csv: string) {
  const lines = csv.split('\n');
  const headers = lines[0].split(',').map(h => h.trim());
  
  // Cari index kolom yang diperlukan
  const tanggalIndex = headers.findIndex(h => h.toLowerCase().includes('tanggal') || h.toLowerCase().includes('date'));
  const brandIndex = headers.findIndex(h => h.toLowerCase().includes('brand') || h.toLowerCase().includes('nama'));
  
  // Untuk data Excel yang diberikan, ambil kolom NYORE COFFEE (index 1)
  const nominalIndex = 1; // NYORE COFFEE ada di kolom B (index 1)
  
  for (let i = 1; i < lines.length; i++) {
    const values = lines[i].split(',').map(v => v.trim());
    
    if (values.length < 2) continue;
    
    // Parse tanggal dari format seperti "1/25", "2/25", dll
    const dateStr = values[0];
    if (!dateStr) continue;
    
    const [day, month] = dateStr.split('/');
    const currentYear = new Date().getFullYear();
    const tanggal = `${currentYear}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
    
    // Parse nominal (hapus Rp, titik, dan koma)
    const nominalStr = values[nominalIndex];
    if (!nominalStr || nominalStr === 'Rp0' || nominalStr === '') continue;
    
    const nominal = parseInt(nominalStr.replace(/[Rp.,]/g, ''));
    if (isNaN(nominal) || nominal === 0) continue;
    
    // Gunakan brand default NYORE COFFEE
    const brand = 'NYORE COFFEE';
    
    // Tambah ke daftar transaksi
    daftarTransaksi.value.push({
      tanggal,
      brand,
      nominal
    });
  }
  
  // Reset input file
  if (csvFileInput.value) {
    csvFileInput.value.value = '';
  }
  
  alert(`Berhasil import ${daftarTransaksi.value.length} transaksi dari CSV`);
}
</script>
