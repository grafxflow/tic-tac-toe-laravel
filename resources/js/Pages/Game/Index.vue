<script setup>
import Layout from '@/Layouts/AuthenticatedLayout.vue'
import { Link, Head, useForm } from '@inertiajs/vue3'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import Modal from '@/Components/Modal.vue'
import Pagination from '@/Components/Pagination.vue'
import { ref } from 'vue'

defineProps({
    games: Object,
})
</script>

<template>
    <Layout>
        <Head title="Welcome" />

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-md sm:rounded-lg">
                    <div class="flex flex-col">
                        <div class="overflow-x-auto -my-2 sm:-mx-6 lg:-mx-8">
                            <div class="inline-block py-2 min-w-full align-middle sm:px-6 lg:px-8">
                                <div class="overflow-hidden border-b border-gray-200 shadow sm:rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-200 table-fixed">
                                        <thead class="bg-indigo-500">
                                            <tr>
                                                <th scope="col" class="w-3/12 text-xs font-semibold tracking-wider text-left text-white uppercase">
                                                    <span class="inline-flex py-3 px-6 w-full justify-between">
                                                        Game ID
                                                    </span>
                                                </th>
                                                <th scope="col" class="w-3/12 text-xs font-semibold tracking-wider text-left text-white uppercase">
                                                    <span class="inline-flex py-3 px-6 w-full justify-between">
                                                        Opposing Player
                                                    </span>
                                                </th>
                                                <th scope="col" class="w-3/12 text-xs font-semibold tracking-wider text-left text-white uppercase">
                                                    <span class="inline-flex py-3 px-6 w-full justify-between">
                                                        Created At
                                                    </span>
                                                </th>
                                                <th scope="col" class="w-3/12 text-xs font-semibold tracking-wider text-left text-white uppercase">
                                                    <span class="inline-flex py-3 px-6 w-full justify-between">
                                                        Actions
                                                    </span>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <tr v-for="(game, index) in games.data" :key="game.id">
                                                <td class="py-4 px-6 whitespace-nowrap">
                                                    {{ game.id }}
                                                </td>
                                                <td class="py-4 px-6 whitespace-nowrap">
                                                    {{ game.opposing_player }}
                                                </td>
                                                <td class="py-4 px-6 whitespace-nowrap">
                                                    {{ game.created_at }}
                                                </td>
                                                <td class="py-4 px-6 whitespace-nowrap">
                                                    <Link :href="route('games.show', { 'gameId': game.id })" class="inline-flex items-center px-4 py-2 bg-indigo-500 border border-gray-300 rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                                        Play Game
                                                    </Link>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <Pagination class="mt-6" :links="games.links" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>
