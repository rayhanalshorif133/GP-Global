<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        new UserAndRoleSeeder();
        new ServiceSeeder();
        new ProductSeeder();
        new ServiceProviderInfoSeeder();
    }
}
