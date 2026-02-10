import { defineStore } from 'pinia';
import axios from 'axios';
import router from '../router';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        token: localStorage.getItem('token') || null,
        user: JSON.parse(localStorage.getItem('user')) || null,
    }),

    getters: {
        isAuthenticated: (state) => !!state.token,
    },

    actions: {
        async login(email, password) {
            try {
                // Panggil API Backend yang sudah kita buat
                const response = await axios.post('/api/login', {
                    email: email,
                    password: password
                });

                // Simpan Token & User ke State dan LocalStorage
                this.token = response.data.token;
                this.user = response.data.user;

                localStorage.setItem('token', this.token);
                localStorage.setItem('user', JSON.stringify(this.user));

                // Set Header Default untuk request selanjutnya
                axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;

                // Pindah ke Dashboard
                router.push('/dashboard');

            } catch (error) {
                console.error("Login Gagal:", error);
                throw error; // Lempar error agar bisa ditangkap di UI
            }
        },

        logout() {
            this.token = null;
            this.user = null;
            localStorage.removeItem('token');
            localStorage.removeItem('user');

            // Hapus Header
            delete axios.defaults.headers.common['Authorization'];

            router.push('/login');
        }
    }
});
