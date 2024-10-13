import { ref } from 'vue'
import axios from 'axios'

export function useUserSettings() {
  const settings = ref({
    name: '',
    syncTime: 120,
    currency: 'USD'
  })
  const isLoading = ref(false)
  const error = ref(null)

  const MIN_SYNC_TIME = 120
  const MAX_SYNC_TIME = 10080 

  const fetchSettings = async () => {
    isLoading.value = true
    error.value = null
    try {
      const response = await axios.get('/api/user-settings')
      console.log('hello set');
      console.log(response.data);
      settings.value = response.data.data
    } catch (e) {
      error.value = 'Failed to fetch settings'
    } finally {
      isLoading.value = false
    }
  }

  const updateSettings = async (newSettings) => {
    isLoading.value = true
    error.value = null
    try {
      // Ensure syncTime is within allowed range
      if (newSettings.syncTime !== undefined) {
        newSettings.syncTime = Math.max(MIN_SYNC_TIME, Math.min(MAX_SYNC_TIME, Math.round(newSettings.syncTime)))
      }
      const response = await axios.put('/api/user-settings', newSettings)
      settings.value = response.data
    } catch (e) {
      error.value = 'Failed to update settings'
    } finally {
      isLoading.value = false
    }
  }

  const resetSettings = async () => {
    isLoading.value = true
    error.value = null
    try {
      const response = await axios.post('/api/user-settings/reset')
      settings.value = response.data.settings
    } catch (err) {
      error.value = 'Failed to reset user settings'
      console.error(err)
    } finally {
      isLoading.value = false
    }
  }

  return {
    settings,
    isLoading,
    error,
    fetchSettings,
    updateSettings,
    resetSettings,
    MIN_SYNC_TIME,
    MAX_SYNC_TIME
  }
}