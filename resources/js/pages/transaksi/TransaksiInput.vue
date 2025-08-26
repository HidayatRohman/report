<template>
  <AppLayout :breadcrumbs="[{ title: 'Input Transaksi', href: '/transaksi-input' }]">
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <div class="grid auto-rows-min gap-4 md:grid-cols-3">
        <div class="relative h-[300px] overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border col-span-3">
          <div class="p-8">
            <div class="flex items-center justify-between mb-6">
              <div>
                <h1 class="text-2xl font-bold mb-2">Input Transaksi</h1>
                <p class="text-gray-600">Kelola transaksi harian brand Anda</p>
              </div>
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

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div class="bg-white rounded-xl shadow-lg p-4 border-l-4 border-green-500">
                <div class="flex items-center">
                  <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600">Total Transaksi</p>
                    <p class="text-xl font-bold text-gray-900">{{ daftarTransaksi.length }}</p>
                  </div>
                  <div class="p-2 bg-green-100 rounded-full">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                  </div>
                </div>
              </div>
              <div class="bg-white rounded-xl shadow-lg p-4 border-l-4 border-blue-500">
                <div class="flex items-center">
                  <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600">Total Nominal</p>
                    <p class="text-xl font-bold text-gray-900">{{ formatRupiah(totalNominal) }}</p>
                  </div>
                  <div class="p-2 bg-blue-100 rounded-full">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                  </div>
                </div>
              </div>
              <div class="bg-white rounded-xl shadow-lg p-4 border-l-4 border-purple-500">
                <div class="flex items-center">
                  <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600">Hari Ini</p>
                    <p class="text-xl font-bold text-gray-900">{{ transaksiHariIni.length }}</p>
                  </div>
                  <div class="p-2 bg-purple-100 rounded-full">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="relative min-h-[200px] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border bg-white p-8 mt-4">
        <h2 class="text-xl font-semibold mb-4">Daftar Transaksi</h2>
        <table class="w-full text-left border-collapse">
          <thead>
            <tr>
              <th class="border-b pb-2">Tanggal</th>
              <th class="border-b pb-2">Brand</th>
              <th class="border-b pb-2">Nominal</th>
              <th class="border-b pb-2">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, idx) in daftarTransaksi" :key="idx" class="hover:bg-gray-50">
              <td class="py-2">{{ formatTanggal(item.tanggal) }}</td>
              <td class="py-2">{{ item.brand }}</td>
              <td class="py-2">{{ formatRupiah(item.nominal) }}</td>
              <td class="py-2">
                <button @click="editTransaksi(idx)" class="px-3 py-1 rounded bg-yellow-400 text-white hover:bg-yellow-500">Edit</button>
                <button @click="deleteTransaksi(idx)" class="px-3 py-1 rounded bg-red-500 text-white hover:bg-red-600 ml-2">Hapus</button>
              </td>
            </tr>
          </tbody>
        </table>
        <div v-if="daftarTransaksi.length === 0" class="text-gray-500 mt-4">Belum ada data transaksi.</div>
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
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed, onMounted } from 'vue';
import TransaksiDialog from './TransaksiDialog.vue';

const daftarTransaksi = ref<Array<{ tanggal: string; brand: string; nominal: number }>>([]);
const dialogOpen = ref(false);
const editIdx = ref<number | null>(null);

// Simulasi data brand (nanti bisa ambil dari API atau state management)
const brandList = ref([
  { namaBrand: 'Nike', namaCV: 'CV Sportindo' },
  { namaBrand: 'Adidas', namaCV: 'CV Atletik' },
  { namaBrand: 'Puma', namaCV: 'CV Dinamis' },
]);

// Reactive brand list yang bisa diambil dari localStorage atau state management
const daftarBrand = ref<Array<{ namaBrand: string; namaCV: string; logoUrl: string | null }>>([]);

// Update brandList berdasarkan daftarBrand yang ada
const activeBrandList = computed(() => {
  // Jika ada data brand yang sudah diinput, gunakan itu
  if (daftarBrand.value.length > 0) {
    return daftarBrand.value.map(brand => ({
      namaBrand: brand.namaBrand,
      namaCV: brand.namaCV
    }));
  }
  // Fallback ke data simulasi jika belum ada brand yang diinput
  return brandList.value;
});

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
  if (editIdx.value !== null) {
    daftarTransaksi.value[editIdx.value] = { ...data };
  } else {
    daftarTransaksi.value.push({ ...data });
  }
}

function editTransaksi(idx: number) {
  editIdx.value = idx;
  dialogOpen.value = true;
}

function deleteTransaksi(idx: number) {
  daftarTransaksi.value.splice(idx, 1);
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

// Load brand data dari localStorage jika ada
onMounted(() => {
  const savedBrands = localStorage.getItem('daftarBrand');
  if (savedBrands) {
    try {
      daftarBrand.value = JSON.parse(savedBrands);
    } catch (e) {
      console.error('Error parsing saved brands:', e);
    }
  }
});
</script>
