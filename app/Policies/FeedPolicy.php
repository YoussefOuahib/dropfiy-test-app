<?php

namespace App\Policies;

use App\Models\Feed;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FeedPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any feeds.
     */
    public function viewAny(User $user): bool
    {
        // Assuming all authenticated users can view the list of feeds
        return true;
    }

    /**
     * Determine whether the user can view the feed.
     */
    public function view(User $user, Feed $feed): bool
    {
        // Assuming users can view any feed
        // You might want to restrict this if feeds should be private
        return $user->id === $feed->user_id;
    }

    /**
     * Determine whether the user can create feeds.
     */
    public function create(User $user): bool
    {
        // Assuming all authenticated users can create feeds
        return true;
    }

    /**
     * Determine whether the user can update the feed.
     */
    public function update(User $user, Feed $feed): bool
    {
        // Assuming users can only update their own feeds
        return $user->id === $feed->user_id;
    }

    /**
     * Determine whether the user can delete the feed.
     */
    public function delete(User $user, Feed $feed): bool
    {
        // Assuming users can only delete their own feeds
        return $user->id === $feed->user_id;
    }

    /**
     * Determine whether the user can sync the feed.
     */
    public function sync(User $user, Feed $feed): bool
    {
        // Assuming users can only sync their own feeds
        return $user->id === $feed->user_id;
    }

    /**
     * Determine whether the user can detach a product from the feed.
     */
    public function detachProduct(User $user, Feed $feed): bool
    {
        // Assuming users can only modify their own feeds
        return $user->id === $feed->user_id;
    }

    /**
     * Determine whether the user can view the report.
     */
    public function viewReport(User $user): bool
    {
        // Assuming all authenticated users can view the report
        // You might want to restrict this to certain user roles
        return true;
    }
}