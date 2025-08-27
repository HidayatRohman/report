<template>
  <AppLayout :breadcrumbs="[{ title: 'Daftar Transaksi', href: '/transaksi-list' }]">
    <div class="p-8">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Daftar Transaksi</h1>
        <div class="flex items-center gap-3">
          <!-- Filter Toggle Button -->
          <button 
            @click="showFilters = !showFilters" 
            class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200 flex items-center gap-2"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"/>
            </svg>
            Filter
            <svg 
              class="w-4 h-4 transition-transform duration-200" 
              :class="{ 'rotate-180': showFilters }" 
              fill="none" 
              stroke="currentColor" 
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </button>
          
          <!-- Add Transaction Button -->
          <button 
            @click="openDialog()" 
            class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors duration-200 flex items-center gap-2"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Tambah Transaksi
          </button>
        </div>
      </div>

      <!-- Filter Section (Collapsible) -->
      <div 
        v-show="showFilters" 
        class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6 transition-all duration-300"
        :class="{ 'animate-slideDown': showFilters, 'animate-slideUp': !showFilters }"
      >
        <div class="flex flex-col gap-6">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
              <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"/>
              </svg>
              Filter Transaksi
            </h3>
            
            <!-- Reset Filter Button -->
            <button 
              @click="resetFilter" 
              class="px-4 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors duration-200 flex items-center gap-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
              </svg>
              Reset Filter
            </button>
          </div>

          <!-- Filter Controls -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Brand Filter -->
            <div class="flex flex-col gap-2">
              <label for="brandFilter" class="text-sm font-medium text-gray-700 dark:text-gray-300">Brand:</label>
              <select 
                id="brandFilter" 
                v-model="selectedBrand" 
                @change="filterTransaksi"
                class="px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm"
              >
                <option value="">Semua Brand</option>
                <option v-for="brand in availableBrands" :key="brand" :value="brand">
                  {{ brand }}
                </option>
              </select>
            </div>

            <!-- Month Filter -->
            <div class="flex flex-col gap-2">
              <label for="monthFilter" class="text-sm font-medium text-gray-700 dark:text-gray-300">Bulan:</label>
              <select 
                id="monthFilter" 
                v-model="selectedMonth" 
                @change="filterTransaksi"
                class="px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm"
              >
                <option value="">Semua Bulan</option>
                <option v-for="month in monthOptions" :key="month.value" :value="month.value">
                  {{ month.label }}
                </option>
              </select>
            </div>

            <!-- Year Filter -->
            <div class="flex flex-col gap-2">
              <label for="yearFilter" class="text-sm font-medium text-gray-700 dark:text-gray-300">Tahun:</label>
              <select 
                id="yearFilter" 
                v-model="selectedYear" 
                @change="filterTransaksi"
                class="px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm"
              >
                <option value="">Semua Tahun</option>
                <option v-for="year in availableYears" :key="year" :value="year">
                  {{ year }}
                </option>
              </select>
            </div>

            <!-- Date Range Filter -->
            <div class="flex flex-col gap-2">
              <label for="dateRangeFilter" class="text-sm font-medium text-gray-700 dark:text-gray-300">Rentang Tanggal:</label>
              <select 
                id="dateRangeFilter" 
                v-model="selectedDateRange" 
                @change="applyDateRangeFilter"
                class="px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm"
              >
                <option value="">Pilih Rentang</option>
                <option value="today">Hari Ini</option>
                <option value="yesterday">Kemarin</option>
                <option value="this_week">Minggu Ini</option>
                <option value="last_week">Minggu Lalu</option>
                <option value="this_month">Bulan Ini</option>
                <option value="last_month">Bulan Lalu</option>
                <option value="last_3_months">3 Bulan Terakhir</option>
                <option value="this_year">Tahun Ini</option>
              </select>
            </div>
          </div>

          <!-- Custom Date Range -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex flex-col gap-2">
              <label for="startDate" class="text-sm font-medium text-gray-700 dark:text-gray-300">Dari Tanggal:</label>
              <input 
                id="startDate" 
                type="date" 
                v-model="startDate" 
                @change="filterTransaksi"
                class="px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm"
              />
            </div>
            <div class="flex flex-col gap-2">
              <label for="endDate" class="text-sm font-medium text-gray-700 dark:text-gray-300">Sampai Tanggal:</label>
              <input 
                id="endDate" 
                type="date" 
                v-model="endDate" 
                @change="filterTransaksi"
                class="px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm"
              />
            </div>
          </div>

          <!-- Filter Summary -->
          <div v-if="hasActiveFilters" class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
            <div class="flex items-start justify-between gap-4">
              <div>
                <p class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-2">Filter Aktif:</p>
                <div class="flex flex-wrap gap-2">
                  <span v-if="selectedBrand" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-200">
                    Brand: {{ selectedBrand }}
                  </span>
                  <span v-if="selectedMonth" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-200">
                    Bulan: {{ getMonthName(selectedMonth) }}
                  </span>
                  <span v-if="selectedYear" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-800 text-purple-800 dark:text-purple-200">
                    Tahun: {{ selectedYear }}
                  </span>
                  <span v-if="selectedDateRange" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 dark:bg-orange-800 text-orange-800 dark:text-orange-200">
                    {{ getDateRangeLabel(selectedDateRange) }}
                  </span>
                  <span v-if="startDate && endDate" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-800 text-red-800 dark:text-red-200">
                    {{ formatTanggal(startDate) }} - {{ formatTanggal(endDate) }}
                  </span>
                </div>
              </div>
              <div class="text-sm text-blue-700 dark:text-blue-300">
                <span class="font-medium">{{ filteredTransaksi.length }}</span> dari {{ daftarTransaksi.length }} transaksi
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Table Section -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
        <table class="w-full text-left border-collapse">
          <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
              <th class="px-6 py-4 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tanggal</th>
              <th class="px-6 py-4 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Brand</th>
              <th class="px-6 py-4 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nominal</th>
              <th class="px-6 py-4 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-for="(item, idx) in filteredTransaksi" :key="idx" class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ formatTanggal(item.tanggal) }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ item.brand }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ formatRupiah(item.nominal) }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                <button 
                  @click="editTransaksi(daftarTransaksi.findIndex(t => t === item))" 
                  class="px-3 py-1 rounded bg-yellow-500 text-white hover:bg-yellow-600 transition-colors duration-200"
                >
                  Edit
                </button>
                <button 
                  @click="deleteTransaksi(daftarTransaksi.findIndex(t => t === item))" 
                  class="px-3 py-1 rounded bg-red-500 text-white hover:bg-red-600 transition-colors duration-200"
                >
                  Hapus
                </button>
              </td>
            </tr>
          </tbody>
        </table>
        
        <!-- Empty State -->
        <div v-if="filteredTransaksi.length === 0" class="text-center py-12">
          <div class="text-gray-400 dark:text-gray-500 mb-2">
            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
          </div>
          <p class="text-gray-500 dark:text-gray-400">
            {{ selectedMonth || selectedYear ? 'Tidak ada transaksi untuk filter yang dipilih' : 'Belum ada data transaksi' }}
          </p>
          <button 
            v-if="selectedMonth || selectedYear" 
            @click="resetFilter" 
            class="mt-2 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm underline"
          >
            Reset filter
          </button>
        </div>
      </div>
      <TransaksiDialog 
        :open="dialogOpen" 
        :isEdit="editIdx !== null" 
        :transaksi="editIdx !== null ? daftarTransaksi[editIdx] : undefined"
        :brandList="brandList"
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
const editIdx = ref<number|null>(null);
const selectedMonth = ref<string>('');
const selectedYear = ref<string>('');
const selectedBrand = ref<string>('');
const selectedDateRange = ref<string>('');
const startDate = ref<string>('');
const endDate = ref<string>('');
const showFilters = ref<boolean>(false);

// Simulasi data brand
const brandList = ref([
  { namaBrand: 'Nike', namaCV: 'CV Sportindo' },
  { namaBrand: 'Adidas', namaCV: 'CV Atletik' },
  { namaBrand: 'Puma', namaCV: 'CV Dinamis' },
]);

// Month options
const monthOptions = ref([
  { value: '01', label: 'Januari' },
  { value: '02', label: 'Februari' },
  { value: '03', label: 'Maret' },
  { value: '04', label: 'April' },
  { value: '05', label: 'Mei' },
  { value: '06', label: 'Juni' },
  { value: '07', label: 'Juli' },
  { value: '08', label: 'Agustus' },
  { value: '09', label: 'September' },
  { value: '10', label: 'Oktober' },
  { value: '11', label: 'November' },
  { value: '12', label: 'Desember' },
]);

// Computed properties
const availableYears = computed(() => {
  const years = new Set<number>();
  daftarTransaksi.value.forEach(transaksi => {
    const year = new Date(transaksi.tanggal).getFullYear();
    years.add(year);
  });
  return Array.from(years).sort((a, b) => b - a);
});

const availableBrands = computed(() => {
  const brands = new Set<string>();
  daftarTransaksi.value.forEach(transaksi => {
    brands.add(transaksi.brand);
  });
  return Array.from(brands).sort();
});

const hasActiveFilters = computed(() => {
  return selectedMonth.value || selectedYear.value || selectedBrand.value || 
         selectedDateRange.value || (startDate.value && endDate.value);
});

const filteredTransaksi = computed(() => {
  let filtered = daftarTransaksi.value;

  // Filter by brand
  if (selectedBrand.value) {
    filtered = filtered.filter(transaksi => transaksi.brand === selectedBrand.value);
  }

  // Filter by month
  if (selectedMonth.value) {
    filtered = filtered.filter(transaksi => {
      const transaksiMonth = new Date(transaksi.tanggal).getMonth() + 1;
      return transaksiMonth.toString().padStart(2, '0') === selectedMonth.value;
    });
  }

  // Filter by year
  if (selectedYear.value) {
    filtered = filtered.filter(transaksi => {
      const transaksiYear = new Date(transaksi.tanggal).getFullYear();
      return transaksiYear.toString() === selectedYear.value;
    });
  }

  // Filter by custom date range
  if (startDate.value && endDate.value) {
    filtered = filtered.filter(transaksi => {
      const transaksiDate = new Date(transaksi.tanggal);
      const start = new Date(startDate.value);
      const end = new Date(endDate.value);
      return transaksiDate >= start && transaksiDate <= end;
    });
  }

  return filtered;
});

// Add some sample data for demonstration
onMounted(() => {
  daftarTransaksi.value = [
    { tanggal: '2025-01-15', brand: 'Nike', nominal: 1500000 },
    { tanggal: '2025-01-20', brand: 'Adidas', nominal: 2000000 },
    { tanggal: '2025-02-10', brand: 'Puma', nominal: 1200000 },
    { tanggal: '2025-02-25', brand: 'Nike', nominal: 1800000 },
    { tanggal: '2025-03-05', brand: 'Adidas', nominal: 2200000 },
    { tanggal: '2025-03-18', brand: 'Puma', nominal: 900000 },
    { tanggal: '2025-07-12', brand: 'Nike', nominal: 1600000 },
    { tanggal: '2025-08-01', brand: 'Adidas', nominal: 2400000 },
    { tanggal: '2025-08-15', brand: 'Puma', nominal: 1100000 },
    { tanggal: '2025-08-25', brand: 'Nike', nominal: 1900000 },
    { tanggal: '2025-08-27', brand: 'Adidas', nominal: 2100000 },
    { tanggal: '2024-11-20', brand: 'Nike', nominal: 1300000 },
    { tanggal: '2024-12-10', brand: 'Puma', nominal: 800000 },
    { tanggal: '2024-12-30', brand: 'Adidas', nominal: 2500000 },
  ];
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
  if (confirm('Yakin ingin menghapus transaksi ini?')) {
    daftarTransaksi.value.splice(idx, 1);
  }
}

function resetFilter() {
  selectedMonth.value = '';
  selectedYear.value = '';
  selectedBrand.value = '';
  selectedDateRange.value = '';
  startDate.value = '';
  endDate.value = '';
}

function filterTransaksi() {
  // This function is called when filters change
  // The computed property will automatically update
}

function applyDateRangeFilter() {
  if (!selectedDateRange.value) return;

  const today = new Date();
  let start: Date, end: Date;

  switch (selectedDateRange.value) {
    case 'today':
      start = new Date(today);
      end = new Date(today);
      break;
    case 'yesterday':
      start = new Date(today);
      start.setDate(start.getDate() - 1);
      end = new Date(start);
      break;
    case 'this_week':
      start = new Date(today);
      start.setDate(today.getDate() - today.getDay());
      end = new Date(today);
      break;
    case 'last_week':
      start = new Date(today);
      start.setDate(today.getDate() - today.getDay() - 7);
      end = new Date(start);
      end.setDate(end.getDate() + 6);
      break;
    case 'this_month':
      start = new Date(today.getFullYear(), today.getMonth(), 1);
      end = new Date(today);
      break;
    case 'last_month':
      start = new Date(today.getFullYear(), today.getMonth() - 1, 1);
      end = new Date(today.getFullYear(), today.getMonth(), 0);
      break;
    case 'last_3_months':
      start = new Date(today.getFullYear(), today.getMonth() - 3, 1);
      end = new Date(today);
      break;
    case 'this_year':
      start = new Date(today.getFullYear(), 0, 1);
      end = new Date(today);
      break;
    default:
      return;
  }

  startDate.value = start.toISOString().split('T')[0];
  endDate.value = end.toISOString().split('T')[0];
  
  // Clear other filters when using date range
  selectedMonth.value = '';
  selectedYear.value = '';
}

function getDateRangeLabel(range: string): string {
  const labels: Record<string, string> = {
    'today': 'Hari Ini',
    'yesterday': 'Kemarin',
    'this_week': 'Minggu Ini',
    'last_week': 'Minggu Lalu',
    'this_month': 'Bulan Ini',
    'last_month': 'Bulan Lalu',
    'last_3_months': '3 Bulan Terakhir',
    'this_year': 'Tahun Ini'
  };
  return labels[range] || range;
}

function getMonthName(monthValue: string): string {
  const month = monthOptions.value.find(m => m.value === monthValue);
  return month ? month.label : monthValue;
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
</script>

<style scoped>
@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slideUp {
  from {
    opacity: 1;
    transform: translateY(0);
  }
  to {
    opacity: 0;
    transform: translateY(-10px);
  }
}

.animate-slideDown {
  animation: slideDown 0.3s ease-out;
}

.animate-slideUp {
  animation: slideUp 0.3s ease-out;
}
</style>
