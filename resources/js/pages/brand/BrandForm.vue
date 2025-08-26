<template>
  <AppLayout :breadcrumbs="[{ title: 'Input Brand', href: '/brand-input' }]">
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <div class="grid auto-rows-min gap-4 md:grid-cols-3">
        <div class="relative h-[300px] overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border col-span-3">
          <div class="p-8">
            <div class="flex items-center justify-between mb-6">
              <div>
                <h1 class="text-2xl font-bold mb-2">Input Brand</h1>
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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div class="bg-white rounded-xl shadow-lg p-4 border-l-4 border-blue-500">
                <div class="flex items-center">
                  <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600">Total Brand</p>
                    <p class="text-xl font-bold text-gray-900">{{ daftarBrand.length }}</p>
                  </div>
                  <div class="p-2 bg-blue-100 rounded-full">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                  </div>
                </div>
              </div>
              <div class="bg-white rounded-xl shadow-lg p-4 border-l-4 border-green-500">
                <div class="flex items-center">
                  <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600">Brand Aktif</p>
                    <p class="text-xl font-bold text-gray-900">{{ daftarBrand.length }}</p>
                  </div>
                  <div class="p-2 bg-green-100 rounded-full">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                  </div>
                </div>
              </div>
              <div class="bg-white rounded-xl shadow-lg p-4 border-l-4 border-purple-500">
                <div class="flex items-center">
                  <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600">CV Terdaftar</p>
                    <p class="text-xl font-bold text-gray-900">{{ new Set(daftarBrand.map(b => b.namaCV)).size }}</p>
                  </div>
                  <div class="p-2 bg-purple-100 rounded-full">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="relative min-h-[200px] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border bg-white p-8 mt-4">
        <h2 class="text-xl font-semibold mb-4">Daftar Brand</h2>
        <table class="w-full text-left border-collapse">
          <thead>
            <tr>
              <th class="border-b pb-2">Logo</th>
              <th class="border-b pb-2">Nama Brand</th>
              <th class="border-b pb-2">Nama CV</th>
              <th class="border-b pb-2">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, idx) in daftarBrand" :key="idx" class="hover:bg-gray-50">
              <td class="py-2">
                <img v-if="item.logoUrl" :src="item.logoUrl" alt="Logo" class="h-10 rounded" />
                <span v-else>-</span>
              </td>
              <td class="py-2">{{ item.namaBrand }}</td>
              <td class="py-2">{{ item.namaCV }}</td>
              <td class="py-2">
                <button @click="editBrand(idx)" class="px-3 py-1 rounded bg-yellow-400 text-white hover:bg-yellow-500">Edit</button>
                <button @click="deleteBrand(idx)" class="px-3 py-1 rounded bg-red-500 text-white hover:bg-red-600 ml-2">Hapus</button>
              </td>
            </tr>
          </tbody>
        </table>
        <div v-if="daftarBrand.length === 0" class="text-gray-500 mt-4">Belum ada data brand.</div>
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
import { ref, onMounted } from 'vue';
import BrandDialog from './BrandDialog.vue';

const daftarBrand = ref<Array<{ namaBrand: string; namaCV: string; logoUrl: string | null }>>([]);
const dialogOpen = ref(false);
const editIdx = ref<number|null>(null);

// Load data dari localStorage saat component di-mount
onMounted(() => {
  const savedBrands = localStorage.getItem('daftarBrand');
  if (savedBrands) {
    try {
      daftarBrand.value = JSON.parse(savedBrands);
    } catch (e) {
      console.error('Error parsing saved brands:', e);
    }
  }
});

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
  
  // Simpan ke localStorage agar bisa diakses di halaman transaksi
  localStorage.setItem('daftarBrand', JSON.stringify(daftarBrand.value));
}
function editBrand(idx: number) {
  editIdx.value = idx;
  dialogOpen.value = true;
}
function deleteBrand(idx: number) {
  daftarBrand.value.splice(idx, 1);
  // Update localStorage setelah hapus
  localStorage.setItem('daftarBrand', JSON.stringify(daftarBrand.value));
}
</script>
