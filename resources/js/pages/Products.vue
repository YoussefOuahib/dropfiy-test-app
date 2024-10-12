<template>
    <div class="p-6 bg-gradient-to-r from-cyan-50 to-sky-50">
        <div class="mb-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-sky-800">Products</h1>
            <OutlinedButton
                text="Add Product"
                color="primary"
                @click="openAddProductModal"
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
                            Product name
                        </th>
                        <th
                            scope="col"
                            class="px-6 py-4 font-semibold tracking-wider"
                        >
                            SKU
                        </th>
                        <th
                            scope="col"
                            class="px-6 py-4 font-semibold tracking-wider"
                        >
                            Price
                        </th>
                        <th
                            scope="col"
                            class="px-6 py-4 font-semibold tracking-wider"
                        >
                            Inventory
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
                            Last Synced
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
                                text="Sync"
                                color="cyan"
                                @click="syncProduct(product.id)"
                            />
                            <OutlinedButton
                                @click="viewProduct(product.id)"
                                text="View"
                                color="green"
                            />
                            <OutlinedButton
                                @click="editProduct(product)"
                                text="Edit"
                                color="blue"
                            />
                            <OutlinedButton
                                @click="deleteProduct(product.id)"
                                text="Delete"
                                color="red"
                            />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <ProductInfoModal
            ref="productInfoModal"
            :product="selectedProduct"
            @close="closeProductInfoModal"
            @detach-feed="handleDetachFeed"
        />
        <AddProductModal
            ref="addProductModal"
            @add-product="handleAddProduct"
            @close="closeAddProductModal"
        />
        <UpdateProductModal
            ref="updateProductModal"
            :product="selectedProduct"
            @update-product="handleUpdateProduct"
            @close="closeUpdateProductModal"
        />
    </div>
</template>


<script setup>
import { onMounted, ref } from "vue";
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
const addProductModal = ref(null);
const productInfoModal = ref(null);
const updateProductModal = ref(null);
const selectedProduct = ref(null);

onMounted(() => {
    fetchProducts();
});

const formatDate = (date) => {
    return date ? new Date(date).toLocaleString() : "Never";
};

const syncProduct = async (productId) => {
    await syncProductComposable(productId);
    await fetchProducts();
};

const openAddProductModal = () => {
    addProductModal.value.open();
};

const closeAddProductModal = () => {
    addProductModal.value.close();
};
const viewProduct = async (productId) => {
    selectedProduct.value = await getProductWithFeeds(productId);
    productInfoModal.value.open();
};

const closeProductInfoModal = () => {
    productInfoModal.value.close();
    selectedProduct.value = null;
};

const editProduct = (product) => {
    selectedProduct.value = product;
    updateProductModal.value.open();
};

const closeUpdateProductModal = () => {
    updateProductModal.value.close();
    selectedProduct.value = null;
};

const deleteProduct = async (productId) => {
    if (confirm("Are you sure you want to delete this product?")) {
        await deleteProductComposable(productId);
        await fetchProducts();
    }
};

const handleAddProduct = async (newProduct) => {
    await addProduct(newProduct);
    await fetchProducts();
};

const handleUpdateProduct = async (productId, updatedProduct) => {
    await updateProduct(productId, updatedProduct);
    await fetchProducts();
    closeUpdateProductModal();
};

const handleDetachFeed = async (productId, feedSlug) => {
    await detachFeed(productId, feedSlug);
    selectedProduct.value = await getProductWithFeeds(productId);
};
</script>