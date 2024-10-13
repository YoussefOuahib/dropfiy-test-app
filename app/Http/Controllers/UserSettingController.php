<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserSettingRequest;
use App\Http\Resources\UserSettingResource;
use App\Models\UserSetting;
use App\Services\UserSettingService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\JsonResponse;

class UserSettingController extends Controller
{
    protected UserSettingService $settingsService;

    public function __construct(UserSettingService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    public function index(): UserSettingResource
    {
        Gate::authorize('view', UserSetting::class);
        return $this->settingsService->getSettings();
    }

    public function update(UpdateUserSettingRequest $request): JsonResponse
    {
        Gate::authorize('update', UserSetting::class);
        try {
            $settings = $this->settingsService->updateSettings($request->validated());
            return $this->respondWithSettings($settings, 'Settings updated successfully');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function resetToDefault(): JsonResponse
    {
        Gate::authorize('update', UserSetting::class);
        try {
            $settings = $this->settingsService->resetToDefault();
            return $this->respondWithSettings($settings, 'Settings reset to default successfully');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    protected function respondWithSettings(UserSettingResource $settings, string $message): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'settings' => $settings,
        ]);
    }
}