<template>
    <div class="p-6 bg-gradient-to-r from-cyan-50 to-sky-50">
        <div class="mb-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-sky-800">Feeds</h1>
            <OutlinedButton
                text="Add Feed"
                color="gray"
                @click="openAddFeedDialog"
            />
        </div>

        <div class="overflow-x-auto rounded-xl shadow-2xl">
            <table class="w-full text-sm bg-white">
                <thead
                    class="text-xs uppercase bg-gradient-to-r from-sky-600 to-cyan-600 text-white"
                >
                    <tr>
                        <th
                            scope="col"
                            class="px-6 py-4 font-semibold tracking-wider"
                        >
                            Feed name
                        </th>
                        <th
                            scope="col"
                            class="px-6 py-4 font-semibold tracking-wider"
                        >
                            Total Products
                        </th>
                        <th
                            scope="col"
                            class="px-6 py-4 font-semibold tracking-wider"
                        >
                            Last Synced
                        </th>
                        <th
                            scope="col"
                            class="px-6 py-4 font-semibold tracking-wider"
                        >
                            Status
                        </th>
                        <th
                            scope="col"
                            class="px-6 py-4 font-semibold tracking-wider"
                        >
                            Created at
                        </th>
                        <th
                            scope="col"
                            class="px-6 py-4 font-semibold tracking-wider"
                        >
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr
                        v-for="feed in feeds"
                        :key="feed.id"
                        class="hover:bg-gray-50 transition-colors duration-200"
                    >
                        <th
                            scope="row"
                            class="px-6 text-center py-4 font-medium text-gray-900 whitespace-nowrap"
                        >
                            {{ feed.name }}
                        </th>
                        <td class="px-6 text-center py-4 text-gray-600">
                            {{ feed.total_products }}
                        </td>
                        <td class="px-6 py-4 text-center text-gray-600">
                            {{ formatDate(feed.last_synced_at) }}
                        </td>
                        <td
                            class="px-6 py-4 d-flex font-medium text-gray-600 text-center"
                        >
                            <span
                                v-if="feed.status === 'pending'"
                                class="text-yellow-500"
                            >
                                Pending
                            </span>
                            <span
                                v-else-if="feed.status === 'synced'"
                                class="text-green-500"
                            >
                                Synced
                            </span>
                            <span
                                v-else-if="feed.status === 'failed'"
                                class="text-red-500"
                            >
                                Failed
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-600 text-center">
                            {{ formatDate(feed.created_at) }}
                        </td>
                        <td class="px-6 py-4 d-flex text-center">
                            <OutlinedButton
                                text="Sync"
                                color="cyan"
                                @click="sync(feed.id)"
                            />

                            <OutlinedButton
                                @click="viewFeed(feed.id)"
                                text="View"
                                color="green"
                            />
                            <OutlinedButton
                                @click="deleteFeed(feed.id)"
                                text="Delete"
                                color="red"
                            />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <dialog
            ref="feedModal"
            class="w-full max-w-4xl p-0 rounded-lg shadow-2xl bg-white overflow-hidden"
        >
            <div v-if="selectedFeed" class="flex flex-col h-full">
                <!-- Modal Header -->
                <div
                    class="bg-gradient-to-r from-sky-600 to-cyan-600 p-6 text-white"
                >
                    <h2 class="text-3xl font-bold mb-2">
                        {{ selectedFeed.name }}
                    </h2>
                    <div class="flex space-x-4 text-sm">
                        <p>
                            <span class="font-semibold">Total Products:</span>
                            {{ selectedFeed.total_products }}
                        </p>
                        <p>
                            <span class="font-semibold">Last Synced:</span>
                            {{ formatDate(selectedFeed.last_synced_at) }}
                        </p>
                        <p>
                            <span class="font-semibold">Status:</span>
                            {{ selectedFeed.status }}
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
                        <table class="w-full text-sm">
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
                                    v-for="product in selectedFeed.products"
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
                                                detachProduct(
                                                    selectedFeed.id,
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
                        @click="closeFeedModal"
                        class="px-3 py-1 mx-2 font-semibold text-red-600 border-2 border-red-600 rounded-lg hover:text-white hover:bg-gradient-to-r hover:from-red-600 hover:to-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 transition-all duration-200 transform hover:scale-105"
                    >
                        Close
                    </button>
                </div>
            </div>
        </dialog>

        <dialog
            ref="addFeedDialog"
            class="w-full max-w-md p-6 rounded-lg shadow-2xl bg-white overflow-hidden"
        >
            <h2 class="text-2xl font-bold mb-4 text-gray-800">Add New Feed</h2>
            <form @submit.prevent="handleAddFeedSubmit" class="space-y-4">
                <div>
                    <label
                        for="feedName"
                        class="block text-sm font-medium text-gray-700"
                        >Feed Name</label
                    >
                    <input
                        id="feedName"
                        v-model="newFeedName"
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
                        @click="closeAddFeedDialog"
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
    </div>
</template>

<script setup>
import { onMounted, watch } from "vue";
import { useFeeds } from "../composables/feed";
import { useProducts } from "../composables/product";
import { ref } from "vue";
import OutlinedButton from "../components/OutlinedButton.vue";

const {
    feeds,
    loading,
    error,
    fetchFeeds,
    syncFeed,
    deleteFeed: deleteFeedComposable,
    getFeedWithProducts,
    addFeed,
    detachProduct,
} = useFeeds();
const { products, fetchProducts } = useProducts();
const feedModal = ref(null);
const selectedFeed = ref(null);
onMounted(() => {
    fetchFeeds();
    fetchProducts();
});
const addFeedDialog = ref(null);
const newFeedName = ref("");
const selectedProducts = ref([]);

const formatDate = (date) => {
    return date ? new Date(date).toLocaleString() : "Never";
};

const sync = async (feedId) => {
    await syncFeed(feedId);
};

const viewFeed = async (feedId) => {
    selectedFeed.value = await getFeedWithProducts(feedId);
    feedModal.value.showModal();
};

const closeFeedModal = () => {
    feedModal.value.close();
    selectedFeed.value = null;
};
const openAddFeedDialog = () => {
    addFeedDialog.value.showModal();
};

const deleteFeed = async (feedId) => {
    if (confirm("Are you sure you want to delete this feed?")) {
        await deleteFeedComposable(feedId);
        await fetchFeeds();
    }
};
const closeAddFeedDialog = () => {
    addFeedDialog.value.close();
    newFeedName.value = "";
    selectedProducts.value = [];
};

const handleAddFeedSubmit = async () => {
    await addFeed(newFeedName.value, selectedProducts.value);
    await fetchFeeds();
    closeAddFeedDialog();
};
</script>
