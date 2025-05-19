<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Group;
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

        User::factory()->create([
            'name' => 'Fathin Mubarak',
            'email' => 'fathin.mubarak@gmail.com',
            'password' => bcrypt('fathinif2012'),
        ]);

        // Create some groups first
        if (Group::count() === 0) {
            Group::create(['name' => 'Petoran']);
            Group::create(['name' => 'Pucangsawit 1']);
            Group::create(['name' => 'Sekarpace']);
        }

        $this->call([
            ClassroomAndStudentSeeder::class,
        ]);
    }
}
