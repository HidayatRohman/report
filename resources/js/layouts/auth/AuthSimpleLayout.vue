<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { home } from '@/routes';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps<{
    title?: string;
    description?: string;
}>();

const page = usePage();
const settings = computed(() => {
    // Get appSettings from page props
    const pageSettings = (page.props as any).appSettings;
    return pageSettings || {
        app_name: 'Kelola Pusat',
        logo: null,
        favicon: null
    };
});
</script>

<template>
    <!-- Professional Financial Background with Overlay -->
    <div class="min-h-screen relative overflow-hidden">
        <!-- Financial Background Pattern -->
        <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-950"></div>
        
        <!-- Financial Grid Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="h-full w-full bg-[linear-gradient(to_right,#ffffff_1px,transparent_1px),linear-gradient(to_bottom,#ffffff_1px,transparent_1px)] bg-[size:60px_60px]"></div>
        </div>
        
        <!-- Financial Chart Lines -->
        <div class="absolute inset-0 opacity-5">
            <svg class="w-full h-full" viewBox="0 0 1200 800" preserveAspectRatio="none">
                <path d="M0,400 Q300,200 600,300 T1200,250" stroke="white" stroke-width="2" fill="none"/>
                <path d="M0,500 Q200,300 400,400 T800,350 Q1000,300 1200,400" stroke="white" stroke-width="1.5" fill="none"/>
                <path d="M0,600 Q400,400 800,500 T1200,450" stroke="white" stroke-width="1" fill="none"/>
            </svg>
        </div>
        
        <!-- Floating Financial Icons -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <!-- Dollar Signs -->
            <div class="absolute top-20 left-20 text-white/10 text-6xl animate-float">$</div>
            <div class="absolute top-60 right-32 text-white/5 text-4xl animate-float-delayed">€</div>
            <div class="absolute bottom-40 left-40 text-white/10 text-5xl animate-float-slow">¥</div>
            <div class="absolute bottom-20 right-20 text-white/5 text-3xl animate-float">£</div>
            
            <!-- Financial Chart Bars -->
            <div class="absolute top-40 right-60 flex items-end gap-1 opacity-10">
                <div class="w-2 h-8 bg-white/30 animate-pulse"></div>
                <div class="w-2 h-12 bg-white/30 animate-pulse" style="animation-delay: 0.2s;"></div>
                <div class="w-2 h-6 bg-white/30 animate-pulse" style="animation-delay: 0.4s;"></div>
                <div class="w-2 h-10 bg-white/30 animate-pulse" style="animation-delay: 0.6s;"></div>
            </div>
            
            <!-- Pie Chart -->
            <div class="absolute bottom-60 left-60 w-16 h-16 opacity-5">
                <svg viewBox="0 0 32 32" class="w-full h-full animate-spin-slow">
                    <circle cx="16" cy="16" r="16" fill="none" stroke="white" stroke-width="32" stroke-dasharray="25 75" stroke-dashoffset="25"/>
                    <circle cx="16" cy="16" r="16" fill="none" stroke="white" stroke-width="32" stroke-dasharray="50 50" stroke-dashoffset="0" opacity="0.7"/>
                </svg>
            </div>
        </div>
        
        <!-- Glass Morphism Overlay -->
        <div class="absolute inset-0 bg-black/20 backdrop-blur-[0.5px]"></div>
        
        <!-- Main Content -->
        <div class="relative flex min-h-screen flex-col items-center justify-center gap-6 p-6 md:p-10">
            <div class="w-full max-w-md">
                <!-- Glass Card -->
                <div class="bg-white/95 dark:bg-gray-900/95 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/20 dark:border-gray-800/50 p-8">
                    <div class="flex flex-col gap-8">
                        <!-- Logo Section -->
                        <div class="flex flex-col items-center gap-6">
                            <Link :href="home()" class="flex flex-col items-center gap-4 font-medium group">
                                <!-- Logo with Background -->
                                <div class="relative">
                                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl blur-lg opacity-30 group-hover:opacity-50 transition-opacity"></div>
                                    <div class="relative flex h-16 w-16 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-xl">
                                        <!-- Custom Logo if available, otherwise default with initials -->
                                        <img 
                                            v-if="settings?.logo" 
                                            :src="`/storage/${settings.logo}`" 
                                            alt="Company Logo"
                                            class="h-12 w-12 object-contain rounded-lg"
                                        />
                                        <!-- Show initials based on app name -->
                                        <div 
                                            v-else 
                                            class="h-10 w-10 flex items-center justify-center text-white font-bold text-lg"
                                        >
                                            {{ settings?.app_name ? 
                                                settings.app_name.split(' ').map(word => word.charAt(0)).join('').substring(0, 2).toUpperCase() : 
                                                'KP' 
                                            }}
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Company Name -->
                                <div class="text-center">
                                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ settings?.app_name || 'Kelola Pusat' }}</h1>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Professional Business Management</p>
                                </div>
                            </Link>
                            
                            <!-- Page Title -->
                            <div class="space-y-2 text-center">
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ title }}</h2>
                                <p class="text-center text-sm text-gray-600 dark:text-gray-400">{{ description }}</p>
                            </div>
                        </div>
                        
                        <!-- Form Content -->
                        <div class="space-y-6">
                            <slot />
                        </div>
                    </div>
                </div>
                
                <!-- Bottom Branding -->
                <div class="mt-6 text-center">
                    <p class="text-gray-500" style="font-size: 8px;">
                        Powered by CTO Bikincabang Enterprise
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

@keyframes float-delayed {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-15px); }
}

@keyframes float-slow {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

@keyframes spin-slow {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.animate-float {
    animation: float 6s ease-in-out infinite;
}

.animate-float-delayed {
    animation: float-delayed 8s ease-in-out infinite;
    animation-delay: 2s;
}

.animate-float-slow {
    animation: float-slow 10s ease-in-out infinite;
    animation-delay: 4s;
}

.animate-spin-slow {
    animation: spin-slow 20s linear infinite;
}
</style>
