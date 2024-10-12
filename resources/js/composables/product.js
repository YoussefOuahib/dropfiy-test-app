import { ref } from 'vue'
import axios from 'axios'

export function useProducts() {
  const products = ref([])
  const loading = ref(false)
  const error = ref(null)
  const currentProduct = ref(null)

  const fetchProducts = async () => {
    loading.value = true
    try {
      const response = await axios.get('/api/products')
      products.value = response.data.data
    } catch (e) {
      error.value = 'Failed to fetch products'
      console.error(e)
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
      error.value = 'Failed to add product'
      console.error(e)
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
      error.value = 'Failed to sync product'
      console.error(e)
    } finally {
      loading.value = false
    }
  }

  const updateProduct = async (product) => {
    loading.value = true
    error.value = null;
    try {
      const response = await axios.put(`/api/products/${product.id}`, product)
      if (response.data.success) {
        const updatedProduct = response.data.data
        const productIndex = products.value.findIndex(product => product.id === productId)
        if (productIndex !== -1) {
          products.value[productIndex] = updatedProduct
          products.value = [...products.value] // Trigger reactivity
        }
        if (currentProduct.value && currentProduct.value.id === productId) {
          currentProduct.value = updatedProduct
        }
        return updatedProduct
      } else {
        error.value = response.data.message
        return null
      }
    } catch (e) {
      error.value = e.response?.data?.message || 'Failed to update product'
      console.error(e)
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
      error.value = 'Failed to delete product'
      console.error(e)
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
      error.value = 'Failed to fetch product details'
      console.error(e)
    } finally {
      loading.value = false
    }
  }

  const detachFeed = async (productId, feedId) => {
    loading.value = true
    try {
      await axios.post(`/api/products/${productId}/detach-feed`, { feed_id: feedId })
      if (currentProduct.value && currentProduct.value.id === productId) {
        // Update the current product's feeds if it's the one we're viewing
        currentProduct.value.feeds = currentProduct.value.feeds.filter(feed => feed.id !== feedId)
      }
      await fetchProducts()
    } catch (e) {
      error.value = 'Failed to detach feed from product'
      console.error(e)
    } finally {
      loading.value = false
    }
  }

  return {
    products,
    loading,
    error,
    currentProduct,
    updateProduct,
    fetchProducts,
    addProduct,
    syncProduct,
    deleteProduct,
    getProductWithFeeds,
    detachFeed
  }
}