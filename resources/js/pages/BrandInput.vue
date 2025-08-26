<template>
  <AppLayout :breadcrumbs="[{ title: 'Input Brand', href: '/brand-input' }]">
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <div class="grid auto-rows-min gap-4 md:grid-cols-3">
        <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border col-span-3">
          <div class="p-8">
            <h1 class="text-2xl font-bold mb-4">Input Brand</h1>
            <form @submit.prevent="submitForm" enctype="multipart/form-data" class="space-y-4">
              <div>
                <label for="logo" class="block mb-2 font-medium">Logo Brand</label>
                <input type="file" id="logo" name="logo" @change="handleFileUpload" accept="image/*" class="w-full" />
              </div>
              <div>
                <label for="namaBrand" class="block mb-2 font-medium">Nama Brand</label>
                <input type="text" id="namaBrand" v-model="form.namaBrand" required class="w-full" />
              </div>
              <div>
                <label for="namaCV" class="block mb-2 font-medium">Nama CV</label>
                <input type="text" id="namaCV" v-model="form.namaCV" required class="w-full" />
              </div>
              <button type="submit" class="px-6 py-2 bg-sidebar-foreground text-sidebar rounded-lg">Simpan</button>
            </form>
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
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, idx) in daftarBrand" :key="idx">
              <td class="py-2">
                <img v-if="item.logoUrl" :src="item.logoUrl" alt="Logo" class="h-10" />
                <span v-else>-</span>
              </td>
              <td class="py-2">{{ item.namaBrand }}</td>
              <td class="py-2">{{ item.namaCV }}</td>
            </tr>
          </tbody>
        </table>
        <div v-if="daftarBrand.length === 0" class="text-gray-500 mt-4">Belum ada data brand.</div>
      </div>
    </div>
  </AppLayout>
</template>
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
// ...existing code...

<script setup lang="ts">
import { ref } from 'vue';

const form = ref({
  namaBrand: '',
  namaCV: '',
  logo: null as File | null,
});

const daftarBrand = ref<Array<{ namaBrand: string; namaCV: string; logoUrl: string | null }>>([]);

function handleFileUpload(event: Event) {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files.length > 0) {
    form.value.logo = target.files[0];
  }
}

function submitForm() {
  let logoUrl = null;
  if (form.value.logo) {
    logoUrl = URL.createObjectURL(form.value.logo);
  }
  daftarBrand.value.push({
    namaBrand: form.value.namaBrand,
    namaCV: form.value.namaCV,
    logoUrl,
  });
  form.value.namaBrand = '';
  form.value.namaCV = '';
  form.value.logo = null;
  alert('Brand berhasil disimpan!');
}
</script>

<style scoped>
.brand-input {
  max-width: 500px;
  margin: 40px auto;
  padding: 32px;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
.form-group {
  margin-bottom: 20px;
}
label {
  display: block;
  margin-bottom: 8px;
  font-weight: 500;
}
input[type="text"], input[type="file"] {
  width: 100%;
  padding: 8px;
  border-radius: 6px;
  border: 1px solid #ccc;
}
button {
  padding: 10px 24px;
  background: #2c3e50;
  color: #fff;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}
button:hover {
  background: #34495e;
}
</style>
