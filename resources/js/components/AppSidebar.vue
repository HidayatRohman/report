<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavGroup from '@/components/NavGroup.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, Settings, Palette } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

import { FileImage, Calculator } from 'lucide-vue-next';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Input Brand',
        href: '/brand-input',
        icon: FileImage,
    },
    {
        title: 'Input Transaksi',
        href: '/transaksi-input',
        icon: Calculator,
    },
];

const settingsNavItems: NavItem[] = [
    {
        title: 'Settings',
        icon: Settings,
        items: [
            {
                title: 'Appearance',
                icon: Palette,
                items: [
                    {
                        title: 'Pengaturan Logo',
                        href: '/settings/logo',
                    }
                ]
            }
        ]
    }
];

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavGroup title="Main" :items="mainNavItems" />
            <NavGroup :items="settingsNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
