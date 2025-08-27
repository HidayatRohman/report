<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-100 overflow-x-hidden max-w-full w-full">
        <!-- Header -->
        <header class="relative z-10 px-4 sm:px-6 py-4" style="padding-right: 0px;">
            <nav class="flex items-center justify-between max-w-7xl mx-auto">
                <!-- Logo -->
                <div class="flex items-center space-x-2">
                    <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center" style="background-color: #113F67;">
                        <span class="text-white font-bold text-xs sm:text-sm">{{ appSettings?.app_name?.substring(0, 3) || 'KPA' }}</span>
                    </div>
                    <span class="font-semibold text-gray-800 text-sm sm:text-base">{{ appSettings?.app_name || 'Kelola Pusat App' }}</span>
                </div>

                <!-- Auth Buttons -->
                <div class="flex items-center space-x-2 sm:space-x-4">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="dashboard()"
                        class="text-white px-3 py-1.5 sm:px-6 sm:py-2 rounded-lg font-medium transition-colors hover:opacity-90 text-sm"
                        style="background-color: #113F67;"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="login()"
                            class="text-gray-600 hover:text-gray-800 font-medium text-sm"
                        >
                            Masuk
                        </Link>
                        <Link
                            :href="register()"
                            class="text-white px-3 py-1.5 sm:px-6 sm:py-2 rounded-lg font-medium transition-colors hover:opacity-90 text-sm"
                            style="background-color: #113F67;"
                        >
                            Daftar
                        </Link>
                    </template>
                    
                    <!-- Menu button for mobile -->
                    <button class="md:hidden p-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </nav>
        </header>

        <!-- Main Content -->
        <div class="relative px-4 sm:px-6 py-8 sm:py-12 lg:py-20">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center">
                    <!-- Left Content -->
                    <div class="space-y-6 lg:space-y-8 text-center lg:text-left order-2 lg:order-1">
                        <div class="space-y-4 lg:space-y-6">
                            <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 leading-tight">
                                Pencatatan Transaksi yang 
                                <span style="color: #113F67;">mudah & aman</span>
                            </h1>
                            <p class="text-base sm:text-lg text-gray-600 leading-relaxed max-w-2xl mx-auto lg:mx-0">
                                Catat dan kelola semua transaksi keuangan Anda dengan mudah. Sistem yang aman dan terpercaya untuk laporan keuangan yang akurat.
                            </p>
                        </div>
                    </div>

                    <!-- Right Content - Phone Mockup -->
                    <div class="relative order-1 lg:order-2 overflow-hidden">
                        <!-- Background decorative elements -->
                        <div class="absolute -top-10 sm:-top-20 -right-5 sm:-right-10 w-20 h-20 sm:w-40 sm:h-40 rounded-full opacity-60" style="background-color: #113F67;"></div>
                        <div class="absolute -bottom-8 sm:-bottom-16 -left-5 sm:-left-8 w-16 h-16 sm:w-32 sm:h-32 bg-purple-200 rounded-full opacity-60"></div>
                        
                        <!-- Phone Mockup -->
                        <div class="relative max-w-xs sm:max-w-sm mx-auto px-4">
                            <div class="relative bg-white rounded-2xl sm:rounded-3xl shadow-2xl p-1 sm:p-2 mx-auto">
                                <!-- Phone frame -->
                                <div class="bg-gray-900 rounded-2xl sm:rounded-3xl p-0.5 sm:p-1">
                                    <div class="bg-white rounded-2xl sm:rounded-3xl overflow-hidden">
                                        <!-- Status bar -->
                                        <div class="flex justify-between items-center px-4 sm:px-6 py-2 sm:py-3 bg-white">
                                            <span class="text-xs sm:text-sm font-medium">9:41</span>
                                            <div class="flex space-x-1">
                                                <div class="w-3 h-1.5 sm:w-4 sm:h-2 bg-green-500 rounded-sm"></div>
                                                <div class="w-4 h-2 sm:w-6 sm:h-3 border border-gray-300 rounded-sm">
                                                    <div class="w-full h-full bg-green-500 rounded-sm"></div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- App content -->
                                        <div class="px-4 sm:px-6 py-3 sm:py-4 space-y-4 sm:space-y-6">
                                            <!-- Welcome header -->
                                            <div class="flex justify-between items-center">
                                                <div>
                                                    <h3 class="text-sm sm:text-lg font-semibold text-gray-900">Selamat datang kembali</h3>
                                                    <p class="text-xs sm:text-sm text-gray-600">Owner Sukses</p>
                                                </div>
                                                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full flex items-center justify-center" style="background-color: #e8f1fa;">
                                                    <svg class="w-4 h-4 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #113F67;">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5-5-5h5z"></path>
                                                    </svg>
                                                </div>
                                            </div>

                                            <!-- Balance card -->
                                            <div class="rounded-xl sm:rounded-2xl p-4 sm:p-6 text-white" style="background: linear-gradient(to right, #113F67, #0d2f4f);">
                                                <div class="space-y-3 sm:space-y-4">
                                                    <div>
                                                        <p class="text-blue-100 text-xs sm:text-sm">Total Omzet</p>
                                                        <p class="text-lg sm:text-2xl font-bold">Rp XXX.XXX.XXX.XXX</p>
                                                    </div>
                                                    <div class="flex justify-between items-center">
                                                        <span class="text-xs sm:text-sm text-blue-100">Cabang Yogyakarta</span>
                                                        <div class="flex space-x-1 sm:space-x-2">
                                                            <div class="w-6 h-4 sm:w-8 sm:h-6 bg-white bg-opacity-20 rounded"></div>
                                                            <div class="w-6 h-4 sm:w-8 sm:h-6 bg-white bg-opacity-20 rounded"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Quick actions -->
                                            <div class="grid grid-cols-4 gap-2 sm:gap-4">
                                                <div class="text-center">
                                                    <div class="w-8 h-8 sm:w-12 sm:h-12 bg-blue-100 rounded-lg sm:rounded-xl flex items-center justify-center mx-auto mb-1 sm:mb-2">
                                                        <svg class="w-4 h-4 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                        </svg>
                                                    </div>
                                                    <span class="text-xs text-gray-600">Transaksi</span>
                                                </div>
                                                <div class="text-center">
                                                    <div class="w-8 h-8 sm:w-12 sm:h-12 bg-green-100 rounded-lg sm:rounded-xl flex items-center justify-center mx-auto mb-1 sm:mb-2">
                                                        <svg class="w-4 h-4 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                                        </svg>
                                                    </div>
                                                    <span class="text-xs text-gray-600">Omzet</span>
                                                </div>
                                                <div class="text-center">
                                                    <div class="w-8 h-8 sm:w-12 sm:h-12 bg-purple-100 rounded-lg sm:rounded-xl flex items-center justify-center mx-auto mb-1 sm:mb-2">
                                                        <svg class="w-4 h-4 sm:w-6 sm:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                                        </svg>
                                                    </div>
                                                    <span class="text-xs text-gray-600">Grafik Penjualan</span>
                                                </div>
                                                <div class="text-center">
                                                    <div class="w-8 h-8 sm:w-12 sm:h-12 bg-orange-100 rounded-lg sm:rounded-xl flex items-center justify-center mx-auto mb-1 sm:mb-2">
                                                        <svg class="w-4 h-4 sm:w-6 sm:h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                                        </svg>
                                                    </div>
                                                    <span class="text-xs text-gray-600">Lainnya</span>
                                                </div>
                                            </div>

                                            <!-- Recent transactions -->
                                            <div class="space-y-2 sm:space-y-3">
                                                <div class="flex justify-between items-center">
                                                    <h4 class="text-sm sm:text-base font-semibold text-gray-900">Transaksi Terkini</h4>
                                                    <button class="text-xs sm:text-sm font-medium" style="color: #113F67;">Lihat Semua</button>
                                                </div>
                                                
                                                <div class="space-y-2 sm:space-y-3">
                                                    <div class="flex items-center justify-between">
                                                        <div class="flex items-center space-x-2 sm:space-x-3">
                                                            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-green-100 rounded-full flex items-center justify-center">
                                                                <span class="text-green-600 font-semibold text-xs sm:text-sm">+</span>
                                                            </div>
                                                            <div>
                                                                <p class="font-medium text-gray-900 text-xs sm:text-sm">Omset Coffee</p>
                                                                <p class="text-xs text-gray-600">Hari ini, 14:30</p>
                                                            </div>
                                                        </div>
                                                        <span class="font-semibold text-green-600 text-xs sm:text-sm">+Rp xx.xxx.xxx</span>
                                                    </div>
                                                    
                                                    <div class="flex items-center justify-between">
                                                        <div class="flex items-center space-x-2 sm:space-x-3">
                                                            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-green-100 rounded-full flex items-center justify-center">
                                                                <span class="text-green-600 font-semibold text-xs sm:text-sm">+</span>
                                                            </div>
                                                            <div>
                                                                <p class="font-medium text-gray-900 text-xs sm:text-sm">Omset Kedai</p>
                                                                <p class="text-xs text-gray-600">Kemarin, 09:00</p>
                                                            </div>
                                                        </div>
                                                        <span class="font-semibold text-green-600 text-xs sm:text-sm">+Rp XXX.XXX.XXX</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Brand Terdaftar Section -->
        <div v-if="daftarBrand.length > 0" class="relative z-10 px-4 sm:px-6 py-12">
            <div class="max-w-7xl mx-auto">
                <div class="bg-white rounded-2xl shadow-xl p-8 sm:p-12">
                    <div class="text-center mb-8">
                        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4">Brand Terdaftar</h2>
                        <p class="text-gray-600">Brand-brand yang telah bergabung dengan platform kami</p>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        <div v-for="(brand, idx) in daftarBrand" :key="idx" class="group">
                            <div class="bg-gradient-to-br from-gray-50 to-white p-6 rounded-xl border border-gray-200 hover:shadow-lg hover:border-blue-300 transition-all duration-300 transform hover:-translate-y-1">
                                <div class="flex items-center gap-4">
                                    <!-- Brand Logo -->
                                    <div class="flex-shrink-0">
                                        <img 
                                            v-if="brand.logoUrl" 
                                            :src="brand.logoUrl" 
                                            :alt="`Logo ${brand.namaBrand}`" 
                                            class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl object-cover border-2 border-gray-200 group-hover:border-blue-300 transition-colors"
                                        />
                                        <div 
                                            v-else 
                                            class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl border-2 border-gray-200 group-hover:border-blue-300 transition-colors flex items-center justify-center"
                                            style="background-color: #113F67;"
                                        >
                                            <span class="text-white font-bold text-sm">
                                                {{ brand.namaBrand.substring(0, 2).toUpperCase() }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <!-- Brand Info -->
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-gray-900 text-sm sm:text-base truncate group-hover:text-blue-600 transition-colors">
                                            {{ brand.namaBrand }}
                                        </h3>
                                        <p class="text-gray-500 text-xs sm:text-sm truncate">
                                            {{ brand.namaCV }}
                                        </p>
                                        <div class="flex items-center mt-2">
                                            <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                                            <span class="text-xs text-green-600 ml-2 font-medium">Aktif</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- View All Button -->
                    <div class="text-center mt-8">
                        <Link 
                            v-if="$page.props.auth.user"
                            :href="dashboard()"
                            class="inline-flex items-center px-6 py-3 text-white font-medium rounded-lg transition-all duration-200 hover:opacity-90 transform hover:scale-105 shadow-lg"
                            style="background-color: #113F67;"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            Lihat Dashboard
                        </Link>
                        <p v-else class="text-gray-500 text-sm">
                            <Link :href="login()" class="text-blue-600 hover:text-blue-700 font-medium">Masuk</Link> 
                            untuk mengelola brand Anda
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat bubble -->
        <div class="fixed z-50" style="bottom: 20px; right: 20px;">
            <button class="text-white p-3 sm:p-4 rounded-full shadow-lg transition-colors hover:opacity-90" style="background-color: #113F67;">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
            </button>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3'
import { dashboard, login, register } from '@/routes'
import { computed } from 'vue'

const props = defineProps({
  brands: {
    type: Array,
    default: () => []
  }
});

const page = usePage()
const appSettings = computed(() => page.props.appSettings)

// Transform brands data for display
const daftarBrand = computed(() => 
  (props.brands || []).map(brand => ({
    namaBrand: brand.nama_brand,
    namaCV: brand.pemilik,
    logoUrl: brand.logo_path ? `/storage/${brand.logo_path}` : null
  }))
);
</script>
