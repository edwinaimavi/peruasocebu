<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            RoleSeeder::class,
            RanchPermissionSeeder::class,
            OwnerPermissionSeeder::class,
            VeterinarianPermissionSeeder::class,
            BreedPermissionSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Edwin Cigueñas',
            'email' => 'edwin.3acp@gmail.com',
            'password' => bcrypt('12345678'),
        ])->assignRole('Administrador');
        User::factory(3)->create();

        $this->call([
            /*  CategorySeeder::class, */
            /*  PostSeeder::class,
            PageSeeder::class,
            BrandSeeder::class, */
            /* ProductSeeder::class, */

            /* PersonSeeder::class,
            ClientSeeder::class,
            MembershipSeeder::class,
            AttendanceSeeder::class,
            PaymentSeeder::class, */
            /* BranchSeeder::class,
        ClientSeeder::class,
        ClientContactSeeder::class,
        GuarantorSeeder::class,
        VehicleSeeder::class,
        LoanSeeder::class,
        LoanDisbursementSeeder::class,
        LoanPaymentSeeder::class,
        LoanInstallmentSeeder::class,
        CollateralSeeder::class, */
        ]);
    }
}
