<template>
  <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-lg mx-4 transform transition-all duration-300">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">{{ isEdit ? 'Edit Brand' : 'Tambah Brand Baru' }}</h2>
        <button @click="$emit('close')" class="p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
          <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
      
      <form @submit.prevent="handleSubmit" class="space-y-6">
        <!-- Logo Upload -->
        <div class="space-y-2">
          <label for="logo" class="block text-sm font-semibold text-gray-700">Logo Brand</label>
          <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-blue-400 transition-colors duration-200">
            <input type="file" id="logo" name="logo" @change="handleFileUpload" accept="image/*" class="hidden" ref="fileInput" />
            <div v-if="!form.logo" @click="$refs.fileInput.click()" class="cursor-pointer">
              <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
              </svg>
              <p class="text-gray-600 font-medium">Klik untuk upload logo</p>
              <p class="text-gray-400 text-sm">PNG, JPG, JPEG (Max: 2MB)</p>
            </div>
            <div v-else class="space-y-3">
              <img v-if="previewUrl" :src="previewUrl" alt="Preview" class="mx-auto h-20 w-20 object-cover rounded-lg" />
              <p class="text-sm text-gray-600">{{ form.logo?.name }}</p>
              <button type="button" @click="removeFile" class="text-red-500 hover:text-red-700 text-sm font-medium">Hapus file</button>
            </div>
          </div>
        </div>

        <!-- Nama Brand -->
        <div class="space-y-2">
          <label for="namaBrand" class="block text-sm font-semibold text-gray-700">Nama Brand</label>
          <input 
            type="text" 
            id="namaBrand" 
            v-model="form.namaBrand" 
            required 
            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
            placeholder="Masukkan nama brand"
          />
        </div>

        <!-- Nama CV -->
        <div class="space-y-2">
          <label for="namaCV" class="block text-sm font-semibold text-gray-700">Nama CV</label>
          <input 
            type="text" 
            id="namaCV" 
            v-model="form.namaCV" 
            required 
            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
            placeholder="Masukkan nama CV"
          />
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
import { ref, watch, defineProps, defineEmits, computed } from 'vue';

const props = defineProps<{ 
  open: boolean; 
  isEdit?: boolean; 
  brand?: { namaBrand: string; namaCV: string; logo: File | null } 
}>();

const emit = defineEmits(['submit', 'close']);

const form = ref({ namaBrand: '', namaCV: '', logo: null as File | null });
const fileInput = ref<HTMLInputElement>();

const previewUrl = computed(() => {
  if (form.value.logo) {
    return URL.createObjectURL(form.value.logo);
  }
  return null;
});

watch(() => props.brand, (val) => {
  if (val) form.value = { ...val };
  else form.value = { namaBrand: '', namaCV: '', logo: null };
}, { immediate: true });

function handleFileUpload(event: Event) {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files.length > 0) {
    form.value.logo = target.files[0];
  }
}

function removeFile() {
  form.value.logo = null;
  if (fileInput.value) {
    fileInput.value.value = '';
  }
}

function handleSubmit() {
  emit('submit', { ...form.value });
  emit('close');
}
</script>
