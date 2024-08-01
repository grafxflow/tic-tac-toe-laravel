<script setup>
import Layout from '@/Layouts/AuthenticatedLayout.vue'
import { computed, ref, reactive } from 'vue'
import { Head, usePage, useForm } from '@inertiajs/vue3'
import OOutline from '@/Components/Icons/OOutline.vue'
import XOutline from '@/Components/Icons/XOutline.vue'

const page = usePage()
const userVariable = computed(() => page.props.auth.user)
const user = usePage().props.auth.user;
const userInvitedId = usePage().props.auth.user;
const form = useForm({});
const gameId = (new URL(window.location.href)).pathname.split('/')[2];

const checkDisable = (locationChecked, userId, nextTurnUserId) => {
    if(locationChecked || userId != nextTurnUserId) {
        return true
    } else {
        return false
    }
}

window.Echo.channel(`game-over-channel.${gameId}.${user.id}`)
.listen('GameOver', (event) => {
    if(event.result) {
        page.props.result = event.result;
        page.props.winnerId = event.winnerId;
    }
});

window.Echo.channel(`game-channel.${gameId}.${user.id}`)
.listen('Play', (event) => {
    if(user.id === event.userId) {
        page.props.nextTurn.user_id = event.userId;
    }
    page.props.locations = event.locations;
});

const addItem = (locationId, gameId, userId, nextTurnUserId) => {
    form.post(route('games.play', {
        'user_id': userId,
        'user_invited_id': nextTurnUserId,
        'game_id': gameId,
        'location': locationId }
    ), {
        preserveScroll: true,
        onFinish: () => form.reset(),
    })
}

defineProps({
    id: String,
    user: Object,
    nextTurn: Object,
    locations: Object,
    playerType: String,
    otherPlayerId: Number,
    result: String,
    winnerId: Number,
})
</script>

<template>
    <Layout>
        <Head title="Welcome" />
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                    <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                        <header>
                            <h2 class="text-center text-lg font-bold text-gray-900 mb-4">
                                <span v-if="page.props.result === 'draw'">
                                    The Game is a {{ page.props.result }}
                                </span>
                                <span v-else-if="page.props.result === 'win'">
                                    The Game is a Over. {{ user.id == page.props.winnerId ? "You were the winner!" : "Your opponent won!" }}
                                </span>
                                <span v-else>
                                    Game - {{ nextTurn !== null && user.id == nextTurn.user_id ? "You are next" : "Waiting on your opponent..." }}
                                </span>
                            </h2>
                        </header>
                        <section class="h-full px-4 sm:px-6 lg:px-8 rounded-lg">
                            <div class="grid grid-cols-3 gap-5 w-[90%] mx-auto mb-10">
                                <div
                                    v-for="(location, index) in locations" :key="location.id" class="w-full rounded-md"
                                >
                                    <button
                                        :id="location.id"
                                        @click="addItem(index, gameId, user.id, nextTurn !== null ? nextTurn.user_id : null)"
                                        :disabled="checkDisable(location.checked, user.id, nextTurn !== null ? nextTurn.user_id : null)"
                                        class="board-square bg-gray-500 rounded-lg py-6 w-full"
                                    >
                                        <span v-if="location.type === 'x'" class="w-11 h-16">
                                            <XOutline class="mx-auto" />
                                        </span>
                                        <span v-else-if="location.type === 'o'" class="w-11 h-16">
                                            <OOutline class="mx-auto" />
                                        </span>
                                        <span v-else>
                                            <div class="w-11 h-16 mx-auto"></div>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
    </Layout>
</template>
