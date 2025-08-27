<template>
  <AppLayout :breadcrumbs="[{ title: 'Input Transaksi', href: '/transaksi-input' }]">
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <div class="grid auto-rows-min gap-4 md:grid-cols-3">
        <div class="relative h-auto md:h-[300px] overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border col-span-3">
          <div class="p-4 md:p-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
              <div>
                <h1 class="text-xl md:text-2xl font-bold mb-2 text-gray-900 dark:text-white">Input Transaksi</h1>
                <p class="text-gray-600 dark:text-gray-300">Kelola transaksi harian brand Anda</p>
              </div>
              <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                <button 
                  @click="triggerCsvImport" 
                  class="px-4 sm:px-6 py-2 sm:py-3 rounded-lg bg-gradient-to-r from-green-600 to-emerald-600 text-white font-medium hover:from-green-700 hover:to-emerald-700 shadow-lg transform hover:scale-105 transition-all duration-200 flex items-center justify-center gap-2 text-sm sm:text-base"
                >
                  <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                  </svg>
                  <span class="hidden sm:inline">Import CSV</span>
                  <span class="sm:hidden">Import</span>
                </button>
                <button 
                  @click="openDialog" 
                  class="px-4 sm:px-6 py-2 sm:py-3 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium hover:from-blue-700 hover:to-indigo-700 shadow-lg transform hover:scale-105 transition-all duration-200 flex items-center justify-center gap-2 text-sm sm:text-base"
                >
                  <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                  </svg>
                  <span class="hidden sm:inline">Tambah Transaksi</span>
                  <span class="sm:hidden">Tambah</span>
                </button>
              </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4">
              <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-green-500">
                <div class="flex items-center">
                  <div class="flex-1">
                    <p class="text-xs md:text-sm font-medium text-gray-600 dark:text-gray-300">Total Transaksi</p>
                    <p class="text-lg md:text-xl font-bold text-gray-900 dark:text-white">{{ daftarTransaksi.length }}</p>
                  </div>
                  <div class="p-2 bg-green-100 dark:bg-green-800 rounded-full">
                    <svg class="w-4 h-4 md:w-5 md:h-5 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                  </div>
                </div>
              </div>
              <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-blue-500">
                <div class="flex items-center">
                  <div class="flex-1">
                    <p class="text-xs md:text-sm font-medium text-gray-600 dark:text-gray-300">Total Nominal</p>
                    <p class="text-lg md:text-xl font-bold text-gray-900 dark:text-white">{{ formatRupiah(totalNominal) }}</p>
                  </div>
                  <div class="p-2 bg-blue-100 dark:bg-blue-800 rounded-full">
                    <svg class="w-4 h-4 md:w-5 md:h-5 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                  </div>
                </div>
              </div>
              <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-purple-500 sm:col-span-2 lg:col-span-1">
                <div class="flex items-center">
                  <div class="flex-1">
                    <p class="text-xs md:text-sm font-medium text-gray-600 dark:text-gray-300">Transaksi Hari Ini</p>
                    <p class="text-lg md:text-xl font-bold text-gray-900 dark:text-white">{{ transaksiHariIni.length }}</p>
                  </div>
                  <div class="p-2 bg-purple-100 dark:bg-purple-800 rounded-full">
                    <svg class="w-4 h-4 md:w-5 md:h-5 text-purple-600 dark:text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Daily Transaction Chart -->
      <DailyTransactionChart :transaksis="chartTransaksis" />
      
      <div class="relative min-h-[200px] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border bg-white dark:bg-gray-800 p-4 md:p-8 mt-4">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg md:text-xl font-semibold text-gray-900 dark:text-white">Daftar Transaksi</h2>
          <button 
            @click="showFilters = !showFilters" 
            class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200 flex items-center gap-2"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"/>
            </svg>
            Filter
            <svg 
              class="w-4 h-4 transition-transform duration-200" 
              :class="{ 'rotate-180': showFilters }" 
              fill="none" 
              stroke="currentColor" 
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </button>
        </div>

        <!-- Filter Section (Collapsible) -->
        <div 
          v-show="showFilters" 
          class="bg-gray-50 dark:bg-gray-900 rounded-xl shadow-lg p-6 mb-6 transition-all duration-300"
          :class="{ 'animate-slideDown': showFilters, 'animate-slideUp': !showFilters }"
        >
          <div class="flex flex-col gap-6">
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"/>
                </svg>
                Filter Transaksi
              </h3>
              
              <!-- Reset Filter Button -->
              <button 
                @click="resetFilter" 
                class="px-4 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors duration-200 flex items-center gap-2"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Reset Filter
              </button>
            </div>

            <!-- Filter Controls -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
              <!-- Brand Filter -->
              <div class="flex flex-col gap-2">
                <label for="brandFilter" class="text-sm font-medium text-gray-700 dark:text-gray-300">Brand:</label>
                <select 
                  id="brandFilter" 
                  v-model="selectedBrand" 
                  @change="applyFilters"
                  class="px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm"
                >
                  <option value="">Semua Brand</option>
                  <option v-for="brand in availableBrands" :key="brand" :value="brand">
                    {{ brand }}
                  </option>
                </select>
              </div>

              <!-- Month Filter -->
              <div class="flex flex-col gap-2">
                <label for="monthFilter" class="text-sm font-medium text-gray-700 dark:text-gray-300">Bulan:</label>
                <select 
                  id="monthFilter" 
                  v-model="selectedMonth" 
                  @change="applyFilters"
                  class="px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm"
                >
                  <option value="">Semua Bulan</option>
                  <option v-for="month in monthOptions" :key="month.value" :value="month.value">
                    {{ month.label }}
                  </option>
                </select>
              </div>

              <!-- Start Date Filter -->
              <div class="flex flex-col gap-2">
                <label for="startDate" class="text-sm font-medium text-gray-700 dark:text-gray-300">Dari Tanggal:</label>
                <input 
                  id="startDate" 
                  type="date" 
                  v-model="startDate" 
                  @change="applyFilters"
                  class="px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm"
                />
              </div>

              <!-- End Date Filter -->
              <div class="flex flex-col gap-2">
                <label for="endDate" class="text-sm font-medium text-gray-700 dark:text-gray-300">Sampai Tanggal:</label>
                <input 
                  id="endDate" 
                  type="date" 
                  v-model="endDate" 
                  @change="applyFilters"
                  class="px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm"
                />
              </div>
            </div>

            <!-- Quick Filter Buttons -->
            <div class="flex flex-wrap gap-2">
              <button 
                @click="setQuickFilter('today')" 
                class="px-3 py-2 text-sm bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-200 hover:bg-blue-200 dark:hover:bg-blue-700 rounded-lg transition-colors duration-200"
              >
                Hari Ini
              </button>
              <button 
                @click="setQuickFilter('week')" 
                class="px-3 py-2 text-sm bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-200 hover:bg-green-200 dark:hover:bg-green-700 rounded-lg transition-colors duration-200"
              >
                7 Hari Terakhir
              </button>
              <button 
                @click="setQuickFilter('month')" 
                class="px-3 py-2 text-sm bg-purple-100 dark:bg-purple-800 text-purple-800 dark:text-purple-200 hover:bg-purple-200 dark:hover:bg-purple-700 rounded-lg transition-colors duration-200"
              >
                30 Hari Terakhir
              </button>
              <button 
                @click="setQuickFilter('current-month')" 
                class="px-3 py-2 text-sm bg-orange-100 dark:bg-orange-800 text-orange-800 dark:text-orange-200 hover:bg-orange-200 dark:hover:bg-orange-700 rounded-lg transition-colors duration-200"
              >
                Bulan Ini
              </button>
            </div>

            <!-- Filter Summary -->
            <div v-if="hasActiveFilters" class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
              <div class="flex items-start justify-between gap-4">
                <div>
                  <p class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-2">Filter Aktif:</p>
                  <div class="flex flex-wrap gap-2">
                    <span v-if="selectedBrand" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-200">
                      Brand: {{ selectedBrand }}
                    </span>
                    <span v-if="selectedMonth" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-200">
                      Bulan: {{ getMonthName(selectedMonth) }}
                    </span>
                    <span v-if="startDate && endDate" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-800 text-red-800 dark:text-red-200">
                      {{ formatTanggal(startDate) }} - {{ formatTanggal(endDate) }}
                    </span>
                  </div>
                </div>
                <div class="text-sm text-blue-700 dark:text-blue-300">
                  <span class="font-medium">{{ filteredTransaksi.length }}</span> dari {{ daftarTransaksi.length }} transaksi
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr>
                <th class="border-b pb-2 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-600">Tanggal</th>
                <th class="border-b pb-2 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-600">Brand</th>
                <th class="border-b pb-2 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-600">Nominal</th>
                <th class="border-b pb-2 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-600">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, idx) in paginatedTransaksi" :key="idx" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="py-2 text-gray-900 dark:text-gray-100">{{ formatTanggal(item.tanggal) }}</td>
                <td class="py-2 text-gray-900 dark:text-gray-100">{{ item.brand }}</td>
                <td class="py-2 text-gray-900 dark:text-gray-100">{{ formatRupiah(item.nominal) }}</td>
                <td class="py-2">
                  <button @click="editTransaksi(getActualIndex(idx))" class="px-3 py-1 rounded bg-yellow-400 text-white hover:bg-yellow-500 text-sm">Edit</button>
                  <button @click="deleteTransaksi(getActualIndex(idx))" class="px-3 py-1 rounded bg-red-500 text-white hover:bg-red-600 ml-2 text-sm">Hapus</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden space-y-3">
          <div v-for="(item, idx) in paginatedTransaksi" :key="idx" class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
            <div class="flex justify-between items-start mb-3">
              <div class="flex-1">
                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ item.brand }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ formatTanggal(item.tanggal) }}</p>
              </div>
              <div class="text-right">
                <p class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ formatRupiah(item.nominal) }}</p>
              </div>
            </div>
            <div class="flex gap-2">
              <button @click="editTransaksi(getActualIndex(idx))" class="flex-1 px-3 py-2 rounded bg-yellow-400 text-white hover:bg-yellow-500 text-xs font-medium">
                Edit
              </button>
              <button @click="deleteTransaksi(getActualIndex(idx))" class="flex-1 px-3 py-2 rounded bg-red-500 text-white hover:bg-red-600 text-xs font-medium">
                Hapus
              </button>
            </div>
          </div>
        </div>
        
        <div v-if="daftarTransaksi.length === 0" class="text-gray-500 dark:text-gray-400 mt-4 text-center py-8">
          <svg class="w-12 h-12 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          Belum ada data transaksi.
        </div>
        
        <!-- Pagination -->
        <div v-if="showPagination" class="flex flex-col sm:flex-row sm:items-center sm:justify-between mt-6 border-t pt-4 border-gray-200 dark:border-gray-600 gap-4">
          <div class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 text-center sm:text-left">
            Menampilkan {{ ((currentPage - 1) * itemsPerPage) + 1 }} - {{ Math.min(currentPage * itemsPerPage, filteredTransaksi.length) }} dari {{ filteredTransaksi.length }} transaksi
            <span v-if="hasActiveFilters" class="text-blue-600 dark:text-blue-400">(hasil filter)</span>
          </div>
          
          <div class="flex items-center justify-center space-x-1">
            <!-- Previous Button -->
            <button 
              @click="prevPage" 
              :disabled="currentPage === 1"
              :class="[
                'px-2 sm:px-3 py-2 rounded-lg text-xs sm:text-sm font-medium transition-colors',
                currentPage === 1 
                  ? 'bg-gray-100 text-gray-400 cursor-not-allowed dark:bg-gray-700 dark:text-gray-500' 
                  : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700'
              ]"
            >
              <span class="hidden sm:inline">‹ Previous</span>
              <span class="sm:hidden">‹</span>
            </button>
            
            <!-- Page Numbers -->
            <template v-for="page in totalPages" :key="page">
              <button 
                v-if="page === 1 || page === totalPages || (page >= currentPage - 1 && page <= currentPage + 1)"
                @click="goToPage(page)"
                :class="[
                  'px-2 sm:px-3 py-2 rounded-lg text-xs sm:text-sm font-medium transition-colors min-w-[32px] sm:min-w-[36px]',
                  page === currentPage 
                    ? 'bg-blue-600 text-white' 
                    : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700'
                ]"
              >
                {{ page }}
              </button>
              <span 
                v-else-if="page === currentPage - 2 || page === currentPage + 2" 
                class="px-1 sm:px-2 py-2 text-gray-500 dark:text-gray-400 text-xs sm:text-sm"
              >
                ...
              </span>
            </template>
            
            <!-- Next Button -->
            <button 
              @click="nextPage" 
              :disabled="currentPage === totalPages"
              :class="[
                'px-2 sm:px-3 py-2 rounded-lg text-xs sm:text-sm font-medium transition-colors',
                currentPage === totalPages 
                  ? 'bg-gray-100 text-gray-400 cursor-not-allowed dark:bg-gray-700 dark:text-gray-500' 
                  : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700'
              ]"
            >
              <span class="hidden sm:inline">Next ›</span>
              <span class="sm:hidden">›</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Dialog -->
      <TransaksiDialog 
        :open="dialogOpen" 
        :isEdit="editIdx !== null" 
        :transaksi="editIdx !== null ? daftarTransaksi[editIdx] : undefined"
        :brandList="activeBrandList"
        @submit="handleDialogSubmit" 
        @close="closeDialog" 
      />

      <!-- Success Notification -->
      <div v-if="showNotification" class="fixed top-4 left-4 right-4 sm:left-auto sm:right-4 sm:w-auto z-50 transform transition-all duration-300">
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 sm:px-6 py-3 sm:py-4 rounded-lg shadow-lg flex items-center gap-3">
          <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <span class="font-medium text-sm sm:text-base flex-1">{{ notificationMessage }}</span>
          <button @click="hideNotification" class="ml-2 text-green-700 hover:text-green-900 flex-shrink-0">
            <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- Hidden CSV Input -->
      <input 
        type="file" 
        ref="csvFileInput" 
        accept=".csv" 
        @change="handleCsvImport" 
        class="hidden"
      />
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed, onMounted } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import TransaksiDialog from './TransaksiDialog.vue';
import DailyTransactionChart from '@/components/DailyTransactionChart.vue';

interface Brand {
  id: number;
  nama_brand: string;
  pemilik: string;
  logo_path?: string;
  namaBrand: string; // Transformed field
  namaCV: string; // Transformed field
  logoPath?: string; // Transformed field
}

interface Transaksi {
  id?: number;
  tanggal: string;
  brand: string;
  nominal: number;
  created_at?: string;
  updated_at?: string;
}

const props = defineProps<{
  brands?: Brand[];
  transaksis?: Transaksi[];
}>();

const page = usePage();
const daftarTransaksi = ref<Transaksi[]>(props.transaksis || []);
const dialogOpen = ref(false);
const editIdx = ref<number | null>(null);
const csvFileInput = ref<HTMLInputElement>();
const showNotification = ref(false);
const notificationMessage = ref('');

// Filter state
const showFilters = ref<boolean>(false);
const selectedBrand = ref<string>('');
const selectedMonth = ref<string>('');
const startDate = ref<string>('');
const endDate = ref<string>('');

// Month options
const monthOptions = ref([
  { value: '01', label: 'Januari' },
  { value: '02', label: 'Februari' },
  { value: '03', label: 'Maret' },
  { value: '04', label: 'April' },
  { value: '05', label: 'Mei' },
  { value: '06', label: 'Juni' },
  { value: '07', label: 'Juli' },
  { value: '08', label: 'Agustus' },
  { value: '09', label: 'September' },
  { value: '10', label: 'Oktober' },
  { value: '11', label: 'November' },
  { value: '12', label: 'Desember' },
]);

// Pagination state
const currentPage = ref(1);
const itemsPerPage = 31;

// Transform brands for dialog component
const activeBrandList = computed(() => {
  return props.brands || [];
});

// Check for flash message when component mounts
onMounted(() => {
  // Normalize nominal values to numbers
  daftarTransaksi.value = daftarTransaksi.value.map(item => ({
    ...item,
    nominal: typeof item.nominal === 'string' ? parseFloat(item.nominal) : item.nominal
  }));

  const flashMessage = (page.props.flash as any)?.success;
  if (flashMessage) {
    showSuccessNotification(flashMessage as string);
  }
});

function showSuccessNotification(message: string) {
  notificationMessage.value = message;
  showNotification.value = true;
  // Auto hide after 3 seconds
  setTimeout(() => {
    hideNotification();
  }, 3000);
}

function hideNotification() {
  showNotification.value = false;
  notificationMessage.value = '';
}

const totalNominal = computed(() => {
  return daftarTransaksi.value.reduce((total, item) => {
    const nominal = typeof item.nominal === 'string' ? parseFloat(item.nominal) : item.nominal;
    return total + (isNaN(nominal) ? 0 : nominal);
  }, 0);
});

const transaksiHariIni = computed(() => {
  return daftarTransaksi.value;
});

// Filter computed properties
const availableBrands = computed(() => {
  const brands = new Set<string>();
  daftarTransaksi.value.forEach(transaksi => {
    brands.add(transaksi.brand);
  });
  return Array.from(brands).sort();
});

const hasActiveFilters = computed(() => {
  return selectedBrand.value || selectedMonth.value || (startDate.value && endDate.value);
});

const filteredTransaksi = computed(() => {
  let filtered = daftarTransaksi.value;

  // Filter by brand
  if (selectedBrand.value) {
    filtered = filtered.filter(transaksi => transaksi.brand === selectedBrand.value);
  }

  // Filter by month
  if (selectedMonth.value) {
    filtered = filtered.filter(transaksi => {
      const transaksiMonth = new Date(transaksi.tanggal).getMonth() + 1;
      return transaksiMonth.toString().padStart(2, '0') === selectedMonth.value;
    });
  }

  // Filter by custom date range
  if (startDate.value && endDate.value) {
    filtered = filtered.filter(transaksi => {
      const transaksiDate = new Date(transaksi.tanggal);
      const start = new Date(startDate.value);
      const end = new Date(endDate.value);
      return transaksiDate >= start && transaksiDate <= end;
    });
  }

  return filtered;
});

// Pagination computed properties - use filtered data
const totalPages = computed(() => {
  return Math.ceil(filteredTransaksi.value.length / itemsPerPage);
});

const paginatedTransaksi = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  return filteredTransaksi.value.slice(start, end);
});

const showPagination = computed(() => {
  return filteredTransaksi.value.length > itemsPerPage;
});

// Convert data for chart component (ensure id is always a number)
const chartTransaksis = computed(() => {
  return daftarTransaksi.value.map((item, index) => ({
    id: item.id || index + 1,
    tanggal: item.tanggal,
    brand: item.brand,
    nominal: item.nominal
  }));
});

function openDialog() {
  editIdx.value = null;
  dialogOpen.value = true;
}

function closeDialog() {
  dialogOpen.value = false;
}

// Pagination functions
function goToPage(page: number) {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
  }
}

function nextPage() {
  if (currentPage.value < totalPages.value) {
    currentPage.value++;
  }
}

function prevPage() {
  if (currentPage.value > 1) {
    currentPage.value--;
  }
}

// Filter functions
function applyFilters() {
  // Reset pagination to first page when filters change
  currentPage.value = 1;
}

function resetFilter() {
  selectedBrand.value = '';
  selectedMonth.value = '';
  startDate.value = '';
  endDate.value = '';
  currentPage.value = 1;
}

function setQuickFilter(type: string) {
  resetFilter();
  const today = new Date();
  
  switch (type) {
    case 'today':
      startDate.value = today.toISOString().split('T')[0];
      endDate.value = today.toISOString().split('T')[0];
      break;
    case 'week':
      const weekAgo = new Date(today);
      weekAgo.setDate(today.getDate() - 7);
      startDate.value = weekAgo.toISOString().split('T')[0];
      endDate.value = today.toISOString().split('T')[0];
      break;
    case 'month':
      const monthAgo = new Date(today);
      monthAgo.setDate(today.getDate() - 30);
      startDate.value = monthAgo.toISOString().split('T')[0];
      endDate.value = today.toISOString().split('T')[0];
      break;
    case 'current-month':
      const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
      const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
      startDate.value = firstDay.toISOString().split('T')[0];
      endDate.value = lastDay.toISOString().split('T')[0];
      break;
  }
  applyFilters();
}

function getMonthName(monthValue: string): string {
  const month = monthOptions.value.find(m => m.value === monthValue);
  return month ? month.label : '';
}

function getActualIndex(paginatedIndex: number): number {
  // Get the actual index from the filtered results
  const filteredItem = paginatedTransaksi.value[paginatedIndex];
  return daftarTransaksi.value.findIndex(item => 
    item.tanggal === filteredItem.tanggal && 
    item.brand === filteredItem.brand && 
    item.nominal === filteredItem.nominal
  );
}

function resetPaginationAfterAdd() {
  // Reset ke halaman terakhir setelah menambah data baru
  const newTotalPages = Math.ceil(daftarTransaksi.value.length / itemsPerPage);
  currentPage.value = newTotalPages;
}

function handleDialogSubmit(data: { tanggal: string; brand: string; nominal: number }) {
  const formData = new FormData();
  formData.append('tanggal', data.tanggal);
  formData.append('brand', data.brand);
  formData.append('nominal', data.nominal.toString());

  if (editIdx.value !== null) {
    // Update existing transaksi
    const transaksi = daftarTransaksi.value[editIdx.value];
    if (transaksi && transaksi.id) {
      formData.append('_method', 'PUT');
      router.post(`/transaksis/${transaksi.id}`, formData, {
        onSuccess: () => {
          closeDialog();
          showSuccessNotification('Transaksi berhasil diperbarui!');
          // Refresh page to show updated data
          setTimeout(() => {
            router.visit('/transaksi-input', { preserveState: false });
          }, 1000);
        },
        onError: () => {
          showSuccessNotification('Terjadi kesalahan saat memperbarui transaksi.');
        }
      });
    }
  } else {
    // Create new transaksi
    router.post('/transaksi-input', formData, {
      onSuccess: () => {
        closeDialog();
        showSuccessNotification('Transaksi berhasil ditambahkan!');
        // Refresh page to show new data
        setTimeout(() => {
          router.visit('/transaksi-input', { preserveState: false });
        }, 1000);
      },
      onError: () => {
        showSuccessNotification('Terjadi kesalahan saat menambahkan transaksi.');
      }
    });
  }
}

function editTransaksi(idx: number) {
  editIdx.value = idx;
  dialogOpen.value = true;
}

function deleteTransaksi(idx: number) {
  const transaksi = daftarTransaksi.value[idx];
  if (transaksi && confirm(`Apakah Anda yakin ingin menghapus transaksi ${transaksi.brand} tanggal ${formatTanggal(transaksi.tanggal)}?`)) {
    if (transaksi.id) {
      router.delete(`/transaksis/${transaksi.id}`, {
        onSuccess: () => {
          showSuccessNotification('Transaksi berhasil dihapus!');
          // Refresh page to show updated data
          setTimeout(() => {
            router.visit('/transaksi-input', { preserveState: false });
          }, 1000);
        },
        onError: () => {
          showSuccessNotification('Terjadi kesalahan saat menghapus transaksi.');
        }
      });
    }
  }
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

function triggerCsvImport() {
  csvFileInput.value?.click();
}

function handleCsvImport(event: Event) {
  const target = event.target as HTMLInputElement;
  const file = target.files?.[0];
  
  if (!file) return;
  
  const reader = new FileReader();
  reader.onload = (e) => {
    const csv = e.target?.result as string;
    parseCSV(csv);
  };
  reader.readAsText(file);
}

function parseCSV(csv: string) {
  const lines = csv.split('\n');
  const headers = lines[0].split(',').map(h => h.trim());
  
  // Cari index kolom yang diperlukan
  const tanggalIndex = headers.findIndex(h => h.toLowerCase().includes('tanggal') || h.toLowerCase().includes('date'));
  const brandIndex = headers.findIndex(h => h.toLowerCase().includes('brand') || h.toLowerCase().includes('nama'));
  
  // Untuk data Excel yang diberikan, ambil kolom NYORE COFFEE (index 1)
  const nominalIndex = 1; // NYORE COFFEE ada di kolom B (index 1)
  
  for (let i = 1; i < lines.length; i++) {
    const values = lines[i].split(',').map(v => v.trim());
    
    if (values.length < 2) continue;
    
    // Parse tanggal dari format seperti "1/25", "2/25", dll
    const dateStr = values[0];
    if (!dateStr) continue;
    
    const [day, month] = dateStr.split('/');
    const currentYear = new Date().getFullYear();
    const tanggal = `${currentYear}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
    
    // Parse nominal (hapus Rp, titik, dan koma)
    const nominalStr = values[nominalIndex];
    if (!nominalStr || nominalStr === 'Rp0' || nominalStr === '') continue;
    
    const nominal = parseInt(nominalStr.replace(/[Rp.,]/g, ''));
    if (isNaN(nominal) || nominal === 0) continue;
    
    // Gunakan brand default NYORE COFFEE
    const brand = 'NYORE COFFEE';
    
    // Tambah ke daftar transaksi
    daftarTransaksi.value.push({
      tanggal,
      brand,
      nominal
    });
  }
  
  // Reset input file
  if (csvFileInput.value) {
    csvFileInput.value.value = '';
  }
  
  alert(`Berhasil import ${daftarTransaksi.value.length} transaksi dari CSV`);
}
</script>

<style scoped>
@keyframes slideDown {
  from {
    opacity: 0;
    max-height: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    max-height: 1000px;
    transform: translateY(0);
  }
}

@keyframes slideUp {
  from {
    opacity: 1;
    max-height: 1000px;
    transform: translateY(0);
  }
  to {
    opacity: 0;
    max-height: 0;
    transform: translateY(-10px);
  }
}

.animate-slideDown {
  animation: slideDown 0.3s ease-out forwards;
}

.animate-slideUp {
  animation: slideUp 0.3s ease-out forwards;
}
</style>
