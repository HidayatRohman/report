<template>
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Pendapatan Bulanan per Brand</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">Grafik perbandingan pendapatan setiap brand per bulan</p>
      </div>
      <div class="flex gap-2">
        <select v-model="selectedYear" @change="updateChart" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
          <option v-for="year in availableYears" :key="year" :value="year">{{ year }}</option>
        </select>
      </div>
    </div>
    
    <div class="h-96">
      <apexchart
        type="bar"
        height="100%"
        :options="chartOptions"
        :series="chartSeries"
      />
    </div>
    
    <!-- Summary Cards -->
    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
      <div v-for="(brand, index) in brandSummary" :key="brand.name" 
           class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
        <div class="flex items-center gap-3 mb-2">
          <div class="w-4 h-4 rounded" :style="{ backgroundColor: chartColors[index % chartColors.length] }"></div>
          <span class="font-medium text-gray-900 dark:text-white">{{ brand.name }}</span>
        </div>
        <div class="text-sm text-gray-600 dark:text-gray-300">
          <p>Total: {{ formatRupiah(brand.total) }}</p>
          <p>Rata-rata: {{ formatRupiah(brand.average) }}/bulan</p>
          <p>Transaksi: {{ brand.transactions }} kali</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';

interface TransactionData {
  tanggal: string;
  brand: string;
  nominal: number;
}

const selectedYear = ref(new Date().getFullYear());
const daftarTransaksi = ref<TransactionData[]>([]);

// Chart colors for different brands
const chartColors = [
  '#3B82F6', // Blue
  '#10B981', // Green
  '#F59E0B', // Yellow
  '#EF4444', // Red
  '#8B5CF6', // Purple
  '#06B6D4', // Cyan
  '#F97316', // Orange
  '#84CC16', // Lime
];

// Month names in Indonesian
const monthNames = [
  'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
  'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'
];

// Available years computed from transaction data
const availableYears = computed(() => {
  const years = new Set<number>();
  daftarTransaksi.value.forEach(transaction => {
    const year = new Date(transaction.tanggal).getFullYear();
    years.add(year);
  });
  const yearArray = Array.from(years).sort((a, b) => b - a);
  return yearArray.length > 0 ? yearArray : [new Date().getFullYear()];
});

// Brand summary statistics
const brandSummary = computed(() => {
  const summary = new Map<string, { total: number; transactions: number }>();
  
  daftarTransaksi.value
    .filter(transaction => new Date(transaction.tanggal).getFullYear() === selectedYear.value)
    .forEach(transaction => {
      const existing = summary.get(transaction.brand) || { total: 0, transactions: 0 };
      const nominal = typeof transaction.nominal === 'string' ? parseFloat(transaction.nominal) : transaction.nominal;
      summary.set(transaction.brand, {
        total: existing.total + (isNaN(nominal) ? 0 : nominal),
        transactions: existing.transactions + 1
      });
    });
  
  return Array.from(summary.entries()).map(([name, data]) => ({
    name,
    total: data.total,
    average: data.total / 12, // Average per month
    transactions: data.transactions
  }));
});

// Chart series data
const chartSeries = computed(() => {
  const brands = Array.from(new Set(daftarTransaksi.value.map(t => t.brand)));
  
  return brands.map((brand, index) => {
    const monthlyData = new Array(12).fill(0);
    
    daftarTransaksi.value
      .filter(transaction => 
        transaction.brand === brand && 
        new Date(transaction.tanggal).getFullYear() === selectedYear.value
      )
      .forEach(transaction => {
        const month = new Date(transaction.tanggal).getMonth();
        const nominal = typeof transaction.nominal === 'string' ? parseFloat(transaction.nominal) : transaction.nominal;
        monthlyData[month] += isNaN(nominal) ? 0 : nominal;
      });
    
    return {
      name: brand,
      data: monthlyData,
      color: chartColors[index % chartColors.length]
    };
  });
});

// Chart options
const chartOptions = computed(() => ({
  chart: {
    type: 'bar',
    height: '100%',
    toolbar: {
      show: true,
      tools: {
        download: true,
        selection: false,
        zoom: false,
        zoomin: false,
        zoomout: false,
        pan: false,
        reset: false
      }
    },
    background: 'transparent'
  },
  plotOptions: {
    bar: {
      horizontal: false,
      columnWidth: '55%',
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
    categories: monthNames,
    labels: {
      style: {
        colors: document.documentElement.classList.contains('dark') ? '#E5E7EB' : '#374151'
      }
    }
  },
  yaxis: {
    title: {
      text: 'Pendapatan (Rupiah)',
      style: {
        color: document.documentElement.classList.contains('dark') ? '#E5E7EB' : '#374151'
      }
    },
    labels: {
      style: {
        colors: document.documentElement.classList.contains('dark') ? '#E5E7EB' : '#374151'
      },
      formatter: function (value: number) {
        return formatRupiah(value);
      }
    }
  },
  fill: {
    opacity: 0.8
  },
  tooltip: {
    theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
    y: {
      formatter: function (val: number) {
        return formatRupiah(val);
      }
    }
  },
  legend: {
    position: 'top',
    horizontalAlign: 'center',
    labels: {
      colors: document.documentElement.classList.contains('dark') ? '#E5E7EB' : '#374151'
    }
  },
  grid: {
    borderColor: document.documentElement.classList.contains('dark') ? '#374151' : '#E5E7EB',
    strokeDashArray: 3
  }
}));

onMounted(() => {
  loadTransactionData();
});

// Watch for year changes
watch(selectedYear, () => {
  updateChart();
});

function loadTransactionData() {
  // Load transaction data from localStorage
  const savedTransaksi = localStorage.getItem('daftarTransaksi');
  if (savedTransaksi) {
    try {
      daftarTransaksi.value = JSON.parse(savedTransaksi);
    } catch (e) {
      console.error('Error parsing saved transactions:', e);
    }
  }
  
  // Generate sample data if empty for demonstration
  if (daftarTransaksi.value.length === 0) {
    generateSampleData();
  }
}

function generateSampleData() {
  const brands = ['NYORE COFFEE', 'Nike', 'Adidas', 'Puma', 'Converse'];
  const currentYear = new Date().getFullYear();
  const sampleData: TransactionData[] = [];
  
  // Generate data for current year and previous year
  for (let year = currentYear - 1; year <= currentYear; year++) {
    for (let month = 0; month < 12; month++) {
      brands.forEach(brand => {
        // Generate 1-3 transactions per brand per month
        const transactionCount = Math.floor(Math.random() * 3) + 1;
        for (let i = 0; i < transactionCount; i++) {
          const day = Math.floor(Math.random() * 28) + 1;
          const date = new Date(year, month, day);
          const nominal = Math.floor(Math.random() * 500000) + 50000; // 50k - 550k
          
          sampleData.push({
            tanggal: date.toISOString().split('T')[0],
            brand,
            nominal
          });
        }
      });
    }
  }
  
  daftarTransaksi.value = sampleData;
  localStorage.setItem('daftarTransaksi', JSON.stringify(sampleData));
}

function updateChart() {
  // Chart will automatically update due to computed properties
}

function formatRupiah(amount: number): string {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(amount);
}
</script>
