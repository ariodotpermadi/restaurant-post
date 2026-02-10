<template>
    <div class="p-6">
        <h2 class="text-2xl font-bold mb-6">Master Makanan</h2>

        <div class="bg-white p-6 rounded-lg shadow mb-8">
            <h3 class="font-bold mb-4">Tambah Menu Baru</h3>
            <form @submit.prevent="addFood" class="flex gap-4 items-end">
                <div class="flex-1">
                    <label class="block text-sm font-bold mb-1"
                        >Nama Makanan</label
                    >
                    <input
                        v-model="form.name"
                        type="text"
                        class="w-full border p-2 rounded"
                        required
                    />
                </div>
                <div class="w-40">
                    <label class="block text-sm font-bold mb-1"
                        >Harga (Rp)</label
                    >
                    <input
                        v-model="form.price"
                        type="number"
                        class="w-full border p-2 rounded"
                        required
                    />
                </div>
                <div class="w-60">
                    <label class="block text-sm font-bold mb-1"
                        >Gambar (Opsional)</label
                    >
                    <input type="file" @change="handleFile" class="text-sm" />
                </div>
                <button
                    type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 font-bold"
                >
                    + Simpan
                </button>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="p-4 text-left">Gambar</th>
                        <th class="p-4 text-left">Nama</th>
                        <th class="p-4 text-left">Harga</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="food in foods"
                        :key="food.id"
                        class="border-b hover:bg-gray-50"
                    >
                        <td class="p-4">
                            <img
                                v-if="food.image"
                                :src="food.image"
                                class="w-12 h-12 object-cover rounded"
                            />
                            <span v-else class="text-gray-400 text-xs"
                                >No Image</span
                            >
                        </td>
                        <td class="p-4 font-bold">{{ food.name }}</td>
                        <td class="p-4">Rp {{ formatPrice(food.price) }}</td>
                        <td class="p-4 text-center">
                            <button
                                @click="deleteFood(food.id)"
                                class="text-red-500 hover:text-red-700 font-bold text-sm"
                            >
                                Hapus
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { useAuthStore } from "../stores/auth";

const authStore = useAuthStore();
const foods = ref([]);
const form = ref({ name: "", price: "", image: null });

const formatPrice = (val) => new Intl.NumberFormat("id-ID").format(val);

const fetchFoods = async () => {
    if (!authStore.token) {
        console.error("Token tidak ditemukan! User belum login?");
        alert("Sesi habis, silakan login ulang.");
        return;
    }

    try {
        axios.defaults.headers.common["Authorization"] =
            `Bearer ${authStore.token}`;

        const res = await axios.get("/api/foods");

        console.log("Response Server:", res.data);

        if (Array.isArray(res.data)) {
            foods.value = res.data;
        } else if (res.data.data && Array.isArray(res.data.data)) {
            foods.value = res.data.data;
        } else {
            console.warn("Format data aneh, tidak bisa dibaca Vue.");
            foods.value = [];
        }
    } catch (error) {
        console.error("Gagal ambil data:", error);
        if (error.response && error.response.status === 401) {
            alert("Tidak ada izin (Unauthorized). Coba Login ulang.");
        }
    }
};
// -------------------------

const handleFile = (e) => {
    form.value.image = e.target.files[0];
};

const addFood = async () => {
    try {
        const formData = new FormData();
        formData.append("name", form.value.name);
        formData.append("price", form.value.price);
        if (form.value.image) formData.append("image", form.value.image);

        await axios.post("/api/foods", formData, {
            headers: { "Content-Type": "multipart/form-data" },
        });

        form.value = { name: "", price: "", image: null };
        alert("Menu berhasil ditambah!");
        fetchFoods();
    } catch (error) {
        alert(
            "Gagal tambah menu: " +
                (error.response?.data?.message || error.message),
        );
        console.error(error);
    }
};

const deleteFood = async (id) => {
    if (!confirm("Hapus menu ini?")) return;
    try {
        await axios.delete(`/api/foods/${id}`);
        fetchFoods();
    } catch (error) {
        alert("Gagal hapus menu");
    }
};

onMounted(fetchFoods);
</script>
