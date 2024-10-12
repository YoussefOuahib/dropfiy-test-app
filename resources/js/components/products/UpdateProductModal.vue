<template>
    <dialog
      ref="dialog"
      class="w-full max-w-md p-6 rounded-lg shadow-2xl bg-white overflow-hidden"
    >
      <h2 class="text-2xl font-bold mb-4 text-gray-800">Update Product</h2>
      <form @submit.prevent="handleSubmit" class="space-y-4">
        <div>
          <label for="productName" class="block text-sm font-medium text-gray-700">Product Name</label>
          <input
            id="productName"
            v-model="updatedProduct.name"
            type="text"
            required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
          />
        </div>
        <div>
          <label for="productSku" class="block text-sm font-medium text-gray-700">SKU</label>
          <input
            id="productSku"
            v-model="updatedProduct.sku"
            type="text"
            required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
          />
        </div>
        <div>
          <label for="productPrice" class="block text-sm font-medium text-gray-700">Price</label>
          <input
            id="productPrice"
            v-model="updatedProduct.price"
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
            v-model="updatedProduct.inventory"
            type="number"
            required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
          />
        </div>
        <div>
          <label for="productDescription" class="block text-sm font-medium text-gray-700">Description</label>
          <textarea
            id="productDescription"
            v-model="updatedProduct.description"
            rows="3"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
          ></textarea>
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
            Update Product
          </button>
        </div>
      </form>
    </dialog>
  </template>
  
  <script setup>
  import { ref, watch } from 'vue';
  
  const props = defineProps({
    product: {
      type: Object,
      required: true
    }
  });
  
  const dialog = ref(null);
  const updatedProduct = ref({ ...props.product });
  
  watch(() => props.product, (newProduct) => {
    updatedProduct.value = { ...newProduct };
  });
  
  const emit = defineEmits(['update-product', 'close']);
  
  const open = () => {
    dialog.value.showModal();
  };
  
  const close = () => {
    dialog.value.close();
    emit('close');
  };
  
  const handleSubmit = () => {
    emit('update-product', updatedProduct.value);
    close();
  };
  
  defineExpose({ open, close });
  </script>