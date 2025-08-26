<template>
  <AppLayout :breadcrumbs="[{ title: 'Brand Management', href: '/brand-input' }]">
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 p-8">
      <!-- Header Section -->
      <div class="flex items-center justify-between mb-8">
        <div>
          <h1 class="text-3xl font-bold text-gray-800 mb-2">Brand Management</h1>
          <p class="text-gray-600">Kelola brand dan logo perusahaan Anda</p>
        </div>
        <button 
          @click="openDialog" 
          class="px-6 py-3 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium hover:from-blue-700 hover:to-indigo-700 shadow-lg transform hover:scale-105 transition-all duration-200 flex items-center gap-2"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
          </svg>
          Tambah Brand
        </button>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
          <div class="flex items-center">
            <div class="flex-1">
              <p class="text-sm font-medium text-gray-600">Total Brand</p>
              <p class="text-2xl font-bold text-gray-900">{{ daftarBrand.length }}</p>
            </div>
            <div class="p-3 bg-blue-100 rounded-full">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
              </svg>
            </div>
          </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
          <div class="flex items-center">
            <div class="flex-1">
              <p class="text-sm font-medium text-gray-600">Brand Aktif</p>
              <p class="text-2xl font-bold text-gray-900">{{ daftarBrand.length }}</p>
            </div>
            <div class="p-3 bg-green-100 rounded-full">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
          </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
          <div class="flex items-center">
            <div class="flex-1">
              <p class="text-sm font-medium text-gray-600">CV Terdaftar</p>
              <p class="text-2xl font-bold text-gray-900">{{ new Set(daftarBrand.map(b => b.namaCV)).size }}</p>
            </div>
            <div class="p-3 bg-purple-100 rounded-full">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Brand Cards -->
      <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b">
          <h2 class="text-lg font-semibold text-gray-800">Daftar Brand</h2>
        </div>
        <div v-if="daftarBrand.length === 0" class="p-12 text-center">
          <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
          </div>
          <p class="text-gray-500 text-lg">Belum ada brand yang ditambahkan</p>
          <p class="text-gray-400 text-sm mt-2">Klik tombol "Tambah Brand" untuk memulai</p>
        </div>
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
          <div v-for="(item, idx) in daftarBrand" :key="idx" class="bg-white border border-gray-200 rounded-xl p-6 hover:shadow-lg transition-all duration-200 hover:border-blue-300">
            <div class="flex items-center mb-4">
              <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center mr-4">
                <img v-if="item.logoUrl" :src="item.logoUrl" alt="Logo" class="w-14 h-14 object-cover rounded-lg" />
                <svg v-else class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
              </div>
              <div class="flex-1">
                <h3 class="font-semibold text-gray-800 text-lg">{{ item.namaBrand }}</h3>
                <p class="text-gray-600 text-sm">{{ item.namaCV }}</p>
              </div>
            </div>
            <div class="flex gap-2">
              <button 
                @click="editBrand(idx)" 
                class="flex-1 px-3 py-2 rounded-lg bg-amber-500 text-white font-medium hover:bg-amber-600 transition-colors duration-200 flex items-center justify-center gap-2"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
              </button>
              <button 
                @click="deleteBrand(idx)" 
                class="flex-1 px-3 py-2 rounded-lg bg-red-500 text-white font-medium hover:bg-red-600 transition-colors duration-200 flex items-center justify-center gap-2"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Hapus
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Dialog -->
      <BrandDialog 
        :open="dialogOpen" 
        :isEdit="editIdx !== null" 
        :brand="editIdx !== null ? { namaBrand: daftarBrand[editIdx].namaBrand, namaCV: daftarBrand[editIdx].namaCV, logo: null } : undefined" 
        @submit="handleDialogSubmit" 
        @close="closeDialog" 
      />
    </div>
  </AppLayout>
</template>
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ref } from 'vue';
import BrandDialog from './BrandDialog.vue';

const daftarBrand = ref<Array<{ namaBrand: string; namaCV: string; logoUrl: string | null }>>([]);
const dialogOpen = ref(false);
const editIdx = ref<number|null>(null);

function openDialog() {
  editIdx.value = null;
  dialogOpen.value = true;
}
function closeDialog() {
  dialogOpen.value = false;
}
function handleDialogSubmit(data: { namaBrand: string; namaCV: string; logo: File | null }) {
  let logoUrl = null;
  if (data.logo) logoUrl = URL.createObjectURL(data.logo);
  if (editIdx.value !== null) {
    daftarBrand.value[editIdx.value] = { ...data, logoUrl };
  } else {
    daftarBrand.value.push({ ...data, logoUrl });
  }
}
function editBrand(idx: number) {
  editIdx.value = idx;
  dialogOpen.value = true;
}
function deleteBrand(idx: number) {
  daftarBrand.value.splice(idx, 1);
}
</script>
