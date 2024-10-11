import { ref } from 'vue'
import axios from 'axios'

export function useProducts() {
  const products = ref([])
  const loading = ref(false)
  const error = ref(null)

  const fetchProducts = async () => {
    try {
      const response = await axios.get('/api/products')
      products.value = response.data.data
    } catch (e) {
      error.value = 'Failed to fetch products'
      console.error(e)
    }
  }



  return {
    products,
    fetchProducts,
    loading,
    error,
  }
}