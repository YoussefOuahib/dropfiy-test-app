<template>
    <dialog
        ref="dialogRef"
        class="w-full max-w-4xl p-0 rounded-lg shadow-2xl bg-white overflow-hidden"
    >
        <div v-if="product" class="flex flex-col h-full">
            <!-- Modal Header -->
            <div
                class="bg-gradient-to-r from-sky-600 to-cyan-600 p-6 text-white"
            >
                <h2 class="text-3xl font-bold mb-2">{{ product.name }}</h2>
                <div class="flex space-x-4 text-sm">
                    <p>
                        <span class="font-semibold">SKU:</span>
                        {{ product.sku }}
                    </p>
                    <p>
                        <span class="font-semibold">Price:</span>
                        {{ product.price }}
                    </p>
                    <p>
                        <span class="font-semibold">Inventory:</span>
                        {{ product.inventory }}
                    </p>
                    <p>
                        <span class="font-semibold">Status:</span>
                        {{ product.is_active ? "Active" : "Inactive" }}
                    </p>
                </div>
            </div>

            <!-- Product Details -->
            <div class="flex-grow overflow-hidden p-6">
                <h3 class="text-xl font-semibold mb-4">Description</h3>
                <p class="text-gray-700">{{ product.description }}</p>

                <h3 class="text-xl font-semibold mt-6 mb-4">
                    Associated Feeds
                </h3>

                <div class="overflow-y-auto max-h-[calc(100vh-20rem)]">
                    <table class="w-full text-sm">
                        <thead>
                            <tr
                                class="bg-gray-50 text-gray-600 uppercase text-xs"
                            >
                                <th
                                    class="sticky top-0 p-3 text-left bg-gray-50"
                                >
                                    Feed Name
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
                                v-for="feed in product.feeds"
                                :key="feed.slug"
                                class="border-b border-gray-100 hover:bg-gray-50 transition-colors duration-150"
                            >
                                <td class="p-3">{{ feed.name }}</td>
                                <td class="p-3">
                                    <OutlinedButton
                                        @click="
                                            $emit(
                                                'detach-feed',
                                                product.id,
                                                feed.id
                                            )
                                        "
                                        color="red"
                                        text="Detach"
                                        class="ml-2"
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
    </dialog>
</template>

<script setup>
import { ref } from "vue";
import OutlinedButton from "../buttons/OutlinedButton.vue";

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(["close", "detach-feed"]);

const dialogRef = ref(null);

const open = () => {
    if (dialogRef.value && !dialogRef.value.open) {
        dialogRef.value.showModal();
    }
};

const close = () => {
    if (dialogRef.value && dialogRef.value.open) {
        dialogRef.value.close();
        emit('close');
    }
};

defineExpose({ open, close });
</script>