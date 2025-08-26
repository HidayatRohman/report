<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

const appSettings = computed(() => (page.props as any).appSettings || {});
const logoUrl = computed(() => {
  const logo = appSettings.value.logo;
  return logo ? `/storage/${logo}` : null;
});
const appName = computed(() => appSettings.value.app_name || 'Laravel Starter Kit');
</script>

<template>
    <div class="flex aspect-square size-8 items-center justify-center rounded-md bg-sidebar-primary text-sidebar-primary-foreground">
        <img 
            v-if="logoUrl" 
            :src="logoUrl" 
            :alt="appName"
            class="size-7 object-contain rounded-sm"
        />
        <AppLogoIcon v-else class="size-5 fill-current text-white dark:text-black" />
    </div>
    <div class="ml-1 grid flex-1 text-left text-sm">
        <span class="mb-0.5 truncate leading-tight font-semibold">{{ appName }}</span>
    </div>
</template>
