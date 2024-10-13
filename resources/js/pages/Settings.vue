<template>
    <div class="min-h-screen p-8 bg-gradient-to-br from-cyan-50 via-sky-50 to-indigo-50">
      <div class="max-w-2xl mx-auto">
        <div class="mb-8 flex justify-between items-center">
          <h1 class="text-3xl font-bold text-sky-800">User Settings</h1>
        </div>
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
          <form @submit.prevent="saveSettings" class="w-full p-8">
            <div class="mb-6">
              <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name</label>
              <input
                id="name"
                v-model="form.name"
                type="text"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition duration-150 ease-in-out"
                placeholder="Enter your name"
              >
            </div>
            
            <div class="mb-6">
              <label for="syncTime" class="block text-sm font-medium text-gray-700 mb-2">Sync Interval</label>
              <div class="flex items-center space-x-4">
                <div class="flex-grow">
                  <input
                    id="syncTime"
                    v-model.number="form.syncTime"
                    type="number"
                    :min="MIN_SYNC_TIME"
                    :max="MAX_SYNC_TIME"
                    step="60"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition duration-150 ease-in-out"
                  >
                </div>
                <span class="bg-gray-100 px-4 py-2 border border-gray-300 rounded-lg text-gray-500 whitespace-nowrap">minutes</span>
              </div>
              <select
                v-model.number="form.syncTime"
                @change="handlePresetChange"
                class="mt-3 w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition duration-150 ease-in-out"
              >
                <option value="">Custom</option>
                <option :value="120">2 hours</option>
                <option :value="180">3 hours</option>
                <option :value="360">6 hours</option>
                <option :value="720">12 hours</option>
                <option :value="1440">24 hours</option>
                <option :value="2880">48 hours</option>
                <option :value="10080">1 week</option>
              </select>
            </div>
            
            <div class="mb-8">
              <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
              <select
                id="currency"
                v-model="form.currency"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition duration-150 ease-in-out"
              >
                <option value="USD">USD - United States Dollar</option>
                <option value="EUR">EUR - Euro</option>
                <option value="GBP">GBP - British Pound</option>
              </select>
            </div>
            
            <div class="flex justify-end">
              
              
              <OutlinedButton
              text="Reset"
              color="red"
              @click="handleResetSettings"
              />
              <OutlinedButton
              text="Save"
              color="cyan"
              @click="saveSettings"
              />
            </div>
          </form>
        </div>
      </div>
    </div>
  </template>
  

<script setup>
import { onMounted, reactive, watch } from 'vue'
import { useUserSettings } from '@/composables/setting'
import OutlinedButton from '../components/buttons/OutlinedButton.vue';

const { settings, isLoading, resetSettings ,error, fetchSettings, updateSettings, MIN_SYNC_TIME, MAX_SYNC_TIME } = useUserSettings()

const form = reactive({
  name: '',
  syncTime: 120,
  currency: 'USD'
})

onMounted(async () => {
  await fetchSettings()
  Object.assign(form, settings.value)
})


const saveSettings = async () => {
  await updateSettings(form)
}

const handleResetSettings = async () => {
  if (confirm('Are you sure you want to reset your settings to default?')) {
    await resetSettings()
    if (!error.value) {
      Object.assign(form, settings.value)
    
    }
  }
}

const handlePresetChange = (event) => {
  const select = event.target
  if (select.value === '') {
    // If "Custom" is selected, don't change the current value
    select.value = form.syncTime.toString()
  }
}

// Watch for changes in the input field and update the select accordingly
watch(() => form.syncTime, (newValue) => {
  const select = document.querySelector('select')
  const option = Array.from(select.options).find(opt => parseInt(opt.value) === newValue)
  if (option) {
    select.value = option.value
  } else {
    select.value = ''
  }
})
</script>