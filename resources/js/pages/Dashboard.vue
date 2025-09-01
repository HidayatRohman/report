<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import MonthlyBrandRevenueChart from '@/components/MonthlyBrandRevenueChart.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';

interface Brand {
  id: number;
  nama_brand: string;
  pemilik: string;
  logo_path?: string;
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
  userRole?: string;
  canEdit?: boolean;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

// Helper function to build correct image URL
function buildImageUrl(logoPath: string): string {
  // Clean the path - remove any leading slashes or 'storage/' prefix
  let cleanPath = logoPath;
  if (cleanPath.startsWith('/storage/')) {
    cleanPath = cleanPath.substring(9); // Remove '/storage/'
  } else if (cleanPath.startsWith('storage/')) {
    cleanPath = cleanPath.substring(8); // Remove 'storage/'
  } else if (cleanPath.startsWith('/')) {
    cleanPath = cleanPath.substring(1); // Remove leading '/'
  }
  
  // Build the full URL
  const baseUrl = window.location.origin;
  const fullUrl = `${baseUrl}/storage/${cleanPath}`;
  
  console.log('Building image URL:', { 
    original: logoPath, 
    cleaned: cleanPath, 
    final: fullUrl 
  });
  
  return fullUrl;
}

// Data refs - initialize with props data
const daftarBrand = ref<Array<{ 
  namaBrand: string; 
  namaCV: string; 
  logoUrl: string | null;
  imageLoaded?: boolean;
  imageError?: boolean;
}>>(
  (props.brands || []).map(brand => {
    console.log('Processing brand:', brand);
    return {
      namaBrand: brand.nama_brand,
      namaCV: brand.pemilik,
      logoUrl: brand.logo_path ? buildImageUrl(brand.logo_path) : null,
      imageLoaded: false,
      imageError: false
    };
  })
);

// Image handling functions
function handleImageError(event: Event) {
  const img = event.target as HTMLImageElement;
  console.error('Image failed to load:', {
    src: img.src,
    naturalWidth: img.naturalWidth,
    naturalHeight: img.naturalHeight,
    complete: img.complete
  });
  
  // Note: Error state is handled by reactive data in template
}

function handleImageLoad(event: Event) {
  const img = event.target as HTMLImageElement;
  console.log('Image loaded successfully:', {
    src: img.src,
    naturalWidth: img.naturalWidth,
    naturalHeight: img.naturalHeight
  });
}

const daftarTransaksi = ref<Array<{ tanggal: string; brand: string; nominal: number }>>(
  (props.transaksis || []).map(transaksi => {
    // Ensure date is in YYYY-MM-DD format
    let formattedDate = transaksi.tanggal;
    if (formattedDate.includes('T')) {
      formattedDate = formattedDate.split('T')[0];
    } else if (formattedDate.includes(' ')) {
      formattedDate = formattedDate.split(' ')[0];
    }
    
    return {
      tanggal: formattedDate,
      brand: transaksi.brand,
      nominal: typeof transaksi.nominal === 'string' ? parseFloat(transaksi.nominal) : transaksi.nominal
    };
  })
);

// Filter refs
const selectedBrand = ref('all');
const startDate = ref('');
const endDate = ref('');

// Computed properties
const totalBrand = computed(() => daftarBrand.value.length);
const totalTransaksi = computed(() => daftarTransaksi.value.length);
const totalNominal = computed(() => {
  return daftarTransaksi.value.reduce((total, item) => {
    const nominal = typeof item.nominal === 'string' ? parseFloat(item.nominal) : item.nominal;
    return total + (isNaN(nominal) ? 0 : nominal);
  }, 0);
});

const transaksiHariIni = computed(() => {
  const today = new Date().toISOString().split('T')[0];
  return daftarTransaksi.value
    .filter(item => item.tanggal === today)
    .reduce((total, item) => {
      const nominal = typeof item.nominal === 'string' ? parseFloat(item.nominal) : item.nominal;
      return total + (isNaN(nominal) ? 0 : nominal);
    }, 0);
});

const countTransaksiHariIni = computed(() => {
  const today = new Date().toISOString().split('T')[0];
  return daftarTransaksi.value.filter(item => item.tanggal === today).length;
});

const recentTransaksi = computed(() => {
  return filteredTransaksi.value
    .sort((a, b) => new Date(b.tanggal).getTime() - new Date(a.tanggal).getTime())
    .slice(0, 5);
});

// Filtered data computed property
const filteredTransaksi = computed(() => {
  let filtered = daftarTransaksi.value;

  // Filter by brand
  if (selectedBrand.value !== 'all') {
    filtered = filtered.filter(item => item.brand === selectedBrand.value);
  }

  // Filter by date range
  if (startDate.value) {
    filtered = filtered.filter(item => item.tanggal >= startDate.value);
  }

  if (endDate.value) {
    filtered = filtered.filter(item => item.tanggal <= endDate.value);
  }

  return filtered;
});

// Update computed properties to use filtered data
const filteredTotalTransaksi = computed(() => filteredTransaksi.value.length);
const filteredTotalNominal = computed(() => {
  return filteredTransaksi.value.reduce((total, item) => {
    const nominal = typeof item.nominal === 'string' ? parseFloat(item.nominal) : item.nominal;
    return total + (isNaN(nominal) ? 0 : nominal);
  }, 0);
});

// Load data from localStorage (fallback only)
onMounted(() => {
  // If no data from props, try localStorage as fallback
  if (daftarBrand.value.length === 0) {
    const savedBrands = localStorage.getItem('daftarBrand');
    if (savedBrands) {
      try {
        daftarBrand.value = JSON.parse(savedBrands);
      } catch (e) {
        console.error('Error parsing saved brands:', e);
      }
    }
  }

  if (daftarTransaksi.value.length === 0) {
    const savedTransaksi = localStorage.getItem('daftarTransaksi');
    if (savedTransaksi) {
      try {
        const parsedTransaksi = JSON.parse(savedTransaksi);
        // Normalize nominal values to numbers
        daftarTransaksi.value = parsedTransaksi.map((item: any) => ({
          ...item,
          nominal: typeof item.nominal === 'string' ? parseFloat(item.nominal) : item.nominal
        }));
      } catch (e) {
        console.error('Error parsing saved transactions:', e);
      }
    } else {
      // Generate sample data if no data exists
      generateSampleData();
    }
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

// Filter functions
function clearFilters() {
  selectedBrand.value = 'all';
  startDate.value = '';
  endDate.value = '';
}

function generateSampleData() {
  // Generate sample transactions for demo
  const sampleTransaksi = [
    { tanggal: '2025-01-25', brand: 'Nyore Nyante', nominal: 150000 },
    { tanggal: '2025-01-24', brand: 'Kemangi', nominal: 250000 },
    { tanggal: '2025-01-23', brand: 'Art Coffee', nominal: 180000 },
    { tanggal: new Date().toISOString().split('T')[0], brand: 'Nyore Nyante', nominal: 95000 },
    { tanggal: new Date().toISOString().split('T')[0], brand: 'Kemangi', nominal: 120000 },
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
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-2 sm:p-4">
            <!-- Welcome Section -->
            <div class="grid auto-rows-min gap-4 md:grid-cols-1">
                <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <div class="p-4 sm:p-6 lg:p-8 bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold mb-2">Selamat Datang di Dashboard</h1>
                                <p class="text-blue-100 text-sm sm:text-base">Kelola brand dan transaksi Anda dengan mudah</p>
                            </div>
                            <div class="hidden sm:block flex-shrink-0 ml-4">
                                <svg class="w-12 h-12 sm:w-14 sm:h-14 lg:w-16 lg:h-16 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 00-2 2h2a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 sm:p-6 mb-6">
                <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-end justify-between">
                    <div class="flex-1 w-full">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Filter Transaksi</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <!-- Brand Filter -->
                            <div>
                                <label for="brand-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <span class="hidden sm:inline">Brand</span>
                                    <span class="sm:hidden flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                        Brand
                                    </span>
                                </label>
                                <select 
                                    id="brand-filter"
                                    v-model="selectedBrand" 
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm"
                                >
                                    <option value="all">Semua Brand</option>
                                    <option v-for="brand in daftarBrand" :key="brand.namaBrand" :value="brand.namaBrand">
                                        {{ brand.namaBrand }}
                                    </option>
                                </select>
                            </div>

                            <!-- Start Date Filter -->
                            <div>
                                <label for="start-date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <span class="hidden sm:inline">Tanggal Mulai</span>
                                    <span class="sm:hidden flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        Mulai
                                    </span>
                                </label>
                                <input 
                                    type="date" 
                                    id="start-date"
                                    v-model="startDate"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm"
                                />
                            </div>

                            <!-- End Date Filter -->
                            <div>
                                <label for="end-date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <span class="hidden sm:inline">Tanggal Selesai</span>
                                    <span class="sm:hidden flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        Selesai
                                    </span>
                                </label>
                                <input 
                                    type="date" 
                                    id="end-date"
                                    v-model="endDate"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Clear Filter Button -->
                    <div class="flex gap-2 w-full lg:w-auto">
                        <button 
                            @click="clearFilters"
                            class="w-full lg:w-auto px-3 sm:px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors duration-200 flex items-center justify-center gap-2 text-sm"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            <span class="hidden sm:inline">Reset Filter</span>
                            <span class="sm:hidden">Reset</span>
                        </button>
                    </div>
                </div>

                <!-- Filter Results Info -->
                <div v-if="selectedBrand !== 'all' || startDate || endDate" class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                    <div class="flex items-center gap-2 text-blue-800 dark:text-blue-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm font-medium">
                            Menampilkan {{ filteredTotalTransaksi }} dari {{ totalTransaksi }} transaksi
                            <span v-if="selectedBrand !== 'all'"> untuk brand "{{ selectedBrand }}"</span>
                            <span v-if="startDate || endDate">
                                <span v-if="startDate && endDate"> dari {{ formatTanggal(startDate) }} sampai {{ formatTanggal(endDate) }}</span>
                                <span v-else-if="startDate"> dari {{ formatTanggal(startDate) }}</span>
                                <span v-else-if="endDate"> sampai {{ formatTanggal(endDate) }}</span>
                            </span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="grid auto-rows-min gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 sm:p-6 border-l-4 border-blue-500">
                    <div class="flex items-center">
                        <div class="flex-1 min-w-0 pr-3">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Total Brand</p>
                            <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ totalBrand }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Brand terdaftar</p>
                        </div>
                        <div class="p-2 sm:p-3 bg-blue-100 dark:bg-blue-800 rounded-full flex-shrink-0">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 sm:p-6 border-l-4 border-green-500">
                    <div class="flex items-center">
                        <div class="flex-1 min-w-0 pr-3">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">
                                {{ selectedBrand !== 'all' || startDate || endDate ? 'Transaksi (Filtered)' : 'Total Transaksi' }}
                            </p>
                            <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ filteredTotalTransaksi }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ selectedBrand !== 'all' || startDate || endDate ? 'Hasil filter' : 'Semua transaksi' }}
                            </p>
                        </div>
                        <div class="p-2 sm:p-3 bg-green-100 dark:bg-green-800 rounded-full flex-shrink-0">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 sm:p-6 border-l-4 border-purple-500">
                    <div class="flex items-center">
                        <div class="flex-1 min-w-0 pr-3">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">
                                {{ selectedBrand !== 'all' || startDate || endDate ? 'Total Nominal (Filtered)' : 'Total Nominal' }}
                            </p>
                            <p class="text-base sm:text-lg lg:text-xl font-bold text-gray-900 dark:text-white break-words">{{ formatRupiah(filteredTotalNominal) }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ selectedBrand !== 'all' || startDate || endDate ? 'Hasil filter' : 'Keseluruhan' }}
                            </p>
                        </div>
                        <div class="p-2 sm:p-3 bg-purple-100 dark:bg-purple-800 rounded-full flex-shrink-0">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600 dark:text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-l-4 border-orange-500">
                    <div class="flex items-center">
                        <div class="flex-1 min-w-0 pr-3">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Transaksi Hari Ini</p>
                            <p class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 dark:text-white break-all">{{ formatRupiah(transaksiHariIni) }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ countTransaksiHariIni }} transaksi hari ini</p>
                        </div>
                        <div class="p-3 bg-orange-100 dark:bg-orange-800 rounded-full flex-shrink-0">
                            <svg class="w-6 h-6 text-orange-600 dark:text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Brand Revenue Chart -->
            <MonthlyBrandRevenueChart 
                :transaksi-data="filteredTransaksi" 
                :brand-list="daftarBrand"
                :selected-brand="selectedBrand"
                :start-date="startDate"
                :end-date="endDate"
            />

            <!-- Recent Activity & Quick Actions -->
            <div class="grid auto-rows-min gap-4 lg:grid-cols-2">
                <!-- Recent Transactions -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Transaksi Terbaru</h3>
                        <button 
                            v-if="props.canEdit" 
                            @click="navigateToTransaksi" 
                            class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 text-sm font-medium"
                        >
                            Lihat Semua
                        </button>
                        <span 
                            v-else 
                            class="text-gray-500 dark:text-gray-400 text-sm"
                        >
                            5 Terakhir
                        </span>
                    </div>
                    <div class="space-y-3">
                        <div v-for="(transaksi, idx) in recentTransaksi" :key="idx" class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center gap-3 min-w-0 flex-1">
                                <div class="w-2 h-2 bg-green-500 rounded-full flex-shrink-0"></div>
                                <div class="min-w-0 flex-1">
                                    <p class="font-medium text-gray-900 dark:text-white truncate">{{ transaksi.brand }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ formatTanggal(transaksi.tanggal) }}</p>
                                </div>
                            </div>
                            <span class="font-semibold text-green-600 dark:text-green-400 text-sm sm:text-base whitespace-nowrap ml-2">{{ formatRupiah(transaksi.nominal) }}</span>
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
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 sm:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        {{ props.canEdit ? 'Aksi Cepat' : 'Informasi' }}
                    </h3>
                    
                    <!-- Actions for users who can edit -->
                    <div v-if="props.canEdit" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <button @click="navigateToBrand" class="p-3 sm:p-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 transform hover:scale-105 shadow-lg">
                            <div class="flex items-center gap-2 sm:gap-3">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                <div class="text-left">
                                    <p class="font-medium text-sm sm:text-base">Tambah Brand</p>
                                    <p class="text-xs opacity-80">Brand baru</p>
                                </div>
                            </div>
                        </button>

                        <button @click="navigateToTransaksi" class="p-3 sm:p-4 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700 transition-all duration-200 transform hover:scale-105 shadow-lg">
                            <div class="flex items-center gap-2 sm:gap-3">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                <div class="text-left">
                                    <p class="font-medium text-sm sm:text-base">Tambah Transaksi</p>
                                    <p class="text-xs opacity-80">Input baru</p>
                                </div>
                            </div>
                        </button>

                        <button @click="importCSV" class="p-3 sm:p-4 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg hover:from-purple-600 hover:to-purple-700 transition-all duration-200 transform hover:scale-105 shadow-lg">
                            <div class="flex items-center gap-2 sm:gap-3">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                                </svg>
                                <div class="text-left">
                                    <p class="font-medium text-sm sm:text-base">Import CSV</p>
                                    <p class="text-xs opacity-80">Upload file</p>
                                </div>
                            </div>
                        </button>

                        <button @click="generateReport" class="p-3 sm:p-4 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all duration-200 transform hover:scale-105 shadow-lg">
                            <div class="flex items-center gap-2 sm:gap-3">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <div class="text-left">
                                    <p class="font-medium text-sm sm:text-base">Laporan</p>
                                    <p class="text-xs opacity-80">Generate</p>
                                </div>
                            </div>
                        </button>
                    </div>

                    <!-- Read-only message for Owner -->
                    <div v-else class="text-center py-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 dark:bg-blue-800 rounded-full mb-4">
                            <svg class="w-8 h-8 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Mode Read-Only</h4>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">
                            Anda memiliki akses untuk melihat semua data<br>
                            tanpa kemampuan untuk mengedit atau menghapus.
                        </p>
                        <div class="inline-flex items-center mt-3 px-3 py-1 bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-200 text-xs font-medium rounded-full">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Role: Owner
                        </div>
                    </div>
                </div>
            </div>

            <!-- Brand Overview -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Brand Terdaftar</h3>
                    <button 
                        v-if="props.canEdit" 
                        @click="navigateToBrand" 
                        class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 text-sm font-medium"
                    >
                        Kelola Brand
                    </button>
                    <span 
                        v-else 
                        class="text-gray-500 dark:text-gray-400 text-sm"
                    >
                        {{ daftarBrand.length }} Brand
                    </span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    <div v-for="(brand, idx) in daftarBrand" :key="idx" class="p-3 sm:p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-3">
                            <div class="relative w-8 h-8 sm:w-10 sm:h-10 flex-shrink-0">
                                <!-- Image with loading state -->
                                <div v-if="brand.logoUrl" class="w-full h-full">
                                    <img 
                                        :src="brand.logoUrl" 
                                        :alt="`Logo ${brand.namaBrand}`" 
                                        class="w-full h-full rounded-lg object-cover transition-opacity duration-200"
                                        :class="{ 'opacity-0': !brand.imageLoaded }"
                                        @load="brand.imageLoaded = true; handleImageLoad($event)"
                                        @error="brand.imageError = true; handleImageError($event)"
                                        v-show="!brand.imageError"
                                    />
                                    <!-- Loading placeholder -->
                                    <div 
                                        v-if="!brand.imageLoaded && !brand.imageError"
                                        class="absolute inset-0 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center animate-pulse"
                                    >
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <!-- Error fallback -->
                                    <div 
                                        v-if="brand.imageError"
                                        class="w-full h-full bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center"
                                    >
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                </div>
                                <!-- No logo fallback -->
                                <div 
                                    v-else 
                                    class="w-full h-full bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center"
                                >
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="font-medium text-gray-900 dark:text-white text-sm sm:text-base truncate">{{ brand.namaBrand }}</p>
                                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 truncate">{{ brand.namaCV }}</p>
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
