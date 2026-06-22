<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Pimpinan Pajak',
            'nip'      => 'NIP001',
            'email'    => 'pimpinan@pajak.com',
            'jabatan'  => 'Kepala Bagian Pajak',
            'role'     => 'pimpinan',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name'     => 'Budi Santoso',
            'nip'      => 'NIP002',
            'email'    => 'staff1@pajak.com',
            'jabatan'  => 'Staff Pajak Senior',
            'role'     => 'staff',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name'     => 'Siti Rahayu',
            'nip'      => 'NIP003',
            'email'    => 'staff2@pajak.com',
            'jabatan'  => 'Staff Pajak Junior',
            'role'     => 'staff',
            'password' => Hash::make('password'),
        ]);
    }
}
