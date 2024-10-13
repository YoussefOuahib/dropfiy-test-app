# Dropfiy Test App

## Product Feed Management for Google Merchant Center

This Laravel/Vue 3 project allows users to implement and manage their product feed for Google Merchant Center. It provides functionality to create, update, and delete feeds, as well as manage products within those feeds.

Repository: [https://github.com/YoussefOuahib/dropfiy-test-app](https://github.com/YoussefOuahib/dropfiy-test-app)

## Table of Contents

1. [Requirements](#requirements)
2. [Installation](#installation)
3. [Configuration](#configuration)
4. [Project Structure](#project-structure)
5. [API Endpoints](#api-endpoints)
6. [Services](#services)
7. [Frontend](#frontend)
8. [Usage](#usage)
9. [Troubleshooting](#troubleshooting)

## Requirements

- PHP 8.1+
- Composer
- Node.js 14+
- npm or yarn
- MySQL 5.7+ or PostgreSQL 10+

## Installation

1. Clone the repository:
   ```
   git clone https://github.com/YoussefOuahib/dropfiy-test-app.git
   cd dropfiy-test-app
   ```

2. Install PHP dependencies:
   ```
   composer install
   ```

3. Install JavaScript dependencies:
   ```
   npm install
   ```

4. Copy the `.env.example` file to `.env` and configure your database settings:
   ```
   cp .env.example .env
   ```

5. Generate an application key:
   ```
   php artisan key:generate
   ```

6. Run database migrations:
   ```
   php artisan migrate
   ```

7. Compile frontend assets:
   ```
   npm run dev
   ```

## Project Structure

### Backend

- `app/Services/FeedService.php`: Manages feed-related operations
- `app/Services/ProductService.php`: Manages product-related operations
- `app/Services/UserSettingService.php`: Manages user settings (currency and sync time)
- `app/Services/GoogleMerchantService.php`: Handles integration with Google Merchant Center

### Frontend

- Composables:
  - `feed.js`: Manages feed-related state and actions
  - `product.js`: Manages product-related state and actions
  - `setting.js`: Manages user settings state and actions

- Components:
  - `/pages/dashboard.vue`: Displays feeds
  - `/pages/products.vue`: Manages products
  - `/pages/settings.vue`: Handles user settings

## API Endpoints

All routes are protected by Sanctum authentication.

### Authentication
- `GET /api/user`: Get authenticated user
- `POST /api/logout`: Logout user

### Feeds
- `GET /api/feeds`: List all feeds
- `POST /api/feeds`: Create a new feed
- `GET /api/feeds/{id}`: Get a specific feed
- `PUT /api/feeds/{id}`: Update a feed
- `DELETE /api/feeds/{id}`: Delete a feed
- `GET /api/feeds-report`: Get feed report
- `POST /api/feeds/{feed}/sync`: Sync a feed
- `POST /api/feeds/{feed}/detach-product`: Detach a product from a feed

### Products
- `GET /api/products`: List all products
- `POST /api/products`: Create a new product
- `GET /api/products/{id}`: Get a specific product
- `PUT /api/products/{id}`: Update a product
- `DELETE /api/products/{id}`: Delete a product
- `POST /api/products/{product}/sync`: Sync a product
- `POST /api/products/{product}/detach-feed`: Detach a feed from a product

### User Settings
- `GET /api/user-settings`: Get user settings
- `PUT /api/user-settings`: Update user settings
- `POST /api/user-settings/reset`: Reset user settings to default

## Services

### FeedService

The `FeedService` class provides methods to manage feeds:

- `createFeed(array $data): Feed`
- `updateFeed(Feed $feed, array $data): Feed`
- `deleteFeed(Feed $feed): void`
- `attachProducts(Feed $feed, array $productIds): void`
- `syncProducts(Feed $feed, array $productIds): void`
- `detachProduct(Feed $feed, int $productId): bool`
- `getReportData(): array`

### ProductService

The `ProductService` class manages products and their synchronization with Google Merchant Center:

- `createProduct(array $data): Product`
  - Creates a new product and syncs it with specified feeds.
- `updateProduct(Product $product, array $data): Product`
  - Updates an existing product and syncs it with specified feeds.
- `deleteProduct(Product $product): void`
  - Deletes a product and removes it from associated feeds.
- `syncProduct(Product $product): void`
  - Syncs a product with Google Merchant Center and updates its status.
- `getProductsForFeed(int $feedId): Collection`
  - Retrieves all active products for a specific feed.

The `ProductService` uses a `GoogleMerchantService` for syncing products with Google Merchant Center.

### UserSettingService

The `UserSettingService` class manages user settings:

- `getUserSettings(): UserSetting`
- `updateUserSettings(array $data): UserSetting`
- `resetToDefault(): UserSetting`

## Frontend

The frontend is built with Vue 3 and uses composables for state management.

### Composables

- `feed.js`: Manages feed state and API calls
- `product.js`: Manages product state and API calls
- `setting.js`: Manages user settings state and API calls

### Main Components

- `dashboard.vue`: Displays and manages feeds
- `products.vue`: Displays and manages products
- `settings.vue`: Displays and manages user settings

To start the development server:

```
npm run dev
```

## Usage

### Backend (PHP)

Here's a basic example of how to use the ProductService:

```php
use App\Services\ProductService;

public function __construct(private ProductService $productService) {}

// Create a new product
$productData = [
    'name' => 'New Product',
    'price' => 19.99,
    'description' => 'A new product description',
    'feed_ids' => [1, 2, 3] // IDs of feeds to associate with the product
];
$product = $this->productService->createProduct($productData);

// Update a product
$updatedData = [
    'name' => 'Updated Product Name',
    'price' => 24.99,
    'feed_ids' => [1, 4] // Updated feed associations
];
$updatedProduct = $this->productService->updateProduct($product, $updatedData);

// Sync a product with Google Merchant Center
$this->productService->syncProduct($product);

// Get products for a specific feed
$feedId = 1;
$feedProducts = $this->productService->getProductsForFeed($feedId);

// Delete a product
$this->productService->deleteProduct($product);
```

### Frontend (Vue 3)

Example of using the feed composable in a Vue component:

```vue
<script setup>
import { useFeed } from '@/composables/feed';

const { feeds, fetchFeeds, createFeed } = useFeed();

// Fetch feeds when component mounts
onMounted(fetchFeeds);

// Create a new feed
const newFeed = {
  name: 'New Feed',
  product_ids: [1, 2, 3]
};
createFeed(newFeed);
</script>
```

## Troubleshooting

- If you encounter database-related issues, ensure your database credentials in the `.env` file are correct.
- For API-related problems, check the Laravel log file in `storage/logs/laravel.log`.
- If you face issues with Google Merchant Center integration, verify your API credentials and ensure you have the necessary permissions.
- If feeds are not syncing as expected, check the user settings for the correct sync time configuration.

For more help, please open an issue on the [GitHub repository](https://github.com/YoussefOuahib/dropfiy-test-app/issues).

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.
