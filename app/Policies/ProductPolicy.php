<?php
namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any products.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view the list of products
    }

    /**
     * Determine whether the user can view the product.
     */
    public function view(User $user, Product $product): bool
    {
        return $user->id === $product->user_id;
    }

    /**
     * Determine whether the user can create products.
     */
    public function create(User $user): bool
    {
        return true; // All authenticated users can create products
    }

    /**
     * Determine whether the user can update the product.
     */
    public function update(User $user, Product $product): bool
    {
        return $user->id === $product->user_id;
    }

    /**
     * Determine whether the user can delete the product.
     */
    public function delete(User $user, Product $product): bool
    {
        return $user->id === $product->user_id;
    }

        /**
     * Determine whether the user can detach the product from a feed.
     */
    public function detachFeed(User $user, Product $product): bool
    {
        // Assuming users can only modify their own feeds
        return $user->id === $product->user_id;
    }

      /**
     * Determine whether the user can sync the product 
     */
    public function sync(User $user, Product $product): bool
    {
        // Assuming users can only modify their own feeds
        return $user->id === $product->user_id;
    }

    /**
     * Determine whether the user can restore the product.
     */
    // public function restore(User $user, Product $product): bool
    // {
    //     return $user->id === $product->user_id;
    // }

    /**
     * Determine whether the user can permanently delete the product.
     */
    public function forceDelete(User $user, Product $product): bool
    {
        return $user->id === $product->user_id;
    }
}