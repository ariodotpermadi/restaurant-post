<template>
    <div class="p-6">
        <h2 class="text-2xl font-bold mb-6">Riwayat Transaksi</h2>

        <div
            class="bg-blue-600 text-white p-6 rounded-lg shadow-lg mb-8 flex justify-between items-center"
        >
            <div>
                <h3 class="text-lg font-semibold">Total Pendapatan (Semua)</h3>
                <p class="text-3xl font-bold">
                    Rp {{ formatPrice(grandTotal) }}
                </p>
            </div>
            <div class="text-4xl">💰</div>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="p-4 text-left">Order ID</th>
                        <th class="p-4 text-left">Tanggal</th>
                        <th class="p-4 text-left">Kasir</th>
                        <th class="p-4 text-left">Total</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="orders.length === 0">
                        <td colspan="5" class="p-8 text-center text-gray-500">
                            Belum ada transaksi selesai.
                        </td>
                    </tr>
                    <tr
                        v-for="order in orders"
                        :key="order.id"
                        class="border-b hover:bg-gray-50"
                    >
                        <td class="p-4 font-bold">#{{ order.id }}</td>
                        <td class="p-4">{{ formatDate(order.created_at) }}</td>
                        <td class="p-4">{{ order.user?.name || "Admin" }}</td>
                        <td class="p-4 font-bold text-green-600">
                            Rp {{ formatPrice(order.total_price) }}
                        </td>
                        <td class="p-4 text-center">
                            <button
                                @click="printReceipt(order.id)"
                                class="bg-gray-800 text-white px-3 py-1 rounded text-sm hover:bg-black transition"
                            >
                                🖨️ Cetak Ulang
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import { useAuthStore } from "../stores/auth";

const authStore = useAuthStore();
const orders = ref([]);

// Format Rupiah
const formatPrice = (val) => new Intl.NumberFormat("id-ID").format(val);

// Format Tanggal (Contoh: 10 Feb 2026 14:30)
const formatDate = (dateString) => {
    const options = {
        day: "numeric",
        month: "short",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    };
    return new Date(dateString).toLocaleDateString("id-ID", options);
};

// Hitung Grand Total dari data yang ada
const grandTotal = computed(() => {
    return orders.value.reduce(
        (sum, order) => sum + Number(order.total_price),
        0,
    );
});

const fetchOrders = async () => {
    try {
        axios.defaults.headers.common["Authorization"] =
            `Bearer ${authStore.token}`;
        const res = await axios.get("/api/orders");
        orders.value = res.data.data;
    } catch (error) {
        console.error("Gagal ambil history:", error);
    }
};

const printReceipt = (id) => {
    // Buka PDF di tab baru
    window.open(`/api/orders/${id}/print`, "_blank");
};

onMounted(fetchOrders);
</script>
