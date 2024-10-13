<template>
  <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white w-full max-w-md p-6 rounded-lg shadow-2xl">
      <h2 class="text-2xl font-bold mb-4 text-gray-800">Add New Product</h2>
      <form @submit.prevent="handleSubmit" class="space-y-4">
        <div>
          <label for="productName" class="block text-sm font-medium text-gray-700">Product Name</label>
          <input
            id="productName"
            v-model="newProduct.name"
            type="text"
            required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
          />
        </div>
        <div>
          <label for="productSku" class="block text-sm font-medium text-gray-700">SKU</label>
          <input
            id="productSku"
            v-model="newProduct.sku"
            type="text"
            required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
          />
        </div>
        <div>
          <label for="productPrice" class="block text-sm font-medium text-gray-700">Price</label>
          <input
            id="productPrice"
            v-model="newProduct.price"
            type="number"
            step="0.01"
            required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
          />
        </div>
        <div>
          <label for="productInventory" class="block text-sm font-medium text-gray-700">Inventory</label>
          <input
            id="productInventory"
            v-model="newProduct.inventory"
            type="number"
            required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
          />
        </div>
        <div>
          <label for="productDescription" class="block text-sm font-medium text-gray-700">Description</label>
          <textarea
            id="productDescription"
            v-model="newProduct.description"
            rows="3"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
          ></textarea>
        </div>
        <div class="flex justify-end space-x-2">
          <button
            type="button"
            @click="closeModal"
            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50"
          >
            Add Product
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  show: Boolean
});

const newProduct = ref({
  name: "",
  sku: "",
  price: 0,
  inventory: 0,
  description: "",
});

const emit = defineEmits(['add-product', 'close']);

const handleSubmit = () => {
  emit('add-product', newProduct.value);
  closeModal();
};

const closeModal = () => {
  emit('close');
};
</script>