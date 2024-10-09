import { createApp } from 'vue'
import router from './router'
import axios from 'axios'

// Configure axios
axios.defaults.withCredentials = true

// Create the Vue application
const app = createApp({})

// Use the router
app.use(router)

// Mount the app to the element with id 'app'
app.mount('#app')