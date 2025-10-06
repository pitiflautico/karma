<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use App\Models\Setting;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class ListSettings extends EditRecord
{
    protected static string $resource = SettingResource::class;

    protected static ?string $title = 'System Settings';

    public function mount($record = null): void
    {
        // Get the current user's organization
        $user = Auth::user();
        $organizationId = $user->organization_id;

        // Try to find existing settings for this organization
        $setting = Setting::where('organization_id', $organizationId)->first();

        if (!$setting) {
            // Create a new setting for this organization
            $setting = Setting::create([
                'user_id' => $user->id,
                'organization_id' => $organizationId,
                'company_name' => 'My Company',
                'default_currency' => 'EUR',
                'invoice_prefix' => 'INV-',
                'invoice_sequence' => 1,
            ]);
        }

        $this->record = $setting;
        $this->authorizeAccess();

        $this->fillForm();

        $this->previousUrl = url()->previous();
    }

    protected function getHeaderActions(): array
    {
        return [
            // No actions needed for settings page
        ];
    }

    protected function getSavedNotification(): ?\Filament\Notifications\Notification
    {
        return \Filament\Notifications\Notification::make()
            ->success()
            ->title('Settings updated')
            ->body('Your system settings have been saved successfully.');
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
