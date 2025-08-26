<template>
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
          {{ chartTitle }}
        </h3>
        <p v-if="isFiltered" class="text-sm text-blue-600 dark:text-blue-400 mt-1">
          {{ filterDescription }}
        </p>
      </div>
      <div class="flex items-center gap-2">
        <select 
          v-model="viewType" 
          @change="updateChart"
          class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
        >
          <option value="period">Berdasarkan Periode</option>
          <option value="year">Berdasarkan Tahun</option>
        </select>
        
        <select 
          v-if="viewType === 'period'"
          v-model="selectedPeriod" 
          @change="updateChart"
          class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
        >
          <option value="3">3 Bulan Terakhir</option>
          <option value="6">6 Bulan Terakhir</option>
          <option value="12">12 Bulan Terakhir</option>
        </select>
        
        <select 
          v-if="viewType === 'year'"
          v-model="selectedYear" 
          @change="updateChart"
          class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
        >
          <option v-for="year in availableYears" :key="year" :value="year">{{ year }}</option>
        </select>
      </div>
    </div>
    
    <!-- Chart Container -->
    <div class="relative">
      <div v-if="hasData">
        <div ref="chartContainer" class="w-full h-80"></div>
      </div>
      <div v-else class="h-80 flex items-center justify-center">
        <div class="text-center text-gray-500 dark:text-gray-400">
          <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 00-2 2h2a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
          </svg>
          <p>Tidak ada data transaksi</p>
          <p class="text-xs mt-1">Total {{ transaksiData.length }} transaksi ditemukan</p>
        </div>
      </div>
    </div>

    <!-- Legend & Summary -->
    <div v-if="hasData" class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div v-for="brand in brandSummary" :key="brand.name" class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
          <div class="flex items-center gap-3">
            <div 
              class="w-4 h-4 rounded-full" 
              :style="{ backgroundColor: brand.color }"
            ></div>
            <div>
              <p class="font-medium text-gray-900 dark:text-white text-sm">{{ brand.name }}</p>
              <p class="text-xs text-gray-500 dark:text-gray-400">{{ brand.transactionCount }} transaksi</p>
            </div>
          </div>
          <span class="font-semibold text-gray-900 dark:text-white text-sm">{{ formatRupiah(brand.total) }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch, nextTick } from 'vue';

interface Brand {
  namaBrand: string;
  namaCV: string;
  logoUrl: string | null;
}

interface TransaksiData {
  tanggal: string;
  brand: string;
  nominal: number;
}

interface ChartData {
  months: string[];
  brands: string[];
  data: { [key: string]: { [brand: string]: number } };
}

const props = defineProps<{
  transaksiData: TransaksiData[];
  brandList: Brand[];
  selectedBrand?: string;
  startDate?: string;
  endDate?: string;
}>();

const chartContainer = ref<HTMLElement>();
const selectedPeriod = ref(6);
const viewType = ref<'period' | 'year'>('period');
const selectedYear = ref<number>(new Date().getFullYear());
let chartInstance: any = null;

// Brand colors
const brandColors = [
  '#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6',
  '#06B6D4', '#F97316', '#84CC16', '#EC4899', '#6B7280'
];

// Available years from transaction data
const availableYears = computed(() => {
  if (!props.transaksiData.length) return [new Date().getFullYear()];
  
  const years = [...new Set(props.transaksiData.map(t => {
    const date = new Date(t.tanggal);
    return date.getFullYear();
  }))].sort((a, b) => b - a);
  
  return years;
});

const chartData = computed((): ChartData | null => {
  if (!props.transaksiData.length) return null;

  let filteredTransaksi = props.transaksiData;
  
  // Apply filtering based on view type
  if (viewType.value === 'period') {
    // Filter by months ago from current date
    const now = new Date();
    const monthsAgo = new Date(now.getFullYear(), now.getMonth() - selectedPeriod.value, 1);
    filteredTransaksi = props.transaksiData.filter(t => {
      const transaksiDate = new Date(t.tanggal);
      return transaksiDate >= monthsAgo && transaksiDate <= now;
    });
  } else {
    // Filter by selected year
    filteredTransaksi = props.transaksiData.filter(t => {
      const transaksiDate = new Date(t.tanggal);
      return transaksiDate.getFullYear() === selectedYear.value;
    });
  }

  // Group by month and brand
  const groupedData: { [key: string]: { [brand: string]: number } } = {};
  
  filteredTransaksi.forEach(t => {
    const date = new Date(t.tanggal);
    const monthKey = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
    
    if (!groupedData[monthKey]) {
      groupedData[monthKey] = {};
    }
    
    if (!groupedData[monthKey][t.brand]) {
      groupedData[monthKey][t.brand] = 0;
    }
    
    const nominal = typeof t.nominal === 'string' ? parseFloat(t.nominal) : t.nominal;
    groupedData[monthKey][t.brand] += isNaN(nominal) ? 0 : nominal;
  });

  // Convert to array format
  const months = Object.keys(groupedData).sort();
  const brands = [...new Set(filteredTransaksi.map(t => t.brand))];
  
  return {
    months,
    brands,
    data: groupedData
  };
});

const brandSummary = computed(() => {
  if (!props.transaksiData.length) return [];

  let filteredTransaksi = props.transaksiData;
  
  // Apply same filtering as chartData
  if (viewType.value === 'period') {
    const now = new Date();
    const monthsAgo = new Date(now.getFullYear(), now.getMonth() - selectedPeriod.value, 1);
    filteredTransaksi = props.transaksiData.filter(t => {
      const transaksiDate = new Date(t.tanggal);
      return transaksiDate >= monthsAgo && transaksiDate <= now;
    });
  } else {
    filteredTransaksi = props.transaksiData.filter(t => {
      const transaksiDate = new Date(t.tanggal);
      return transaksiDate.getFullYear() === selectedYear.value;
    });
  }

  const brandTotals: { [brand: string]: { total: number; count: number } } = {};
  
  filteredTransaksi.forEach(t => {
    if (!brandTotals[t.brand]) {
      brandTotals[t.brand] = { total: 0, count: 0 };
    }
    
    const nominal = typeof t.nominal === 'string' ? parseFloat(t.nominal) : t.nominal;
    brandTotals[t.brand].total += isNaN(nominal) ? 0 : nominal;
    brandTotals[t.brand].count += 1;
  });

  return Object.entries(brandTotals)
    .map(([brand, data], index) => ({
      name: brand,
      total: data.total,
      transactionCount: data.count,
      color: brandColors[index % brandColors.length]
    }))
    .sort((a, b) => b.total - a.total);
});

const hasData = computed(() => {
  return chartData.value && chartData.value.months.length > 0;
});

// Computed properties for filter information
const isFiltered = computed(() => {
  return props.selectedBrand && props.selectedBrand !== 'all' || props.startDate || props.endDate;
});

const chartTitle = computed(() => {
  if (isFiltered.value && props.selectedBrand && props.selectedBrand !== 'all') {
    return `Pendapatan Bulanan - ${props.selectedBrand}`;
  }
  return 'Pendapatan Bulanan per Brand';
});

const filterDescription = computed(() => {
  const filters = [];
  if (props.selectedBrand && props.selectedBrand !== 'all') {
    filters.push(`Brand: ${props.selectedBrand}`);
  }
  if (props.startDate && props.endDate) {
    filters.push(`Periode: ${props.startDate} - ${props.endDate}`);
  } else if (props.startDate) {
    filters.push(`Dari: ${props.startDate}`);
  } else if (props.endDate) {
    filters.push(`Sampai: ${props.endDate}`);
  }
  return filters.join(' | ');
});

const formatRupiah = (amount: number): string => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(amount);
};

const formatMonth = (monthKey: string): string => {
  const [year, month] = monthKey.split('-');
  const date = new Date(parseInt(year), parseInt(month) - 1);
  return date.toLocaleDateString('id-ID', { month: 'short', year: 'numeric' });
};

const renderChart = async () => {
  if (!chartContainer.value || !hasData.value) return;

  try {
    // Dynamic import ApexCharts
    const ApexCharts = (await import('apexcharts')).default;
    
    // Destroy existing chart
    if (chartInstance) {
      chartInstance.destroy();
    }

    const chartDataValue = chartData.value;
    if (!chartDataValue) return;

    const { months, brands, data } = chartDataValue;
    
    if (!months.length || !brands.length) return;

    // Prepare series data
    const series = brands.map((brand: string, index: number) => ({
      name: brand,
      data: months.map((month: string) => data[month]?.[brand] || 0),
      color: brandColors[index % brandColors.length]
    }));

    const options = {
      chart: {
        type: 'bar' as const,
        height: 320,
        toolbar: {
          show: false
        },
        background: 'transparent'
      },
      plotOptions: {
        bar: {
          horizontal: false,
          columnWidth: '60%',
          borderRadius: 4,
          dataLabels: {
            position: 'top'
          }
        }
      },
      dataLabels: {
        enabled: false
      },
      stroke: {
        show: true,
        width: 2,
        colors: ['transparent']
      },
      xaxis: {
        categories: months.map(formatMonth),
        labels: {
          style: {
            colors: '#6B7280',
            fontSize: '12px'
          }
        }
      },
      yaxis: {
        title: {
          text: 'Pendapatan (IDR)',
          style: {
            color: '#6B7280',
            fontSize: '12px'
          }
        },
        labels: {
          style: {
            colors: '#6B7280',
            fontSize: '12px'
          },
          formatter: (value: number) => {
            if (value >= 1000000000) {
              return (value / 1000000000).toFixed(1) + 'B';
            } else if (value >= 1000000) {
              return (value / 1000000).toFixed(1) + 'M';
            } else if (value >= 1000) {
              return (value / 1000).toFixed(1) + 'K';
            }
            return value.toString();
          }
        }
      },
      fill: {
        opacity: 0.8
      },
      tooltip: {
        theme: 'dark',
        y: {
          formatter: (value: number) => formatRupiah(value)
        }
      },
      legend: {
        show: false
      },
      grid: {
        borderColor: '#E5E7EB',
        strokeDashArray: 3
      },
      series
    };

    chartInstance = new ApexCharts(chartContainer.value, options);
    await chartInstance.render();
  } catch (error) {
    console.error('Error rendering chart:', error);
  }
};

const updateChart = async () => {
  if (chartInstance) {
    chartInstance.destroy();
    chartInstance = null;
  }
  await nextTick();
  renderChart();
};

watch([() => props.transaksiData, selectedPeriod, viewType, selectedYear], async () => {
  updateChart();
}, { deep: true });

onMounted(async () => {
  // Set initial selectedYear to the most recent year from data
  if (availableYears.value.length > 0) {
    selectedYear.value = availableYears.value[0];
  }
  await nextTick();
  renderChart();
});
</script>
