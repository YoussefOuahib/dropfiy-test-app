import { createRouter, createWebHistory } from 'vue-router'
import axios from 'axios'

// Import your Vue components
import Login from '../pages/Login.vue'
import Dashboard from '../pages/Dashboard.vue'



const routes = [
    { 
        path: '/', 
        redirect: { name: 'Login' } 
    },
    { 
        path: '/login', 
        component: Login, 
        name: 'Login' 
    },
    // { 
    //     path: '/register', 
    //     component: Register, 
    //     name: 'Register' 
    // },
    { 
        path: '/dashboard', 
        component: Dashboard, 
        name: 'Dashboard', 
        meta: { requiresAuth: true } 
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

router.beforeEach(async (to, from, next) => {
    if (to.meta.requiresAuth) {
        try {
            // Check if user is authenticated
            await axios.get('/api/user')
            next()
        } catch (error) {
            // If not authenticated, redirect to login
            next({ name: 'Login' })
        }
    } else {
        next()
    }
})

export default router