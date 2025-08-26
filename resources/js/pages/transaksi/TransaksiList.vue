<template>
  <AppLayout :breadcrumbs="[{ title: 'Daftar Transaksi', href: '/transaksi-list' }]">
    <div class="p-8">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Daftar Transaksi</h1>
        <button @click="openDialog()" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Tambah Transaksi</button>
      </div>
      <table class="w-full text-left border-collapse shadow rounded-xl overflow-hidden">
        <thead class="bg-gray-100">
          <tr>
            <th class="border-b pb-2">Tanggal</th>
            <th class="border-b pb-2">Brand</th>
            <th class="border-b pb-2">Nominal</th>
            <th class="border-b pb-2">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, idx) in daftarTransaksi" :key="idx" class="hover:bg-gray-50">
            <td class="py-2">{{ formatTanggal(item.tanggal) }}</td>
            <td class="py-2">{{ item.brand }}</td>
            <td class="py-2">{{ formatRupiah(item.nominal) }}</td>
            <td class="py-2">
              <button @click="editTransaksi(idx)" class="px-3 py-1 rounded bg-yellow-400 text-white hover:bg-yellow-500">Edit</button>
              <button @click="deleteTransaksi(idx)" class="px-3 py-1 rounded bg-red-500 text-white hover:bg-red-600 ml-2">Hapus</button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="daftarTransaksi.length === 0" class="text-gray-500 mt-4">Belum ada data transaksi.</div>
      <TransaksiDialog 
        :open="dialogOpen" 
        :isEdit="editIdx !== null" 
        :transaksi="editIdx !== null ? daftarTransaksi[editIdx] : undefined"
        :brandList="brandList"
        @submit="handleDialogSubmit" 
        @close="closeDialog" 
      />
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ref } from 'vue';
import TransaksiDialog from './TransaksiDialog.vue';

const daftarTransaksi = ref<Array<{ tanggal: string; brand: string; nominal: number }>>([]);
const dialogOpen = ref(false);
const editIdx = ref<number|null>(null);

// Simulasi data brand
const brandList = ref([
  { namaBrand: 'Nike', namaCV: 'CV Sportindo' },
  { namaBrand: 'Adidas', namaCV: 'CV Atletik' },
  { namaBrand: 'Puma', namaCV: 'CV Dinamis' },
]);

function openDialog() {
  editIdx.value = null;
  dialogOpen.value = true;
}

function closeDialog() {
  dialogOpen.value = false;
}

function handleDialogSubmit(data: { tanggal: string; brand: string; nominal: number }) {
  if (editIdx.value !== null) {
    daftarTransaksi.value[editIdx.value] = { ...data };
  } else {
    daftarTransaksi.value.push({ ...data });
  }
}

function editTransaksi(idx: number) {
  editIdx.value = idx;
  dialogOpen.value = true;
}

function deleteTransaksi(idx: number) {
  daftarTransaksi.value.splice(idx, 1);
}

function formatRupiah(amount: number): string {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR'
  }).format(amount);
}

function formatTanggal(tanggal: string): string {
  return new Date(tanggal).toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'long',
    year: 'numeric'
  });
}
</script>
