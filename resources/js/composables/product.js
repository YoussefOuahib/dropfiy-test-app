import { ref } from 'vue'
import axios from 'axios'

export function useProducts() {
  const products = ref([])
  const loading = ref(false)
  const error = ref(null)
  const currentProduct = ref(null)
  const showErrorModal = ref(false)

  const fetchProducts = async () => {
    loading.value = true
    try {
      const response = await axios.get('/api/products')
      products.value = response.data.data
    } catch (e) {
      showError('Failed to fetch products')
    } finally {
      loading.value = false
    }
  }

  const addProduct = async (productData) => {
    loading.value = true
    try {
      const response = await axios.post('/api/products', productData)
      await fetchProducts()
      return response.data.data
    } catch (e) {
      showError(e.response?.data?.message || 'Failed to add product')
      return null
    } finally {
      loading.value = false
    }
  }

  const syncProduct = async (productId) => {
    loading.value = true
    try {
      await axios.post(`/api/products/${productId}/sync`)
      await fetchProducts()
    } catch (e) {
      showError('Failed to sync product')
    } finally {
      loading.value = false
    }
  }

  const updateProduct = async (product) => {
    loading.value = true
    try {
      const response = await axios.put(`/api/products/${product.id}`, product)
      if (response.data.success) {
        const updatedProduct = response.data.data
        const productIndex = products.value.findIndex(p => p.id === product.id)
        if (productIndex !== -1) {
          products.value[productIndex] = updatedProduct
          products.value = [...products.value] // Trigger reactivity
        }
        if (currentProduct.value && currentProduct.value.id === product.id) {
          currentProduct.value = updatedProduct
        }
        return updatedProduct
      } else {
        showError(response.data.message)
        return null
      }
    } catch (e) {
      showError(e.response?.data?.message || 'Failed to update product')
      return null
    } finally {
      loading.value = false
    }
  }

  const deleteProduct = async (productId) => {
    loading.value = true
    try {
      await axios.delete(`/api/products/${productId}`)
      await fetchProducts()
    } catch (e) {
      showError('Failed to delete product')
    } finally {
      loading.value = false
    }
  }

  const getProductWithFeeds = async (productId) => {
    loading.value = true
    try {
      const response = await axios.get(`/api/products/${productId}`)
      currentProduct.value = response.data.data
      return currentProduct.value
    } catch (e) {
      showError('Failed to fetch product details')
      return null
    } finally {
      loading.value = false
    }
  }

  const detachFeed = async (productId, feedId) => {
    loading.value = true
    try {
      await axios.post(`/api/products/${productId}/detach-feed`, { feed_id: feedId })
      if (currentProduct.value && currentProduct.value.id === productId) {
        currentProduct.value.feeds = currentProduct.value.feeds.filter(feed => feed.id !== feedId)
      }
      await fetchProducts()
    } catch (e) {
      showError('Failed to detach feed from product')
    } finally {
      loading.value = false
    }
  }

  const showError = (message) => {
    error.value = message
    showErrorModal.value = true
  }

  const closeErrorModal = () => {
    showErrorModal.value = false
    error.value = null
  }

  return {
    products,
    loading,
    error,
    currentProduct,
    showErrorModal,
    updateProduct,
    fetchProducts,
    addProduct,
    syncProduct,
    deleteProduct,
    getProductWithFeeds,
    detachFeed,
    closeErrorModal
  }
}