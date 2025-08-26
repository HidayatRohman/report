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
    
    <div class="h-96 relative">
      <canvas ref="chartCanvas"></canvas>
    </div>
    
    <!-- Legend dan Summary -->
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
import { ref, onMounted, onUnmounted, computed } from 'vue';
import {
  Chart,
  CategoryScale,
  LinearScale,
  BarElement,
  LineElement,
  PointElement,
  Title,
  Tooltip,
  Legend,
  type ChartConfiguration
} from 'chart.js';

// Register Chart.js components
Chart.register(
  CategoryScale,
  LinearScale,
  BarElement,
  LineElement,
  PointElement,
  Title,
  Tooltip,
  Legend
);

interface TransactionData {
  tanggal: string;
  brand: string;
  nominal: number;
}

const chartCanvas = ref<HTMLCanvasElement>();
const chart = ref<Chart>();
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

// Available years computed from transaction data
const availableYears = computed(() => {
  const years = new Set<number>();
  daftarTransaksi.value.forEach(transaction => {
    const year = new Date(transaction.tanggal).getFullYear();
    years.add(year);
  });
  return Array.from(years).sort((a, b) => b - a);
});

// Brand summary statistics
const brandSummary = computed(() => {
  const summary = new Map<string, { total: number; transactions: number }>();
  
  daftarTransaksi.value
    .filter(transaction => new Date(transaction.tanggal).getFullYear() === selectedYear.value)
    .forEach(transaction => {
      const existing = summary.get(transaction.brand) || { total: 0, transactions: 0 };
      summary.set(transaction.brand, {
        total: existing.total + transaction.nominal,
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

// Month names in Indonesian
const monthNames = [
  'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
  'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'
];

onMounted(() => {
  loadTransactionData();
  initChart();
});

onUnmounted(() => {
  if (chart.value) {
    chart.value.destroy();
  }
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

function initChart() {
  if (!chartCanvas.value) return;
  
  const ctx = chartCanvas.value.getContext('2d');
  if (!ctx) return;
  
  const config: ChartConfiguration = {
    type: 'bar',
    data: {
      labels: monthNames,
      datasets: []
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        title: {
          display: false
        },
        legend: {
          position: 'top',
          labels: {
            color: document.documentElement.classList.contains('dark') ? '#E5E7EB' : '#374151',
            usePointStyle: true,
            pointStyle: 'rect'
          }
        },
        tooltip: {
          mode: 'index',
          intersect: false,
          callbacks: {
            label: (context) => {
              const label = context.dataset.label || '';
              const value = formatRupiah(context.parsed.y);
              return `${label}: ${value}`;
            }
          }
        }
      },
      scales: {
        x: {
          display: true,
          title: {
            display: true,
            text: 'Bulan',
            color: document.documentElement.classList.contains('dark') ? '#E5E7EB' : '#374151'
          },
          grid: {
            color: document.documentElement.classList.contains('dark') ? '#374151' : '#E5E7EB'
          },
          ticks: {
            color: document.documentElement.classList.contains('dark') ? '#E5E7EB' : '#374151'
          }
        },
        y: {
          display: true,
          title: {
            display: true,
            text: 'Pendapatan (Rupiah)',
            color: document.documentElement.classList.contains('dark') ? '#E5E7EB' : '#374151'
          },
          grid: {
            color: document.documentElement.classList.contains('dark') ? '#374151' : '#E5E7EB'
          },
          ticks: {
            color: document.documentElement.classList.contains('dark') ? '#E5E7EB' : '#374151',
            callback: function(value) {
              return formatRupiah(value as number);
            }
          }
        }
      },
      interaction: {
        mode: 'index',
        intersect: false,
      }
    }
  };
  
  chart.value = new Chart(ctx, config);
  updateChart();
}

function updateChart() {
  if (!chart.value) return;
  
  // Get unique brands
  const brands = Array.from(new Set(daftarTransaksi.value.map(t => t.brand)));
  
  // Process data for selected year
  const datasets = brands.map((brand, index) => {
    const monthlyData = new Array(12).fill(0);
    
    daftarTransaksi.value
      .filter(transaction => 
        transaction.brand === brand && 
        new Date(transaction.tanggal).getFullYear() === selectedYear.value
      )
      .forEach(transaction => {
        const month = new Date(transaction.tanggal).getMonth();
        monthlyData[month] += transaction.nominal;
      });
    
    return {
      label: brand,
      data: monthlyData,
      backgroundColor: chartColors[index % chartColors.length],
      borderColor: chartColors[index % chartColors.length],
      borderWidth: 1
    };
  });
  
  chart.value.data.datasets = datasets;
  chart.value.update();
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
