<script setup lang="ts">
import { SidebarGroup, SidebarGroupContent, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem, SidebarMenuSub, SidebarMenuSubButton, SidebarMenuSubItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { ChevronRight } from 'lucide-vue-next';
import { ref } from 'vue';

interface Props {
    title?: string;
    items: NavItem[];
}

const props = defineProps<Props>();

const expandedItems = ref<Set<string>>(new Set());

const toggleExpanded = (title: string) => {
    if (expandedItems.value.has(title)) {
        expandedItems.value.delete(title);
    } else {
        expandedItems.value.add(title);
    }
};

const isExpanded = (title: string) => expandedItems.value.has(title);

// Recursive component for nested menu items
const renderMenuItem = (item: NavItem, depth: number = 0) => {
    if (item.items && item.items.length > 0) {
        return {
            hasSubmenu: true,
            item,
            depth
        };
    }
    return {
        hasSubmenu: false,
        item,
        depth
    };
};
</script>

<template>
    <SidebarGroup>
        <SidebarGroupLabel v-if="title">{{ title }}</SidebarGroupLabel>
        <SidebarGroupContent>
            <SidebarMenu>
                <template v-for="item in items" :key="item.title">
                    <SidebarMenuItem>
                        <!-- Item with submenu -->
                        <template v-if="item.items && item.items.length > 0">
                            <SidebarMenuButton
                                @click="toggleExpanded(item.title)"
                                class="cursor-pointer"
                                :class="{ 'bg-sidebar-accent': isExpanded(item.title) }"
                            >
                                <component v-if="item.icon" :is="item.icon" class="size-4" />
                                <span>{{ item.title }}</span>
                                <ChevronRight 
                                    class="ml-auto size-4 transition-transform duration-200" 
                                    :class="{ 'rotate-90': isExpanded(item.title) }"
                                />
                            </SidebarMenuButton>
                            
                            <SidebarMenuSub v-if="isExpanded(item.title)" class="ml-4">
                                <template v-for="subItem in item.items" :key="subItem.title">
                                    <SidebarMenuSubItem>
                                        <!-- Nested submenu -->
                                        <template v-if="subItem.items && subItem.items.length > 0">
                                            <SidebarMenuSubButton
                                                @click="toggleExpanded(subItem.title)"
                                                class="cursor-pointer"
                                                :class="{ 'bg-sidebar-accent': isExpanded(subItem.title) }"
                                            >
                                                <component v-if="subItem.icon" :is="subItem.icon" class="size-4" />
                                                <span>{{ subItem.title }}</span>
                                                <ChevronRight 
                                                    class="ml-auto size-4 transition-transform duration-200" 
                                                    :class="{ 'rotate-90': isExpanded(subItem.title) }"
                                                />
                                            </SidebarMenuSubButton>
                                            
                                            <SidebarMenuSub v-if="isExpanded(subItem.title)" class="ml-4">
                                                <SidebarMenuSubItem v-for="nestedItem in subItem.items" :key="nestedItem.title">
                                                    <SidebarMenuSubButton as-child>
                                                        <Link :href="nestedItem.href || '#'">
                                                            <component v-if="nestedItem.icon" :is="nestedItem.icon" class="size-4" />
                                                            <span>{{ nestedItem.title }}</span>
                                                        </Link>
                                                    </SidebarMenuSubButton>
                                                </SidebarMenuSubItem>
                                            </SidebarMenuSub>
                                        </template>
                                        
                                        <!-- Regular submenu item -->
                                        <template v-else>
                                            <SidebarMenuSubButton as-child>
                                                <Link :href="subItem.href || '#'">
                                                    <component v-if="subItem.icon" :is="subItem.icon" class="size-4" />
                                                    <span>{{ subItem.title }}</span>
                                                </Link>
                                            </SidebarMenuSubButton>
                                        </template>
                                    </SidebarMenuSubItem>
                                </template>
                            </SidebarMenuSub>
                        </template>
                        
                        <!-- Regular menu item -->
                        <template v-else>
                            <SidebarMenuButton as-child>
                                <Link :href="item.href || '#'">
                                    <component v-if="item.icon" :is="item.icon" class="size-4" />
                                    <span>{{ item.title }}</span>
                                </Link>
                            </SidebarMenuButton>
                        </template>
                    </SidebarMenuItem>
                </template>
            </SidebarMenu>
        </SidebarGroupContent>
    </SidebarGroup>
</template>
