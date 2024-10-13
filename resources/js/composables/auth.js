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
    const password_confirmation = ref('') // Added password confirmation for registration
    const name = ref('') // Added name for registration
    const error = ref('')
    const isLoading = ref(false)

    const login = async () => {
        error.value = "";
        isLoading.value = true;

        try {
            await axios.get("/sanctum/csrf-cookie");

            await axios.post("/login", {
                email: email.value,
                password: password.value,
            });

            setAuthStatus(true);
            window.location.href = "/dashboard";
        } catch (e) {
            if (e.response?.data?.errors) {
                error.value = Object.values(e.response.data.errors).flat().join(" ");
            } else if (e.response?.data?.message) {
                error.value = e.response.data.message;
            } else {
                error.value = "An unexpected error occurred. Please try again.";
            }
        } finally {
            isLoading.value = false;
        }
    };

    const register = async () => {
        error.value = "";
        isLoading.value = true;
        try {
            await axios.get("/sanctum/csrf-cookie");

            await axios.post("/register", {
                name: name.value,
                email: email.value,
                password: password.value,
                password_confirmation: password_confirmation.value,
            });

            setAuthStatus(true);
            window.location.href = "/dashboard";
        } catch (e) {
            if (e.response?.data?.errors) {
                error.value = Object.values(e.response.data.errors).flat().join(" ");
            } else if (e.response?.data?.message) {
                error.value = e.response.data.message;
            } else {
                error.value = "Registration failed. Please try again.";
            }
        } finally {
            isLoading.value = false;
        }
    };

    const logout = async () => {
        try {
            await axios.post('/api/logout');
            setAuthStatus(false);
        } catch (error) {
            console.error('Logout failed:', error);
        }
    };

    const checkAuthStatus = async () => {
        try {
            const response = await axios.get('/api/user');
            if (response.status === 200) {
                setAuthStatus(true);
                return true;
            }
        } catch (error) {
            if (error.response && error.response.status === 401) {
                setAuthStatus(false);
            } else {
                console.error('Error checking auth status:', error);
            }
        }
        return false;
    };

    return {
        isAuthenticated: readonly(isAuthenticated),
        login,
        register,
        name,
        email,
        password,
        password_confirmation,
        error,
        isLoading,
        logout,
        checkAuthStatus,
    };
};
