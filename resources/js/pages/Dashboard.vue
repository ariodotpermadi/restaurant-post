<template>
    <div class="min-h-screen bg-gray-50">
        <nav
            class="bg-white shadow-sm px-6 py-4 flex justify-between items-center sticky top-0 z-50"
        >
            <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                🍽️ Resto POS
            </h1>

            <div class="flex items-center gap-3">
                <router-link
                    to="/food-list"
                    class="px-4 py-2 text-sm font-bold text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition"
                >
                    📂 Master Menu
                </router-link>

                <router-link
                    to="/history"
                    class="px-4 py-2 text-sm font-bold text-gray-600 hover:text-gray-900 bg-gray-100 rounded-lg hover:bg-gray-200 transition"
                >
                    📜 Riwayat
                </router-link>

                <div class="h-6 w-px bg-gray-300 mx-2"></div>
                <span class="text-gray-600 text-sm hidden md:block">
                    Halo, <b>{{ user?.name }}</b>
                </span>

                <button
                    @click="handleLogout"
                    class="px-4 py-2 text-sm text-red-600 border border-red-200 rounded-lg hover:bg-red-50 transition"
                >
                    Logout
                </button>
            </div>
        </nav>

        <main class="p-6 max-w-7xl mx-auto">
            <h2 class="text-2xl font-bold text-gray-700 mb-6">Status Meja</h2>

            <div v-if="loading" class="text-center py-10">
                <p class="text-gray-500">Mengambil data meja...</p>
            </div>

            <div v-else-if="tables.length === 0" class="text-center py-10">
                <p class="text-red-500">Data meja tidak ditemukan.</p>
            </div>

            <div
                v-else
                class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6"
            >
                <div
                    v-for="table in tables"
                    :key="table.id"
                    @click="openTable(table)"
                    class="relative p-6 rounded-xl shadow-md cursor-pointer transition-transform transform hover:scale-105 border-2"
                    :class="
                        table.status === 'available'
                            ? 'bg-white border-green-400'
                            : 'bg-red-50 border-red-400'
                    "
                >
                    <div class="flex justify-between items-start">
                        <span
                            class="text-3xl font-bold"
                            :class="
                                table.status === 'available'
                                    ? 'text-gray-800'
                                    : 'text-red-800'
                            "
                        >
                            {{ table.table_number }}
                        </span>

                        <span
                            class="px-2 py-1 text-xs rounded-full font-bold uppercase"
                            :class="
                                table.status === 'available'
                                    ? 'bg-green-100 text-green-800'
                                    : 'bg-red-100 text-red-800'
                            "
                        >
                            {{ table.status }}
                        </span>
                    </div>

                    <p class="mt-4 text-sm text-gray-500">
                        {{
                            table.status === "available"
                                ? "Klik untuk pesan"
                                : "Sedang melayani..."
                        }}
                    </p>
                </div>
            </div>
        </main>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { useAuthStore } from "../stores/auth";
import axios from "axios";
import { useRouter } from "vue-router";

const authStore = useAuthStore();
const router = useRouter();
const tables = ref([]);
const loading = ref(true);
const user = computed(() => authStore.user);

const handleLogout = () => {
    authStore.logout();
};

const fetchTables = async () => {
    try {
        axios.defaults.headers.common["Authorization"] =
            `Bearer ${authStore.token}`;
        const response = await axios.get("/api/tables");

        console.log("Response API:", response.data);


        if (Array.isArray(response.data)) {
            tables.value = response.data;
        } else if (response.data.data && Array.isArray(response.data.data)) {
            tables.value = response.data.data;
        } else {
            tables.value = [];
        }
    } catch (error) {
        console.error("Gagal ambil meja:", error);
        if (error.response && error.response.status === 401) {
            authStore.logout();
        }
    } finally {
        loading.value = false;
    }
};

const openTable = (table) => {
    router.push(`/order/${table.id}`);
};

onMounted(() => {
    fetchTables();
});
</script>
