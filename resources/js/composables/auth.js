import { ref, readonly } from 'vue'
import axios from 'axios'

const isAuthenticated = ref(localStorage.getItem('isLogged') === 'true')

export const useAuth = () => {
    const setAuthStatus = (status) => {
        isAuthenticated.value = status
        localStorage.setItem('isLogged', status)
    }
    const email = ref('')
    const password = ref('')
    const error = ref('')
    const isLoading = ref(false)

    const login = async () => {
        error.value = "";
        isLoading.value = true;

        try {
            // Get CSRF cookie
            await axios.get("/sanctum/csrf-cookie");

            // Attempt login
            await axios.post("/login", {
                email: email.value,
                password: password.value,
            });

            // If successful, redirect to dashboard
            setAuthStatus(true);
            window.location.href = "/dashboard"
        } catch (e) {
            if (e.response && e.response.data && e.response.data.errors) {
                // Handle validation errors
                error.value = Object.values(e.response.data.errors)
                    .flat()
                    .join(" ");
            } else if (e.response && e.response.data && e.response.data.message) {
                // Handle other API errors
                error.value = e.response.data.message;
            } else {
                // Handle unexpected errors
                error.value = "An unexpected error occurred. Please try again.";
            }
        } finally {
            isLoading.value = false;
        }
    };

    const logout = async () => {
        try {
            await axios.post('/api/logout')
            setAuthStatus(false)
        } catch (error) {
            console.error('Logout failed:', error)
            throw error
        }
    }

    return {
        isAuthenticated: readonly(isAuthenticated),
        login,
        email,
        password,
        logout
    }
}