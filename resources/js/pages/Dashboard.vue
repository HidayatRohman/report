<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import RevenueChart from '@/components/RevenueChart.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

// Data refs
const daftarBrand = ref<Array<{ namaBrand: string; namaCV: string; logoUrl: string | null }>>([]);
const daftarTransaksi = ref<Array<{ tanggal: string; brand: string; nominal: number }>>([]);

// Computed properties
const totalBrand = computed(() => daftarBrand.value.length);
const totalTransaksi = computed(() => daftarTransaksi.value.length);
const totalNominal = computed(() => {
  return daftarTransaksi.value.reduce((total, item) => total + item.nominal, 0);
});

const transaksiHariIni = computed(() => {
  const today = new Date().toISOString().split('T')[0];
  return daftarTransaksi.value.filter(item => item.tanggal === today).length;
});

const recentTransaksi = computed(() => {
  return daftarTransaksi.value
    .sort((a, b) => new Date(b.tanggal).getTime() - new Date(a.tanggal).getTime())
    .slice(0, 5);
});

// Load data from localStorage
onMounted(() => {
  // Load brand data
  const savedBrands = localStorage.getItem('daftarBrand');
  if (savedBrands) {
    try {
      daftarBrand.value = JSON.parse(savedBrands);
    } catch (e) {
      console.error('Error parsing saved brands:', e);
    }
  }

  // Load transaction data (simulate from seeded data or localStorage)
  const savedTransaksi = localStorage.getItem('daftarTransaksi');
  if (savedTransaksi) {
    try {
      daftarTransaksi.value = JSON.parse(savedTransaksi);
    } catch (e) {
      console.error('Error parsing saved transactions:', e);
    }
  } else {
    // Generate sample data if no data exists
    generateSampleData();
  }
});

// Helper functions
function formatRupiah(amount: number): string {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR'
  }).format(amount);
}

function formatTanggal(tanggal: string): string {
  return new Date(tanggal).toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  });
}

function generateSampleData() {
  // Generate sample transactions for demo
  const sampleTransaksi = [
    { tanggal: '2025-01-25', brand: 'NYORE COFFEE', nominal: 150000 },
    { tanggal: '2025-01-24', brand: 'Nike', nominal: 250000 },
    { tanggal: '2025-01-23', brand: 'Adidas', nominal: 180000 },
    { tanggal: new Date().toISOString().split('T')[0], brand: 'NYORE COFFEE', nominal: 95000 },
    { tanggal: new Date().toISOString().split('T')[0], brand: 'Puma', nominal: 120000 },
  ];
  daftarTransaksi.value = sampleTransaksi;
}

// Action handlers
function navigateToBrand() {
  router.visit('/brand-input');
}

function navigateToTransaksi() {
  router.visit('/transaksi-input');
}

function importCSV() {
  router.visit('/transaksi-input');
}

function generateReport() {
  alert('Fitur laporan akan segera hadir!');
}
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <!-- Welcome Section -->
            <div class="grid auto-rows-min gap-4 md:grid-cols-1">
                <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <div class="p-8 bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-3xl font-bold mb-2">Selamat Datang di Dashboard</h1>
                                <p class="text-blue-100">Kelola brand dan transaksi Anda dengan mudah</p>
                            </div>
                            <div class="hidden md:block">
                                <svg class="w-16 h-16 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 00-2 2h2a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="grid auto-rows-min gap-4 md:grid-cols-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Total Brand</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ totalBrand }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Brand terdaftar</p>
                        </div>
                        <div class="p-3 bg-blue-100 dark:bg-blue-800 rounded-full">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Total Transaksi</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ totalTransaksi }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Semua transaksi</p>
                        </div>
                        <div class="p-3 bg-green-100 dark:bg-green-800 rounded-full">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Total Nominal</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ formatRupiah(totalNominal) }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Keseluruhan</p>
                        </div>
                        <div class="p-3 bg-purple-100 dark:bg-purple-800 rounded-full">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-l-4 border-orange-500">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Hari Ini</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ transaksiHariIni }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Transaksi</p>
                        </div>
                        <div class="p-3 bg-orange-100 dark:bg-orange-800 rounded-full">
                            <svg class="w-6 h-6 text-orange-600 dark:text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity & Quick Actions -->
            <div class="grid auto-rows-min gap-4 md:grid-cols-2">
                <!-- Recent Transactions -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Transaksi Terbaru</h3>
                        <button @click="navigateToTransaksi" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 text-sm font-medium">
                            Lihat Semua
                        </button>
                    </div>
                    <div class="space-y-3">
                        <div v-for="(transaksi, idx) in recentTransaksi" :key="idx" class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ transaksi.brand }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ formatTanggal(transaksi.tanggal) }}</p>
                                </div>
                            </div>
                            <span class="font-semibold text-green-600 dark:text-green-400">{{ formatRupiah(transaksi.nominal) }}</span>
                        </div>
                        <div v-if="recentTransaksi.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <p>Belum ada transaksi</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Aksi Cepat</h3>
                    <div class="grid grid-cols-2 gap-3">
                        <button @click="navigateToBrand" class="p-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 transform hover:scale-105 shadow-lg">
                            <div class="flex items-center gap-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                <div>
                                    <p class="font-medium">Tambah Brand</p>
                                    <p class="text-xs opacity-80">Brand baru</p>
                                </div>
                            </div>
                        </button>

                        <button @click="navigateToTransaksi" class="p-4 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700 transition-all duration-200 transform hover:scale-105 shadow-lg">
                            <div class="flex items-center gap-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                <div>
                                    <p class="font-medium">Tambah Transaksi</p>
                                    <p class="text-xs opacity-80">Input baru</p>
                                </div>
                            </div>
                        </button>

                        <button @click="importCSV" class="p-4 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg hover:from-purple-600 hover:to-purple-700 transition-all duration-200 transform hover:scale-105 shadow-lg">
                            <div class="flex items-center gap-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                                </svg>
                                <div>
                                    <p class="font-medium">Import CSV</p>
                                    <p class="text-xs opacity-80">Upload file</p>
                                </div>
                            </div>
                        </button>

                        <button @click="generateReport" class="p-4 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all duration-200 transform hover:scale-105 shadow-lg">
                            <div class="flex items-center gap-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <div>
                                    <p class="font-medium">Laporan</p>
                                    <p class="text-xs opacity-80">Generate</p>
                                </div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Revenue Chart -->
            <RevenueChart />

            <!-- Brand Overview -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Brand Terdaftar</h3>
                    <button @click="navigateToBrand" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 text-sm font-medium">
                        Kelola Brand
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <div v-for="(brand, idx) in daftarBrand" :key="idx" class="p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-3">
                            <img v-if="brand.logoUrl" :src="brand.logoUrl" alt="Logo" class="w-10 h-10 rounded-lg object-cover" />
                            <div v-else class="w-10 h-10 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ brand.namaBrand }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ brand.namaCV }}</p>
                            </div>
                        </div>
                    </div>
                    <div v-if="daftarBrand.length === 0" class="col-span-full text-center py-8 text-gray-500 dark:text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <p>Belum ada brand terdaftar</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
