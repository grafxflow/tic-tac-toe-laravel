<script setup>
import Layout from '@/Layouts/AuthenticatedLayout.vue'
import { Link, Head, useForm, usePage } from '@inertiajs/vue3'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import Modal from '@/Components/Modal.vue'
import Pagination from '@/Components/Pagination.vue'
import { ref } from 'vue'

defineProps({
    users: Object,
    user: Object,
})

const confirmingAddGame = ref(false);
const userInvitedId = ref(null);
const userInvitedName = ref(null);
const authUserId = usePage().props.auth.user.id;
const form = useForm({});

const confirmAddGame = (id, name) => {
    userInvitedId.value = id
    userInvitedName.value = name
    confirmingAddGame.value = true
}

const addGame = () => {
    form.post(route('games.store', {
        'user_id': authUserId,
        'user_invited_id': userInvitedId.value }
    ), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onFinish: () => form.reset(),
    })
}

const closeModal = () => {
  confirmingAddGame.value = false
  form.reset();
}
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
                                                    <span class="inline-flex py-3 px-6 w-full justify-between" @click="sort('id')">
                                                        User ID
                                                    </span>
                                                </th>
                                                <th scope="col" class="w-3/12 text-xs font-semibold tracking-wider text-left text-white uppercase">
                                                    <span class="inline-flex py-3 px-6 w-full justify-between" @click="sort('email')">
                                                        Email
                                                    </span>
                                                </th>
                                                <th scope="col" class="w-3/12 text-xs font-semibold tracking-wider text-left text-white uppercase">
                                                    <span class="inline-flex py-3 px-6 w-full justify-between" @click="sort('name')">
                                                        Name
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
                                            <tr v-for="(user, index) in users.data" :key="user.id">
                                                <td class="py-4 px-6 whitespace-nowrap">
                                                    {{ user.id }}
                                                </td>
                                                <td class="py-4 px-6 whitespace-nowrap">
                                                    {{ user.email }}
                                                </td>
                                                <td class="py-4 px-6 whitespace-nowrap">
                                                    {{ user.name }}
                                                </td>
                                                <td class="py-4 px-6 whitespace-nowrap">
                                                    <span v-if="user.game">
                                                        <Link :href="route('games.show', { 'gameId': user.games.id })" class="inline-flex items-center px-4 py-2 bg-indigo-500 border border-gray-300 rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                                            Play Game
                                                        </Link>
                                                    </span>
                                                    <div v-else>
                                                        <PrimaryButton @click="confirmAddGame(user.id, user.name)">
                                                            New Game
                                                        </PrimaryButton>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <Pagination class="mt-6" :links="users.links" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="confirmingAddGame" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Are you sure you want to invite <span class="text-indigo-500 font-bold">{{ userInvitedName }}</span> to play?
                </h2>
                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal">
                        Cancel
                    </SecondaryButton>
                    <PrimaryButton
                    class="ml-3"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="addGame(authUserId, userInvitedId)"
                    >
                        Add Game
                    </PrimaryButton>
                </div>
            </div>
        </Modal>

    </Layout>
</template>
