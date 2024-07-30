<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    links: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <div class="relative flex justify-center">
        <template v-for="link in links" :key="link.id">
            <Link
                class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-white focus:border-indigo-500 focus:text-indigo-500"
                :class="{ 'bg-gray-200': link.active, '!text-gray-300': !link.url }"
                :href="link.url ?? ''"
                v-html="link.label"
            />
        </template>
    </div>
</template>
