<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Group;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Staff;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Fathin Mubarak',
            'email' => 'fathin.mubarak@gmail.com',
            'password' => bcrypt('fathinif2012'),
        ]);

        $this->call([
            ClassroomAndStudentSeeder::class,
        ]);

        Staff::factory(20)->create();
    }
}
