<template>
    <div class="p-6 bg-gradient-to-r from-cyan-50 to-sky-50">
        <div
            v-if="feeds.length === 0"
            class="flex justify-center items-center h-64"
        >
            <div
                class="text-center p-10 bg-white rounded-lg shadow-lg border border-gray-200"
            >
                <h2 class="text-3xl font-bold text-sky-600 mb-4">
                    You have no feeds yet
                </h2>
                <p class="text-lg text-gray-500 mb-6">
                    Start by adding your first feed to see your data here.
                </p>
                <OutlinedButton
                    text="Add Feed"
                    color="primary"
                    @click="showAddModal = true"
                />
            </div>
        </div>

        <template v-if="feeds.length > 0">
            <div class="flex justify-evenly" v-if="reportData">
                <div>
                    <a
                        href="#"
                        class="block max-w-sm p-6 border border-gray-200 rounded-lg shadow hover:bg-sky-400 bg-gradient-to-r from-sky-600 to-cyan-600"
                    >
                        <h2
                            class="mb-2 font-medium text-xl tracking-tight text-center text-gray-100"
                        >
                            Total Feeds
                        </h2>
                        <p
                            class="text-4xl font-extrabold text-center text-white"
                        >
                            {{ reportData.overall_stats.total_feeds ?? "N/A" }}
                        </p>
                    </a>
                </div>
                <div>
                    <a
                        href="#"
                        class="block max-w-sm p-6 border border-gray-200 rounded-lg shadow hover:bg-green-400 bg-gradient-to-r from-green-400 to-emerald-400"
                    >
                        <h2
                            class="mb-2 text-xl font-medium tracking-tight text-center text-gray-100"
                        >
                            Synced Feeds
                        </h2>
                        <p
                            class="text-4xl font-extrabold text-center text-white"
                        >
                            {{ reportData.overall_stats.synced_feeds ?? "N/A" }}
                        </p>
                    </a>
                </div>
                <div>
                    <a
                        href="#"
                        class="block max-w-sm p-6 border border-gray-200 rounded-lg shadow hover:bg-rose-400 bg-gradient-to-r from-rose-400 to-red-400"
                    >
                        <h2
                            class="mb-2 text-xl font-medium tracking-tight text-center text-gray-100"
                        >
                            Failed Feeds
                        </h2>
                        <p
                            class="text-4xl font-extrabold text-center text-white"
                        >
                            {{ reportData.overall_stats.failed_feeds ?? "N/A" }}
                        </p>
                    </a>
                </div>
                <div>
                    <a
                        href="#"
                        class="block max-w-sm p-6 border border-gray-200 rounded-lg shadow hover:bg-sky-400 bg-gradient-to-r from-sky-600 to-cyan-600"
                    >
                        <h2
                            class="mb-2 text-xl font-medium tracking-tight text-center text-gray-100"
                        >
                            Total Products
                        </h2>
                        <p
                            class="text-4xl font-extrabold text-center text-white"
                        >
                            {{
                                reportData.overall_stats.total_products ?? "N/A"
                            }}
                        </p>
                    </a>
                </div>
            </div>
            <div class="mb-4 mt-4 flex justify-between items-center">
                <h1 class="text-2xl font-bold text-sky-800">Feeds</h1>
                <OutlinedButton
                    text="Add Feed"
                    color="primary"
                    @click="showAddModal = true"
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
                            :key="feed.slug"
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
                                    @click="sync(feed.slug)"
                                />
                                <OutlinedButton
                                    @click="editFeed(feed)"
                                    text="Edit"
                                    color="yellow"
                                />
                                <OutlinedButton
                                    @click="viewFeed(feed.slug)"
                                    text="View"
                                    color="green"
                                />
                                <OutlinedButton
                                    @click="deleteFeed(feed.slug)"
                                    text="Delete"
                                    color="red"
                                />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </template>

        <FeedInfoModal
            :show="showInfoModal"
            :feed="selectedFeed"
            @close="showInfoModal = false"
            @detach-product="detachProduct"
        />
        <AddFeedDialog
            :show="showAddModal"
            @close="showAddModal = false"
            :products="products"
            @add-feed="handleAddFeed"
        />
        <UpdateFeedModal
            :show="showUpdateModal"
            :feed="selectedFeed"
            @close="showUpdateModal = false"
            @update="handleUpdateFeed"
        />
    </div>
</template>
<script setup>
import { onMounted, ref } from "vue";
import { useFeeds } from "../composables/feed";
import { useProducts } from "../composables/product";
import OutlinedButton from "../components/buttons/OutlinedButton.vue";
import AddFeedDialog from "../components/feeds/AddFeedDialog.vue";
import FeedInfoModal from "../components/feeds/FeedInfoModal.vue";
import UpdateFeedModal from "../components/feeds/UpdateFeedModal.vue";

const {
    feeds,
    loading,
    error,
    fetchFeeds,
    syncFeed,
    deleteFeed: deleteFeedComposable,
    getFeedWithProducts,
    addFeed,
    updateFeed,
    detachProduct,
    fetchReportData,
    reportData,
} = useFeeds();
const { products, fetchProducts } = useProducts();
const selectedFeed = ref(null);
const showInfoModal = ref(false);
const showUpdateModal = ref(false);
const showAddModal = ref(false);
onMounted(() => {
    fetchFeeds();
    fetchProducts();
    fetchReportData();
});

const formatDate = (date) => {
    return date ? new Date(date).toLocaleString() : "Never";
};

const sync = async (feedId) => {
    await syncFeed(feedId);
};

const viewFeed = async (feedId) => {
    showInfoModal.value = true;
    selectedFeed.value = await getFeedWithProducts(feedId);
};
const editFeed = (feed) => {
    selectedFeed.value = feed;
    showUpdateModal.value = true;
};

const deleteFeed = async (feedId) => {
    if (confirm("Are you sure you want to delete this feed?")) {
        await deleteFeedComposable(feedId);
        await fetchFeeds();
    }
};

const handleAddFeed = async ({ name, productIds }) => {
    await addFeed(name, productIds);
    await fetchFeeds();
};

const handleUpdateFeed = async ({ id, name }) => {
    await updateFeed(id, name);
    showUpdateModal.value = true;
    await fetchFeeds();
};
</script>
