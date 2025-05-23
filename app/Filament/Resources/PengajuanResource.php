<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengajuanResource\Pages;
use App\Models\Pengajuan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Illuminate\Support\Facades\Auth;

class PengajuanResource extends Resource
{
    protected static ?string $model = Pengajuan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('user_id')->default(auth()->id()),
                Hidden::make('unit_id')->default(auth()->user()?->unit_id),
                Hidden::make('sub_unit_id')->default(auth()->user()?->sub_unit_id),
                Hidden::make('cabang_id')->default(auth()->user()?->cabang_id),

                Forms\Components\TextInput::make('judul')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('deskripsi')
                    ->rows(3)
                    ->nullable(),

                Forms\Components\Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'diproses' => 'Diproses',
                        'disetujui' => 'Disetujui',
                        'ditolak' => 'Ditolak',
                    ])
                    ->default('draft')
                    ->required(),

                Forms\Components\Textarea::make('catatan')
                    ->rows(3)
                    ->nullable(),

                FileUpload::make('lampiran_files')
                    ->label('Lampiran Berkas')
                    ->multiple()
                    ->directory('pengajuans')
                    ->enableOpen()
                    ->enableDownload()
                    ->preserveFilenames()
                    ->nullable(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('judul')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('status')->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('Dibuat Oleh')->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'diproses' => 'Diproses',
                        'disetujui' => 'Disetujui',
                        'ditolak' => 'Ditolak',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPengajuans::route('/'),
            'create' => Pages\CreatePengajuan::route('/create'),
            'edit' => Pages\EditPengajuan::route('/{record}/edit'),
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        // Super user: akses penuh
        if ($user->hasRole('super_user')) {
            return parent::getEloquentQuery();
        }

        // Admin Sub Unit: hanya data milik sub_unit
        if ($user->hasRole('admin_sub_unit')) {
            return parent::getEloquentQuery()
                ->where('sub_unit_id', $user->sub_unit_id);
        }

        if ($user->hasAnyRole(['admin_unit', 'analis_unit', 'kepala_unit'])) {
            return parent::getEloquentQuery()
                ->where('unit_id', $user->unit_id)
                ->where('cabang_id', $user->cabang_id);
        }        // Admin/Analis/Kepala Cabang: semua data dalam cabang
        if ($user->hasAnyRole(['admin_cabang', 'analis_cabang', 'kepala_cabang'])) {
            return parent::getEloquentQuery()
                ->where('cabang_id', $user->cabang_id);
        }

        // Role tidak dikenal: kosongkan query
        return parent::getEloquentQuery()->whereRaw('0 = 1');
    }
}
