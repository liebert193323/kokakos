<?php

namespace Database\Seeders; // Tambahkan baris ini

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class MoveTenantDataToUserSeeder extends Seeder
{
    public function run()
    {
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            User::create([
                'name' => $tenant->name,
                'email' => $tenant->email,
                'phone' => $tenant->phone,
                'per_month' => $tenant->per_month,
                'price_per_semester' => $tenant->price_per_semester,
                'price_per_year' => $tenant->price_per_year,
                'password' => bcrypt('password'), // Anda bisa menyesuaikan password default
            ]);
        }
    }
}