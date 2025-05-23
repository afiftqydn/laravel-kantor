<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Cabang;
use App\Models\Unit;
use App\Models\SubUnit;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Daftar role yang ingin dibuat
        $roles = ['super_user', 'admin_sub_unit', 'admin_unit', 'admin_cabang', 'analis_unit', 'analis_cabang', 'kepala_unit', 'kepala_cabang'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // Ambil cabang default untuk super user
        $defaultCabang = Cabang::first();

        $this->createUser(
            'itwgs@wgs.com', // email baru
            'IT WGS',        // nama baru
            'super_user',
            $defaultCabang ? $defaultCabang->id : null
        );

        // Buat user untuk tiap Cabang
        foreach (Cabang::all() as $cabang) {
            $slug = $this->slugify($cabang->nama);

            $this->createUser("admin.cabang.{$slug}@wgs.com", "Admin Cabang {$cabang->nama}", 'admin_cabang', $cabang->id);
            $this->createUser("analis.cabang.{$slug}@wgs.com", "Analis Cabang {$cabang->nama}", 'analis_cabang', $cabang->id);
            $this->createUser("kepala.cabang.{$slug}@wgs.com", "Kepala Cabang {$cabang->nama}", 'kepala_cabang', $cabang->id);
        }

        // Buat user untuk tiap Unit
        foreach (Unit::all() as $unit) {
            $slug = $this->slugify($unit->nama);

            $this->createUser("admin.unit.{$slug}@wgs.com", "Admin Unit {$unit->nama}", 'admin_unit', $unit->cabang_id, $unit->id);
            $this->createUser("analis.unit.{$slug}@wgs.com", "Analis Unit {$unit->nama}", 'analis_unit', $unit->cabang_id, $unit->id);
            $this->createUser("kepala.unit.{$slug}@wgs.com", "Kepala Unit {$unit->nama}", 'kepala_unit', $unit->cabang_id, $unit->id);
        }

        // Buat user untuk tiap Sub Unit
        foreach (SubUnit::all() as $subUnit) {
            $slug = $this->slugify($subUnit->nama);

            $this->createUser("admin.subunit.{$slug}@wgs.com", "Admin Sub Unit {$subUnit->nama}", 'admin_sub_unit', $subUnit->cabang_id, $subUnit->unit_id, $subUnit->id);
        }
    }

    /**
     * Buat atau update user dengan role dan relasi wilayah
     */
    private function createUser($email, $name, $role, $cabangId = null, $unitId = null, $subUnitId = null)
    {
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'email' => $email,
                'password' => bcrypt('password'),
                'cabang_id' => $cabangId,
                'unit_id' => $unitId,
                'sub_unit_id' => $subUnitId,
            ]
        );

        $user->syncRoles([$role]);
        $this->command->info("User {$email} created with role {$role} and password 'password'");
    }

    /**
     * Ubah string nama menjadi slug lowercase dengan underscore
     */
    private function slugify(string $text): string
    {
        return Str::of($text)
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', '_')
            ->trim('_')
            ->toString();
    }
}
