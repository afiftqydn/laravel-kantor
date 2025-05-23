<?php

namespace App\Filament\Resources\PengajuanResource\Pages;

use App\Filament\Resources\PengajuanResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePengajuan extends CreateRecord
{
    protected static string $resource = PengajuanResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Hapus data lampiran_files dari data utama agar tidak disimpan ke kolom database
        unset($data['lampiran_files']);
        return $data;
    }

    protected function afterSave(): void
    {
        parent::afterSave();

        $files = $this->form->getState()['lampiran_files'] ?? [];

        foreach ($files as $file) {
            $this->record->addMedia($file)
                ->usingFileName($file->getClientOriginalName())
                ->toMediaCollection('lampiran');
        }
    }
}
