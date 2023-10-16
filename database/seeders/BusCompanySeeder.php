<?php

namespace Database\Seeders;

use App\Models\BusCompany;
use App\Models\Route;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BusCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // BusCompany::factory()->count(5)->create()->each(function ($user) {
        //     User::factory()->create($user);
        // });
        BusCompany::factory()->count(5)->create();

        $arrays = BusCompany::all();

        foreach (BusCompany::all() as $company) {
            $user = User::factory()->create([
                "bus_company_id" => $company->id
            ]);

            Route::factory(6)->create([
                "bus_company_id" => $company->id
            ]);

            $user->assignRole('dashboard-user');
        }
    }
}