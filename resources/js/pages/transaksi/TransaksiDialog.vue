<template>
  <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-4 sm:p-6 md:p-8 w-full max-w-lg transform transition-all duration-300 max-h-[90vh] overflow-y-auto">
      <div class="flex items-center justify-between mb-4 sm:mb-6">
        <h2 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-800 dark:text-white">{{ isEdit ? 'Edit Transaksi' : 'Tambah Transaksi Baru' }}</h2>
        <button @click="$emit('close')" class="p-1 sm:p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors duration-200 flex-shrink-0">
          <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
      
      <form @submit.prevent="handleSubmit" class="space-y-4 sm:space-y-6">
        <!-- Tanggal -->
        <div class="space-y-2">
          <label for="tanggal" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Tanggal</label>
          <input 
            type="date" 
            id="tanggal" 
            v-model="form.tanggal" 
            required 
            class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm sm:text-base"
          />
        </div>

        <!-- Brand -->
        <div class="space-y-2">
          <label for="brand" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Brand</label>
          <div class="relative">
            <select 
              id="brand" 
              v-model="form.brand" 
              required 
              class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 appearance-none text-sm sm:text-base"
            >
              <option value="">Pilih Brand</option>
              <option v-for="brand in brandList" :key="brand.namaBrand" :value="brand.namaBrand">
                {{ brand.namaBrand }} - {{ brand.namaCV }}
              </option>
            </select>
            <!-- Dropdown arrow -->
            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
              <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
              </svg>
            </div>
          </div>
          
          <!-- Selected Brand Preview with Logo -->
          <div v-if="form.brand && selectedBrandData" class="mt-2 p-2 sm:p-3 bg-gray-50 dark:bg-gray-700 rounded-lg flex items-center gap-2 sm:gap-3">
            <div v-if="selectedBrandData.logoPath" class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg overflow-hidden bg-white dark:bg-gray-600 border border-gray-200 dark:border-gray-600 flex items-center justify-center flex-shrink-0">
              <img 
                :src="`/storage/${selectedBrandData.logoPath}`" 
                :alt="selectedBrandData.namaBrand"
                class="w-full h-full object-cover"
                @error="handleImageError"
              />
            </div>
            <div v-else class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center flex-shrink-0">
              <span class="text-white font-bold text-sm sm:text-lg">{{ selectedBrandData.namaBrand.charAt(0) }}</span>
            </div>
            <div class="min-w-0 flex-1">
              <p class="font-medium text-gray-900 dark:text-white text-sm sm:text-base truncate">{{ selectedBrandData.namaBrand }}</p>
              <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 truncate">{{ selectedBrandData.namaCV }}</p>
            </div>
          </div>
        </div>

        <!-- Nominal Transaksi -->
        <div class="space-y-2">
          <label for="nominal" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Nominal Transaksi</label>
          <div class="relative">
            <span class="absolute left-3 sm:left-4 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 font-medium text-sm sm:text-base">Rp</span>
            <input 
              type="number" 
              id="nominal" 
              v-model.number="form.nominal" 
              required 
              class="w-full pl-10 sm:pl-12 pr-3 sm:pr-4 py-2 sm:py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm sm:text-base placeholder-gray-500 dark:placeholder-gray-400"
              placeholder="0"
              min="0"
              step="1000"
            />
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 pt-4">
          <button 
            type="button" 
            @click="$emit('close')" 
            class="w-full sm:flex-1 px-4 sm:px-6 py-2 sm:py-3 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 font-medium hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200 text-sm sm:text-base"
          >
            Batal
          </button>
          <button 
            type="submit" 
            class="w-full sm:flex-1 px-4 sm:px-6 py-2 sm:py-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium hover:from-blue-700 hover:to-indigo-700 shadow-lg transform hover:scale-105 transition-all duration-200 text-sm sm:text-base"
          >
            {{ isEdit ? 'Update' : 'Simpan' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, computed, defineProps, defineEmits } from 'vue';

const props = defineProps<{ 
  open: boolean; 
  isEdit?: boolean; 
  transaksi?: { tanggal: string; brand: string; nominal: number };
  brandList: Array<{ namaBrand: string; namaCV: string; logoPath?: string }>;
}>();

const emit = defineEmits(['submit', 'close']);

const form = ref({ 
  tanggal: new Date().toISOString().split('T')[0], 
  brand: '', 
  nominal: 0 
});

watch(() => props.transaksi, (val) => {
  if (val) {
    // Format tanggal untuk input date (YYYY-MM-DD)
    let formattedDate = val.tanggal;
    
    // Handle different date formats
    if (formattedDate.includes('T')) {
      // ISO format: 2025-08-26T00:00:00.000Z
      formattedDate = formattedDate.split('T')[0];
    } else if (formattedDate.includes(' ')) {
      // SQL datetime format: 2025-08-26 00:00:00
      formattedDate = formattedDate.split(' ')[0];
    }
    
    form.value = { 
      ...val,
      tanggal: formattedDate
    };
  } else {
    form.value = { 
      tanggal: new Date().toISOString().split('T')[0], 
      brand: '', 
      nominal: 0 
    };
  }
}, { immediate: true });

// Computed property to get selected brand data
const selectedBrandData = computed(() => {
  if (!form.value.brand) return null;
  return props.brandList.find(brand => brand.namaBrand === form.value.brand);
});

function handleSubmit() {
  // Ensure nominal is a number
  const submitData = {
    ...form.value,
    nominal: typeof form.value.nominal === 'string' ? parseFloat(form.value.nominal) : form.value.nominal
  };
  emit('submit', submitData);
  emit('close');
}

function handleImageError(event: Event) {
  // Hide broken image
  const img = event.target as HTMLImageElement;
  img.style.display = 'none';
}
</script>
