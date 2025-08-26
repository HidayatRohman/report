<template>
  <AppLayout :breadcrumbs="[{ title: 'Daftar Brand', href: '/brand-list' }]">
    <div class="p-8">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Daftar Brand</h1>
        <button @click="openDialog()" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Tambah Brand</button>
      </div>
      <table class="w-full text-left border-collapse shadow rounded-xl overflow-hidden">
        <thead class="bg-gray-100">
          <tr>
            <th class="border-b pb-2 px-4 py-2">Nama Brand</th>
            <th class="border-b pb-2 px-4 py-2">Pemilik</th>
            <th class="border-b pb-2 px-4 py-2">Deskripsi</th>
            <th class="border-b pb-2 px-4 py-2">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="brand in brands" :key="brand.id" class="hover:bg-gray-50">
            <td class="py-2 px-4">{{ brand.nama_brand }}</td>
            <td class="py-2 px-4">{{ brand.pemilik }}</td>
            <td class="py-2 px-4">{{ brand.deskripsi || '-' }}</td>
            <td class="py-2 px-4">
              <button @click="editBrand(brand)" class="px-3 py-1 rounded bg-yellow-400 text-white hover:bg-yellow-500">Edit</button>
              <button @click="deleteBrand(brand)" class="px-3 py-1 rounded bg-red-500 text-white hover:bg-red-600 ml-2">Hapus</button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="brands.length === 0" class="text-gray-500 mt-4">Belum ada data brand.</div>
      <BrandDialog
        :open="dialogOpen"
        :isEdit="editingBrand !== null"
        :brand="editingBrand"
        @submit="handleDialogSubmit"
        @close="closeDialog"
      />
    </div>
  </AppLayout>
</template>
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import BrandDialog from './BrandDialog.vue';

interface Brand {
  id: number;
  nama_brand: string;
  pemilik: string;
  deskripsi?: string;
  created_at: string;
  updated_at: string;
}

const props = defineProps<{
  brands: Brand[];
}>();

const dialogOpen = ref(false);
const editingBrand = ref<Brand | null>(null);

function openDialog() {
  editingBrand.value = null;
  dialogOpen.value = true;
}

function closeDialog() {
  dialogOpen.value = false;
  editingBrand.value = null;
}

function handleDialogSubmit(data: { nama_brand: string; pemilik: string; deskripsi?: string }) {
  if (editingBrand.value) {
    // Update existing brand
    router.put(`/brands/${editingBrand.value.id}`, data, {
      onSuccess: () => {
        closeDialog();
      }
    });
  } else {
    // Create new brand
    router.post('/brands', data, {
      onSuccess: () => {
        closeDialog();
      }
    });
  }
}

function editBrand(brand: Brand) {
  editingBrand.value = { ...brand };
  dialogOpen.value = true;
}

function deleteBrand(brand: Brand) {
  if (confirm(`Apakah Anda yakin ingin menghapus brand "${brand.nama_brand}"?`)) {
    router.delete(`/brands/${brand.id}`);
  }
}
</script>
