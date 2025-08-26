<template>
  <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-lg mx-4 transform transition-all duration-300">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">{{ isEdit ? 'Edit Transaksi' : 'Tambah Transaksi Baru' }}</h2>
        <button @click="$emit('close')" class="p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
          <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
      
      <form @submit.prevent="handleSubmit" class="space-y-6">
        <!-- Tanggal -->
        <div class="space-y-2">
          <label for="tanggal" class="block text-sm font-semibold text-gray-700">Tanggal</label>
          <input 
            type="date" 
            id="tanggal" 
            v-model="form.tanggal" 
            required 
            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
          />
        </div>

        <!-- Brand -->
        <div class="space-y-2">
          <label for="brand" class="block text-sm font-semibold text-gray-700">Brand</label>
          <select 
            id="brand" 
            v-model="form.brand" 
            required 
            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
          >
            <option value="">Pilih Brand</option>
            <option v-for="brand in brandList" :key="brand.namaBrand" :value="brand.namaBrand">
              {{ brand.namaBrand }} - {{ brand.namaCV }}
            </option>
          </select>
        </div>

        <!-- Nominal Transaksi -->
        <div class="space-y-2">
          <label for="nominal" class="block text-sm font-semibold text-gray-700">Nominal Transaksi</label>
          <div class="relative">
            <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">Rp</span>
            <input 
              type="number" 
              id="nominal" 
              v-model.number="form.nominal" 
              required 
              class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
              placeholder="0"
              min="0"
              step="1000"
            />
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-3 pt-4">
          <button 
            type="button" 
            @click="$emit('close')" 
            class="flex-1 px-6 py-3 rounded-xl border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition-all duration-200"
          >
            Batal
          </button>
          <button 
            type="submit" 
            class="flex-1 px-6 py-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium hover:from-blue-700 hover:to-indigo-700 shadow-lg transform hover:scale-105 transition-all duration-200"
          >
            {{ isEdit ? 'Update' : 'Simpan' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, defineProps, defineEmits } from 'vue';

const props = defineProps<{ 
  open: boolean; 
  isEdit?: boolean; 
  transaksi?: { tanggal: string; brand: string; nominal: number };
  brandList: Array<{ namaBrand: string; namaCV: string }>;
}>();

const emit = defineEmits(['submit', 'close']);

const form = ref({ 
  tanggal: new Date().toISOString().split('T')[0], 
  brand: '', 
  nominal: 0 
});

watch(() => props.transaksi, (val) => {
  if (val) form.value = { ...val };
  else form.value = { 
    tanggal: new Date().toISOString().split('T')[0], 
    brand: '', 
    nominal: 0 
  };
}, { immediate: true });

function handleSubmit() {
  // Ensure nominal is a number
  const submitData = {
    ...form.value,
    nominal: typeof form.value.nominal === 'string' ? parseFloat(form.value.nominal) : form.value.nominal
  };
  emit('submit', submitData);
  emit('close');
}
</script>
