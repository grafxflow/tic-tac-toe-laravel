<script setup>
import Layout from '@/Layouts/AuthenticatedLayout.vue'
import { Link, Head, useForm, usePage } from '@inertiajs/vue3'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import Modal from '@/Components/Modal.vue'
import Pagination from '@/Components/Pagination.vue'
import { ref } from 'vue'

const page = usePage();

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
                                                        Winning Player
                                                    </span>
                                                </th>
                                                <th scope="col" class="w-3/12 text-xs font-semibold tracking-wider text-left text-white uppercase">
                                                    <span class="inline-flex py-3 px-6 w-full justify-between">
                                                        Created At
                                                    </span>
                                                </th>
                                                <th scope="col" class="w-3/12 text-xs font-semibold tracking-wider text-left text-white uppercase">
                                                    <span class="inline-flex py-3 px-6 w-full justify-between">
                                                        Status
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
                                                    <span v-if="game.hasOwnProperty('winner_player') && game.winner_player !== null && game.winner_player.id == page.props.auth.user.id">
                                                        {{ game.winner_player.email }}
                                                    </span>
                                                </td>
                                                <td class="py-4 px-6 whitespace-nowrap">
                                                    {{ game.created_at }}
                                                </td>
                                                <td class="py-4 px-6 whitespace-nowrap">
                                                    <div v-if="game.hasOwnProperty('winner_player') && game.winner_player !== null && game.winner_player.id === page.props.auth.user.id" class="inline-flex items-center px-4 py-2 bg-green-500 border border-gray-300 rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow-sm">
                                                        <span>
                                                            Won
                                                        </span>
                                                    </div>
                                                    <div v-else class="inline-flex items-center px-4 py-2 bg-gray-400 border border-gray-300 rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow-sm">
                                                        Lost or Draw
                                                    </div>
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
