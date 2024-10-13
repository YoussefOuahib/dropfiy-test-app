<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserSetting;
use App\Http\Resources\UserSettingResource;
use Illuminate\Support\Facades\DB;

class UserSettingService
{
    /**
     * Update user settings
     *
     * @param array $data
     * @return array
     */
    public function updateSettings(array $data): UserSettingResource
    {
        return DB::transaction(function () use ($data) {
            $user = auth()->user();
            $userSetting = $user->userSetting;

            if (!$userSetting) {
                $userSetting = new UserSetting(['user_id' => $user->id]);
            }
            
            // Update user's name if provided
            if (isset($data['name'])) {
                $user->name = $data['name'];
                $user->save();
            }
            if (isset($data['syncTime'])) {
                $userSetting->sync_time = $data['syncTime'];
            }
            if (isset($data['currency'])) {
                $userSetting->currency = $data['currency'];
            }

            $settingData = array_diff_key($data, array_flip(['name']));
            
            $userSetting->fill($settingData);
            $userSetting->save();

            return new UserSettingResource($userSetting);
        });
    }
    /**
     * Reset user settings to default values
     *
     * @return UserSettingResource
     */
    public function resetToDefault(): UserSettingResource
    {
        $user = auth()->user();
        return DB::transaction(function () use ($user) {
            $userSetting = $user->userSetting;
            if (!$userSetting) {
                $userSetting = new UserSetting(['user_id' => $user->id]);
            }
            
            $userSetting->fill($this->getDefaultSettings());
            $userSetting->save();

            return new UserSettingResource($userSetting);
        });
    }

    /**
     * Get default settings
     *
     * @return array
     */
    private function getDefaultSettings(): array
    {
        return [
            'currency' => 'USD',
            'sync_interval' => 120, // 2 hours in minutes
        ];
    }

    /**
     * Get user settings
     *
     * @param User $user
     * @return UserSettingResource
     */
    public function getSettings(): UserSettingResource
    {
        $user = auth()->user();
        $userSetting = $user->userSetting;
        if (!$userSetting) {
            $userSetting = $this->resetToDefault()->resource;
        }
        return new UserSettingResource($userSetting);
    }
}