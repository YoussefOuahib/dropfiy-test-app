import { ref } from 'vue'
import axios from 'axios'

export function useFeeds() {
  const feeds = ref([])
  const loading = ref(false)
  const error = ref(null)

  const fetchFeeds = async () => {
    loading.value = true
    try {
      const response = await axios.get('/api/feeds')
      feeds.value = response.data.data
    } catch (e) {
      error.value = 'Failed to fetch feeds'
      console.error(e)
    } finally {
      loading.value = false
    }
  }

  const syncFeed = async (feedId) => {
    try {
      await axios.post(`/api/feeds/${feedId}/sync`)
      await fetchFeeds();
      console.log(feeds.value);
    } catch (e) {
      error.value = 'Failed to generate and submit feed'
      console.error(e)
    }
  }

  const deleteFeed = async (feedId) => {
    try {
      await axios.delete(`/api/feeds/${feedId}`)
      await fetchFeeds()
    } catch (e) {
      error.value = 'Failed to delete feed'
      console.error(e)
    }
  }

  const getFeedWithProducts = async (feedId) => {
    try {
      const response = await axios.get(`/api/feeds/${feedId}`)
      return response.data.data
    } catch (e) {
      error.value = 'Failed to fetch feed details'
      console.error(e)
      return null
    }
  }
  const addFeed = async (feedName, selectedProducts) => {
    try {
      await axios.post('/api/feeds', {
        name: feedName,
        product_ids: selectedProducts
      })
      await fetchFeeds()
    } catch (e) {
      error.value = 'Failed to add feed'
      console.error(e)
    }
  }
  const detachProduct = async (feedId, productId) => {
    loading.value = true
    error.value = null
    try {
      const response = await axios.post(`/api/feeds/${feedId}/detach-product`, { product_id: productId })
      
      if (response.data.success) {
        // Update the local state with the returned feed data
        const updatedFeed = response.data.feed
        const feedIndex = feeds.value.findIndex(feed => feed.id === feedId)
        if (feedIndex !== -1) {
          feeds.value[feedIndex] = updatedFeed
          feeds.value = [...feeds.value] // Trigger reactivity
        }
        return true // Indicate success
      } else {
        error.value = response.data.message
        return false // Indicate failure
      }
    } catch (e) {
      error.value = e.response?.data?.message || 'Failed to detach product from feed'
      console.error(e)
      return false // Indicate failure
    } finally {
      loading.value = false
    }
  }

  return {
    detachProduct,
    addFeed,
    feeds,
    loading,
    error,
    fetchFeeds,
    syncFeed,
    deleteFeed,
    getFeedWithProducts
  }
}