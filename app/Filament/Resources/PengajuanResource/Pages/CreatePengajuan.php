<?php

namespace App\Filament\Resources\PengajuanResource\Pages;

use App\Filament\Resources\PengajuanResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePengajuan extends CreateRecord
{
    protected static string $resource = PengajuanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['lampiran_files']);
        return $data;
    }

    protected function afterCreate(): void
    {
        $files = $this->form->getState()['lampiran_files'] ?? [];

        foreach ($files as $file) {
            $this->record->addMedia($file)
                ->usingFileName($file->getClientOriginalName())
                ->toMediaCollection('lampiran');
        }
    }
}
