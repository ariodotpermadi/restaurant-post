<template>
    <div class="flex h-screen bg-gray-100">
        <div class="flex-1 p-6 overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <button @click="$router.push('/dashboard')" class="text-gray-600 hover:text-gray-900 flex items-center gap-2">
                    ⬅ Kembali ke Dashboard
                </button>
                <h2 class="text-2xl font-bold text-gray-800">Meja {{ tableId }}</h2>
            </div>

            <div v-if="loading" class="text-center mt-10">Memuat Menu...</div>

            <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <div
                    v-for="food in foods"
                    :key="food.id"
                    @click="addToCart(food)"
                    class="bg-white p-4 rounded-xl shadow cursor-pointer hover:ring-2 hover:ring-blue-500 transition"
                >
                    <div class="h-32 bg-gray-200 rounded-md mb-3 flex items-center justify-center text-gray-400">
                        <span v-if="!food.image">🍔 No Image</span>
                        <img v-else :src="food.image" class="h-full w-full object-cover rounded-md">
                    </div>
                    <h3 class="font-bold text-gray-800">{{ food.name }}</h3>
                    <p class="text-blue-600 font-bold">Rp {{ formatPrice(food.price) }}</p>
                </div>
            </div>
        </div>

        <div class="w-96 bg-white shadow-2xl p-6 flex flex-col h-full border-l">
            <h2 class="text-xl font-bold mb-4 border-b pb-2">Rincian Pesanan</h2>

            <div class="flex-1 overflow-y-auto space-y-4 pr-2">

                <div v-if="existingItems.length > 0">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Sudah Masuk Dapur</h3>
                    <div v-for="(item, index) in existingItems" :key="'exist-'+index" class="flex justify-between items-center bg-gray-50 p-2 rounded mb-1 border border-gray-200">
                        <div>
                            <h4 class="font-bold text-gray-600 text-sm">{{ item.food.name }}</h4>
                            <div class="text-xs text-gray-500">
                                {{ item.quantity }} x Rp {{ formatPrice(item.price) }}
                            </div>
                        </div>
                        <div class="text-gray-600 font-bold text-sm">
                            Rp {{ formatPrice(item.price * item.quantity) }}
                        </div>
                    </div>
                    <hr class="my-4 border-dashed">
                </div>

                <div v-if="cart.length > 0">
                    <h3 class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-2">Pesanan Baru (Draft)</h3>
                    <div v-for="(item, index) in cart" :key="'new-'+index" class="flex justify-between items-center bg-blue-50 p-2 rounded mb-2 border border-blue-200">
                        <div>
                            <h4 class="font-bold text-gray-800">{{ item.name }}</h4>
                            <div class="text-sm text-gray-600">
                                Rp {{ formatPrice(item.price) }} x
                                <input
                                    type="number"
                                    v-model="item.qty"
                                    min="1"
                                    class="w-10 border rounded text-center ml-1 bg-white"
                                >
                            </div>
                        </div>
                        <button @click="removeFromCart(index)" class="text-red-500 hover:text-red-700 bg-white rounded-full p-1 shadow-sm">
                            🗑️
                        </button>
                    </div>
                </div>

                <div v-if="cart.length === 0 && existingItems.length === 0" class="text-center text-gray-400 mt-10">
                    Keranjang Kosong.
                </div>
            </div>

            <div class="mt-4 border-t pt-4 bg-white">
                <div class="flex justify-between text-lg font-bold mb-4">
                    <span>Total Tagihan:</span>
                    <span class="text-blue-700">Rp {{ formatPrice(grandTotal) }}</span>
                </div>

                <button
                    v-if="cart.length > 0"
                    @click="submitOrder"
                    :disabled="processing"
                    class="w-full py-3 mb-3 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 disabled:bg-gray-400 transition shadow-lg transform hover:-translate-y-1"
                >
                    {{ processing ? 'Mengirim...' : `+ Tambah Pesanan (${cart.length} item)` }}
                </button>

                <button
                    v-if="orderId"
                    @click="checkoutOrder"
                    class="w-full py-3 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700 transition"
                    :class="cart.length > 0 ? 'opacity-50 cursor-not-allowed' : ''"
                    :disabled="cart.length > 0"
                >
                    {{ cart.length > 0 ? 'Kirim pesanan baru dulu' : '💰 Bayar & Selesai' }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import { useAuthStore } from '../stores/auth';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();

const tableId = route.params.id;
const foods = ref([]);
const existingItems = ref([]);
const cart = ref([]);
const loading = ref(true);
const processing = ref(false);
const orderId = ref(null);

const formatPrice = (value) => new Intl.NumberFormat('id-ID').format(value);


const fetchFoods = async () => {
    try {
        axios.defaults.headers.common['Authorization'] = `Bearer ${authStore.token}`;
        const response = await axios.get('/api/foods');
        foods.value = Array.isArray(response.data) ? response.data : response.data.data;
    } catch (error) {
        console.error("Gagal ambil menu:", error);
    } finally {
        loading.value = false;
    }
};

const grandTotal = computed(() => {
    const totalExisting = existingItems.value.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const totalNew = cart.value.reduce((sum, item) => sum + (item.price * item.qty), 0);
    return totalExisting + totalNew;
});

const addToCart = (food) => {
    const existingInCart = cart.value.find(item => item.id === food.id);
    if (existingInCart) {
        existingInCart.qty++;
    } else {
        cart.value.push({ ...food, qty: 1 });
    }
};

const removeFromCart = (index) => {
    cart.value.splice(index, 1);
};

const refreshOrderState = async () => {
    try {
        const res = await axios.get(`/api/tables/${tableId}/current-order`);
        if (res.data && res.data.data) {
            const data = res.data.data;
            orderId.value = data.id;
            existingItems.value = data.items;
            console.log("Data pesanan lama ter-load:", existingItems.value);
        }
    } catch (error) {
        console.log("Belum ada pesanan aktif.");
    }
};

const submitOrder = async () => {
    if (!confirm('Kirim pesanan tambahan ini ke dapur?')) return;
    processing.value = true;

    try {
        if (!orderId.value) {
            const openResponse = await axios.post('/api/orders/open', { table_id: tableId });
            orderId.value = openResponse.data.id;
        }

        for (const item of cart.value) {
            await axios.post(`/api/orders/${orderId.value}/add-item`, {
                food_id: item.id,
                quantity: item.qty,
                price: item.price
            });
        }

        cart.value = [];

        await refreshOrderState();

        alert('Pesanan tambahan berhasil masuk!');

    } catch (error) {
        console.error(error);
        alert('Gagal kirim pesanan.');
    } finally {
        processing.value = false;
    }
};

// BAYAR
const checkoutOrder = async () => {
    if (!confirm('Selesaikan pembayaran dan kosongkan meja?')) return;
    try {
        await axios.post(`/api/orders/${orderId.value}/close`);
        window.open(`/api/orders/${orderId.value}/print`, '_blank');
        router.push('/dashboard');
    } catch (error) {
        alert('Gagal bayar.');
    }
};

onMounted(async () => {
    await fetchFoods();
    await refreshOrderState();
});
</script>
