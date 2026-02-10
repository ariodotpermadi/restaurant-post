import { createRouter, createWebHistory } from "vue-router";
import Login from "../pages/Login.vue";
import Dashboard from "../pages/Dashboard.vue";
import Order from "../pages/Order.vue";
import FoodMenu from "../pages/FoodList.vue";
import History from "../pages/History.vue";

const routes = [
    {
        path: "/login",
        name: "Login",
        component: Login,
    },
    {
        path: "/dashboard",
        name: "Dashboard",
        component: Dashboard,
    },
    {
        path: "/order/:id",
        name: "Order",
        component: Order,
        meta: { requiresAuth: true },
    },
    {
        path: "/food-list",
        name: "FoodList",
        component: FoodMenu,
        meta: { requiresAuth: true },
    },
    {
        path: "/history",
        name: "History",
        component: History,
        meta: { requiresAuth: true },
    },
    {
        path: "/",
        redirect: "/login",
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
