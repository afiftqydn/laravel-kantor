<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Pengajuan;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PengajuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::whereIn('email', function ($query) {
            $query->select('email')
                ->from('users')
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->whereIn('model_has_roles.role_id', function ($q) {
                    $q->select('id')->from('roles')->whereIn('name', ['admin_sub_unit', 'admin_unit']);
                });
        })->get();

        foreach ($users as $user) {
            Pengajuan::create([
                'judul' => 'Pengajuan ' . fake()->words(3, true),
                'deskripsi' => fake()->sentence(10),
                'status' => 'draft',
                'catatan' => null,
                'user_id' => $user->id,
                'cabang_id' => $user->cabang_id,
                'unit_id' => $user->unit_id,
                'sub_unit_id' => $user->sub_unit_id,
            ]);
        }
    }
}
