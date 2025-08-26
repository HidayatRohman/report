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
