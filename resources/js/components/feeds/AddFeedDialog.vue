<template>
    <dialog
      ref="dialog"
      class="w-full max-w-md p-6 rounded-lg shadow-2xl bg-white overflow-hidden"
    >
      <h2 class="text-2xl font-bold mb-4 text-gray-800">Add New Feed</h2>
      <form @submit.prevent="handleSubmit" class="space-y-4">
        <div>
          <label
            for="feedName"
            class="block text-sm font-medium text-gray-700"
          >Feed Name</label>
          <input
            id="feedName"
            v-model="feedName"
            type="text"
            required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
          />
        </div>
        <div class="space-y-2">
          <label
            for="products"
            class="block text-sm font-semibold text-gray-700 dark:text-gray-300"
          >
            Select Products
          </label>
          <div class="relative">
            <select
              id="products"
              v-model="selectedProducts"
              multiple
              required
              class="w-full px-4 py-2 text-gray-700 bg-white border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none transition-colors duration-200 ease-in-out"
            >
              <option
                v-for="product in products"
                :key="product.id"
                :value="product.id"
                class="py-2 px-4 hover:bg-gray-100 rounded-lg"
              >
                {{ product.name }}
              </option>
            </select>
          </div>
        </div>
        <div class="flex justify-end space-x-2">
          <button
            type="button"
            @click="close"
            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50"
          >
            Add Feed
          </button>
        </div>
      </form>
    </dialog>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  
  const props = defineProps({
    products: {
      type: Array,
      required: true,
    },
  });
  
  const emit = defineEmits(['add-feed', 'close']);
  
  const dialog = ref(null);
  const feedName = ref('');
  const selectedProducts = ref([]);
  
  const open = () => {
    dialog.value.showModal();
  };
  
  const close = () => {
    dialog.value.close();
    feedName.value = '';
    selectedProducts.value = [];
    emit('close');
  };
  
  const handleSubmit = () => {
    emit('add-feed', {
      name: feedName.value,
      productIds: selectedProducts.value,
    });
    close();
  };
  
  defineExpose({
    open,
    close,
  });
  </script>