<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            SuperAdminSeeder::class,
            CatalogSeeder::class,
        ]);

        // Sample products/slides only outside production
        if (! app()->isProduction()) {
            $this->call(DemoProductSeeder::class);
        }
    }
}
