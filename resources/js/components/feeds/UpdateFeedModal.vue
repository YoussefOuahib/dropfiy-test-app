<template>
      <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white w-full max-w-md p-6 rounded-lg shadow-2xl">
      <h2 class="text-2xl font-bold mb-4">Update Feed</h2>
      <form @submit.prevent="updateFeed">
        <div class="mb-4">
          <label for="feedName" class="block text-sm font-medium text-gray-700">Feed Name</label>
          <input
            id="feedName"
            v-model="updatedFeedName"
            type="text"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            required
          />
        </div>
        <div class="flex justify-end space-x-2">
          <button
            type="button"
            @click="closeModal"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Update
          </button>
        </div>
      </form>
    </div>
    </div>
  </template>
  
  <script setup>
  import { ref, watch } from 'vue';
  
  const props = defineProps({
    show: Boolean,
    feed: {
      type: Object,
      required: true
    }
  });
  
  const emit = defineEmits(['update', 'close']);
  
  const updatedFeedName = ref('');
  
  watch(() => props.feed, (newFeed) => {
    if (newFeed) {
      updatedFeedName.value = newFeed.name;
    }
  }, { immediate: true });
  

  
  const closeModal = () => {
    emit('close');
  };
  
  const updateFeed = () => {
    emit('update', {
      id: props.feed.id,
      name: updatedFeedName.value
    });
    closeModal();
  };
  
  </script>