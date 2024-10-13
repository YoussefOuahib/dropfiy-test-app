<template>
    <div
        v-if="show"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center"
    >
        <div class="bg-white w-full max-w-2xl p-6 rounded-lg shadow-2xl">
            <div v-if="feed" class="flex flex-col h-full">
                <!-- Modal Header -->
                <div
                    class="bg-gradient-to-r from-sky-600 to-cyan-600 p-6 text-white"
                >
                    <h2 class="text-3xl font-bold mb-2">
                        {{ feed.name }}
                    </h2>
                    <div class="flex space-x-4 text-sm">
                        <p>
                            <span class="font-semibold">Total Products:</span>
                            {{ feed.total_products }}
                        </p>
                        <p>
                            <span class="font-semibold">Last Synced:</span>
                            {{ formatDate(feed.last_synced_at) }}
                        </p>
                        <p>
                            <span class="font-semibold">Status:</span>
                            {{ feed.status }}
                        </p>
                    </div>
                </div>

                <!-- Products Table -->
                <div class="flex-grow overflow-hidden">
                    <h3
                        class="text-xl font-semibold p-4 bg-gray-100 border-b border-gray-200"
                    >
                        Products
                    </h3>
                    <div class="overflow-y-auto max-h-[calc(100vh-20rem)]">
                        <table  class="w-full text-sm">
                            <thead>
                                <tr
                                    class="bg-gray-50 text-gray-600 uppercase text-xs"
                                >
                                    <th
                                        class="sticky top-0 p-3 text-left bg-gray-50"
                                    >
                                        Name
                                    </th>
                                    <th
                                        class="sticky top-0 p-3 text-left bg-gray-50"
                                    >
                                        SKU
                                    </th>
                                    <th
                                        class="sticky top-0 p-3 text-left bg-gray-50"
                                    >
                                        Price
                                    </th>
                                    <th
                                        class="sticky top-0 p-3 text-left bg-gray-50"
                                    >
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="product in feed.products"
                                    :key="product.id"
                                    class="border-b border-gray-100 hover:bg-gray-50 transition-colors duration-150"
                                >
                                    <td class="p-3">{{ product.name }}</td>
                                    <td class="p-3 font-mono text-sm">
                                        {{ product.sku }}
                                    </td>
                                    <td class="p-3">{{ product.price }}</td>
                                    <td class="p-3">
                                        <OutlinedButton
                                            @click="
                                                $emit(
                                                    'detach-product',
                                                    feed.slug,
                                                    product.id
                                                )
                                            "
                                            color="red"
                                            text="Detach"
                                        />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="bg-gray-50 px-6 py-3 flex justify-end">
                    <button
                        @click="close"
                        class="px-3 py-1 mx-2 font-semibold text-red-600 border-2 border-red-600 rounded-lg hover:text-white hover:bg-gradient-to-r hover:from-red-600 hover:to-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 transition-all duration-200 transform hover:scale-105"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import OutlinedButton from "../buttons/OutlinedButton.vue";

const props = defineProps({
    show: Boolean,
    feed: {
        type: Object,
    },
});

const emit = defineEmits(["close", "detach-product"]);

const formatDate = (date) => {
    return date ? new Date(date).toLocaleString() : "Never";
};
const close = () => {
    emit("close");
};
</script>
