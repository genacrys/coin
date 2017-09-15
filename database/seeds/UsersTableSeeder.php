<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Genacrys',
            'email' => 'genacrys@gmail.com',
            'password' => bcrypt('asperitas'),
        ]);
        DB::table('users')->insert([
            'name' => 'Quy Vu',
            'email' => 'libra1310hp@gmail.com',
            'password' => bcrypt('asperitas'),
        ]);
    }
}
