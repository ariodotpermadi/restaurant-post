<template>
    <div class="flex items-center justify-center min-h-screen bg-gray-100">
        <div class="w-full max-w-md p-8 bg-white rounded-lg shadow-lg">
            <h1 class="mb-6 text-2xl font-bold text-center text-gray-800">
                Resto POS Login
            </h1>

            <form @submit.prevent="handleLogin">
                <div class="mb-4">
                    <label class="block mb-2 text-sm font-bold text-gray-700">Email</label>
                    <input
                        v-model="email"
                        type="email"
                        class="w-full px-3 py-2 border rounded shadow focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="pelayan@test.com"
                        required
                    >
                </div>

                <div class="mb-6">
                    <label class="block mb-2 text-sm font-bold text-gray-700">Password</label>
                    <input
                        v-model="password"
                        type="password"
                        class="w-full px-3 py-2 border rounded shadow focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="********"
                        required
                    >
                </div>

                <button
                    type="submit"
                    class="w-full px-4 py-2 font-bold text-white bg-blue-600 rounded hover:bg-blue-700 focus:outline-none transition duration-200"
                    :disabled="loading"
                >
                    {{ loading ? 'Memproses...' : 'Masuk' }}
                </button>

                <p v-if="errorMessage" class="mt-4 text-sm text-center text-red-500 bg-red-100 p-2 rounded">
                    {{ errorMessage }}
                </p>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useAuthStore } from '../stores/auth';

const authStore = useAuthStore();
const email = ref('');
const password = ref('');
const errorMessage = ref('');
const loading = ref(false);

const handleLogin = async () => {
    loading.value = true;
    errorMessage.value = '';

    try {
        await authStore.login(email.value, password.value);
    } catch (error) {
        errorMessage.value = error.response?.data?.message || 'Email atau Password salah!';
    } finally {
        loading.value = false;
    }
};
</script>
