<template>
    <div class="p-6 bg-gradient-to-r from-cyan-50 to-sky-50">
        <div class="mb-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-sky-800">Products</h1>
            <OutlinedButton
                text="Add Product"
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
                            v-for="header in tableHeaders"
                            :key="header"
                            scope="col"
                            class="px-6 py-4 font-semibold tracking-wider"
                        >
                            {{ header }}
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr
                        v-for="product in products"
                        :key="product.id"
                        class="hover:bg-gray-50 transition-colors duration-200"
                    >
                        <th
                            scope="row"
                            class="px-6 text-center py-4 font-medium text-gray-900 whitespace-nowrap"
                        >
                            {{ product.name }}
                        </th>
                        <td class="px-6 text-center py-4 text-gray-600">
                            {{ product.sku }}
                        </td>
                        <td class="px-6 text-center py-4 text-gray-600">
                            {{ product.price }}
                        </td>
                        <td class="px-6 text-center py-4 text-gray-600">
                            {{ product.inventory }}
                        </td>
                        <td
                            class="px-6 py-4 d-flex font-medium text-gray-600 text-center"
                        >
                            <span
                                :class="{
                                    'text-green-500': product.is_active,
                                    'text-red-500': !product.is_active,
                                }"
                            >
                                {{ product.is_active ? "Active" : "Inactive" }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-600 text-center">
                            {{ formatDate(product.last_synced_at) }}
                        </td>
                        <td class="px-6 py-4 d-flex text-center">
                            <OutlinedButton
                                v-for="action in actions"
                                :key="action.text"
                                :text="action.text"
                                :color="action.color"
                                @click="action.handler(product)"
                            />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <AddProductModal
            :show="showAddModal"
            @close="showAddModal = false"
            @add-product="handleAddProduct"
        />

        <ProductInfoModal
            :show="showInfoModal"
            :product="selectedProduct"
            @close="showInfoModal = false"
            @detach-feed="handleDetachFeed"
        />

        <UpdateProductModal
            :show="showUpdateModal"
            :product="selectedProduct"
            @close="showUpdateModal = false"
            @update-product="handleUpdateProduct"
        />
    </div>
</template>

<script setup>
import { onMounted, ref, computed } from "vue";
import { useProducts } from "../composables/product";
import OutlinedButton from "../components/buttons/OutlinedButton.vue";
import AddProductModal from "../components/products/AddProductModal.vue";
import ProductInfoModal from "../components/products/ProductInfoModal.vue";
import UpdateProductModal from "../components/products/UpdateProductModal.vue";

const {
    products,
    loading,
    error,
    fetchProducts,
    syncProduct: syncProductComposable,
    deleteProduct: deleteProductComposable,
    getProductWithFeeds,
    addProduct,
    updateProduct,
    detachFeed,
} = useProducts();

const showAddModal = ref(false);
const showInfoModal = ref(false);
const showUpdateModal = ref(false);
const selectedProduct = ref(null);

onMounted(fetchProducts);

const formatDate = (date) => (date ? new Date(date).toLocaleString() : "Never");

const syncProduct = async (productId) => {
    await syncProductComposable(productId);
    await fetchProducts();
};

const viewProduct = async (product) => {
    showInfoModal.value = true;
    selectedProduct.value = await getProductWithFeeds(product.id);
};

const editProduct = (product) => {
    showUpdateModal.value = true;
    selectedProduct.value = product;
};

const closeUpdateProductModal = () => {
    selectedProduct.value = null;
};

const deleteProduct = async (product) => {
    if (confirm(`Are you sure you want to delete ${product.name}?`)) {
        await deleteProductComposable(product.id);
        await fetchProducts();
    }
};

const handleAddProduct = async (newProduct) => {
    await addProduct(newProduct);
    await fetchProducts();
    showAddModal.value = false;
};

const handleUpdateProduct = async (productId, updatedProduct) => {
    await updateProduct(productId, updatedProduct);
    await fetchProducts();
    closeUpdateProductModal();
};

const handleDetachFeed = async (productId, feedId) => {
    await detachFeed(productId, feedId);
    selectedProduct.value = await getProductWithFeeds(productId);
};

const tableHeaders = [
    "Product name",
    "SKU",
    "Price",
    "Inventory",
    "Status",
    "Last Synced",
    "Actions",
];

const actions = computed(() => [
    {
        text: "Sync",
        color: "cyan",
        handler: (product) => syncProduct(product.id),
    },
    { text: "View", color: "green", handler: viewProduct },
    { text: "Edit", color: "blue", handler: editProduct },
    { text: "Delete", color: "red", handler: deleteProduct },
]);
</script>
