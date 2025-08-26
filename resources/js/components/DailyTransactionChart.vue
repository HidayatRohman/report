<template>
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 md:p-6 mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 gap-2">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Transaksi Harian</h3>
      <div class="flex items-center gap-2">
        <select 
          v-model="selectedBrand" 
          @change="updateChart"
          class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
        >
          <option value="">Semua Brand</option>
          <option v-for="brand in uniqueBrands" :key="brand" :value="brand">{{ brand }}</option>
        </select>
        <select 
          v-model="selectedPeriod" 
          @change="updateChart"
          class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
        >
          <option value="7">7 Hari Terakhir</option>
          <option value="15">15 Hari Terakhir</option>
          <option value="30">30 Hari Terakhir</option>
        </select>
      </div>
    </div>
    
    <!-- Chart Container -->
    <div class="relative">
      <div ref="chartContainer" class="w-full h-64 md:h-80"></div>
      <div v-if="!hasData" class="absolute inset-0 flex items-center justify-center text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/50 rounded-lg border dark:border-gray-600">
        <div class="text-center">
          <svg class="w-12 h-12 mx-auto mb-3 opacity-60 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 00-2 2h2a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
          </svg>
          <p class="text-sm font-medium">Tidak ada data transaksi</p>
        </div>
      </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-4">
      <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3 border dark:border-blue-800/30">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-blue-700 dark:text-blue-300 font-medium">Rata-rata Harian</p>
            <p class="text-sm font-bold text-blue-900 dark:text-blue-100">{{ formatRupiah(averageDaily) }}</p>
          </div>
          <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
          </svg>
        </div>
      </div>
      
      <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-3 border dark:border-green-800/30">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-green-700 dark:text-green-300 font-medium">Nominal Tertinggi</p>
            <p class="text-sm font-bold text-green-900 dark:text-green-100">{{ formatRupiah(maxDaily.amount) }}</p>
            <p class="text-xs text-green-600 dark:text-green-400">{{ maxDaily.date }}</p>
          </div>
          <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 00-2 2h2a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
          </svg>
        </div>
      </div>
      
      <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-3 border dark:border-purple-800/30">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-purple-700 dark:text-purple-300 font-medium">Total Periode</p>
            <p class="text-sm font-bold text-purple-900 dark:text-purple-100">{{ formatRupiah(totalPeriod) }}</p>
          </div>
          <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
          </svg>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch, nextTick } from 'vue'
import ApexCharts from 'apexcharts'

interface Transaction {
  id: number;
  tanggal: string;
  brand: string;
  nominal: number;
}

const props = defineProps<{
  transaksis: Transaction[]
}>()

// Reactive data
const selectedPeriod = ref<number>(30)
const selectedBrand = ref<string>('')
const chartContainer = ref<HTMLElement>()
let chart: ApexCharts | null = null

// Computed properties
const uniqueBrands = computed(() => {
  const brands = [...new Set(props.transaksis.map(t => t.brand))]
  return brands.sort()
})

const filteredTransactions = computed(() => {
  const now = new Date()
  const periodDays = selectedPeriod.value
  const startDate = new Date(now.getTime() - (periodDays - 1) * 24 * 60 * 60 * 1000)
  
  return props.transaksis.filter(transaction => {
    // Handle date format - could be YYYY-MM-DD or ISO format
    let dateStr = transaction.tanggal
    if (dateStr.includes('T')) {
      dateStr = dateStr.split('T')[0] // Extract date part from ISO format
    }
    
    const transactionDate = new Date(dateStr)
    const isWithinPeriod = transactionDate >= startDate && transactionDate <= now
    const isBrandMatch = !selectedBrand.value || transaction.brand === selectedBrand.value
    
    return isWithinPeriod && isBrandMatch
  })
})

const dailyData = computed(() => {
  const data: { [key: string]: number } = {}
  
  // Initialize all dates in the period with 0
  const now = new Date()
  const periodDays = selectedPeriod.value
  
  for (let i = periodDays - 1; i >= 0; i--) {
    const date = new Date(now.getTime() - i * 24 * 60 * 60 * 1000)
    const dateStr = date.toISOString().split('T')[0]
    data[dateStr] = 0
  }
  
  // Sum transactions by date
  filteredTransactions.value.forEach(transaction => {
    // Handle date format - could be YYYY-MM-DD or ISO format
    let dateStr = transaction.tanggal
    if (dateStr.includes('T')) {
      dateStr = dateStr.split('T')[0] // Extract date part from ISO format
    }
    
    if (data.hasOwnProperty(dateStr)) {
      data[dateStr] += transaction.nominal
    }
  })
  
  return data
})

const chartData = computed(() => {
  const sortedDates = Object.keys(dailyData.value).sort()
  
  return {
    categories: sortedDates.map(date => {
      const d = new Date(date)
      return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short' })
    }),
    series: sortedDates.map(date => dailyData.value[date])
  }
})

const hasData = computed(() => {
  return chartData.value.series.some(value => value > 0)
})

const averageDaily = computed(() => {
  const values = Object.values(dailyData.value)
  const total = values.reduce((sum, val) => sum + val, 0)
  return values.length > 0 ? total / values.length : 0
})

const maxDaily = computed(() => {
  const entries = Object.entries(dailyData.value)
  const maxEntry = entries.reduce((max, current) => 
    current[1] > max[1] ? current : max
  , ['', 0])
  
  return {
    date: maxEntry[0] ? new Date(maxEntry[0]).toLocaleDateString('id-ID', { 
      day: '2-digit', 
      month: 'short',
      year: 'numeric'
    }) : '-',
    amount: maxEntry[1]
  }
})

const totalPeriod = computed(() => {
  return Object.values(dailyData.value).reduce((sum, val) => sum + val, 0)
})

// Helper functions
const formatRupiah = (amount: number): string => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(amount)
}

const getChartOptions = () => {
  // Detect if dark mode is active
  const isDarkMode = document.documentElement.classList.contains('dark')
  
  return {
    chart: {
      type: 'area',
      height: '100%',
      toolbar: {
        show: false
      },
      zoom: {
        enabled: false
      },
      background: 'transparent'
    },
    theme: {
      mode: isDarkMode ? 'dark' : 'light'
    },
    dataLabels: {
      enabled: false
    },
    stroke: {
      curve: 'smooth',
      width: 2
    },
    series: [{
      name: 'Transaksi',
      data: chartData.value.series
    }],
    xaxis: {
      categories: chartData.value.categories,
      labels: {
        style: {
          fontSize: '12px',
          colors: isDarkMode ? '#D1D5DB' : '#6B7280',
          fontFamily: 'Inter, system-ui, sans-serif'
        }
      },
      axisBorder: {
        color: isDarkMode ? '#374151' : '#E5E7EB'
      },
      axisTicks: {
        color: isDarkMode ? '#374151' : '#E5E7EB'
      }
    },
    yaxis: {
      labels: {
        style: {
          fontSize: '12px',
          colors: isDarkMode ? '#D1D5DB' : '#6B7280',
          fontFamily: 'Inter, system-ui, sans-serif'
        },
        formatter: (value: number) => {
          return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
            notation: 'compact'
          }).format(value)
        }
      }
    },
    fill: {
      type: 'gradient',
      gradient: {
        shadeIntensity: 1,
        opacityFrom: 0.7,
        opacityTo: 0.3,
        stops: [0, 100]
      }
    },
    colors: ['#3B82F6'],
    grid: {
      borderColor: isDarkMode ? '#374151' : '#E5E7EB',
      strokeDashArray: 3
    },
    tooltip: {
      theme: isDarkMode ? 'dark' : 'light',
      style: {
        fontSize: '12px',
        fontFamily: 'Inter, system-ui, sans-serif'
      },
      y: {
        formatter: (value: number) => formatRupiah(value)
      }
    },
    legend: {
      labels: {
        colors: isDarkMode ? '#D1D5DB' : '#6B7280'
      }
    },
    responsive: [{
      breakpoint: 768,
      options: {
        chart: {
          height: 250
        },
        xaxis: {
          labels: {
            rotate: -45,
            style: {
              fontSize: '11px',
              colors: isDarkMode ? '#D1D5DB' : '#6B7280'
            }
          }
        }
      }
    }]
  }
}

const renderChart = async () => {
  if (!chartContainer.value) {
    return
  }
  
  await nextTick()
  
  if (chart) {
    chart.destroy()
    chart = null
  }
  
  // Always render chart, even with zero data to show empty state
  chart = new ApexCharts(chartContainer.value, getChartOptions())
  chart.render()
}

const updateChart = async () => {
  if (chart) {
    await chart.updateOptions(getChartOptions())
  } else {
    await renderChart()
  }
}

// Lifecycle hooks
onMounted(() => {
  renderChart()
  
  // Watch for theme changes
  const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
        updateChart()
      }
    })
  })
  
  observer.observe(document.documentElement, {
    attributes: true,
    attributeFilter: ['class']
  })
})

watch([selectedPeriod, selectedBrand], () => {
  updateChart()
})

watch(() => props.transaksis, () => {
  updateChart()
}, { deep: true })
</script>
