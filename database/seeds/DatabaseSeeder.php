<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Andrey Alpert',
            'email' => 'andrey.alpert@gmail.com',
            'password' => '$2y$10$GpBkqII4DXpo275Dvhw8ie3aVN/YwS4TpnTcIK5r.8Fp29GyPeNx6',
        ]);
    }
}
