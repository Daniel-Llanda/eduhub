<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        // \App\Models\User::factory(10)->create();

        \App\Models\Admin::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@gmail.com',
        ]);
       
        \App\Models\User::factory()->create([
            'name' => 'Shajib Milon',
            'email' => 'shajib@gmail.com',
        ]);
        \App\Models\User::factory()->create([
            'name' => 'Ray Mamuad',
            'email' => 'ray@gmail.com',
        ]);
        \App\Models\User::factory()->create([
            'name' => 'Ryan Dador',
            'email' => 'ryan@gmail.com',
        ]);
        \App\Models\Teacher::factory()->create([
            'name' => 'Teacher one',
            'email' => 'teacherone@gmail.com',
        ]);
        \App\Models\Teacher::factory()->create([
            'name' => 'Teacher two',
            'email' => 'teachertwo@gmail.com',
        ]);
        \App\Models\Teacher::factory()->create([
            'name' => 'Teacher three',
            'email' => 'teacherthree@gmail.com',
        ]);
    }
}
