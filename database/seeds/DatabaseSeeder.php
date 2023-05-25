<?php

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
        \DB::table('roles')->insert([
            0 => [ 'role'      => 'admin' ],
            1 => [ 'role'      => 'teacher' ],
            2 => [ 'role'      => 'student' ]
        ]);

        \DB::table('users')->insert([
            0 => [ 
                    'role_id' => 1, 
                    'name' => 'Yoselyn Cruz', 
                    'email' => 'yoselynmajestice@gmail.com',
                    'view_password' => '12345', 
                    'password' => bcrypt('12345')
                ]
        ]);

        // \DB::table('types')->insert([
        //     0 => [ 'type'      => 'admin' ],
        //     1 => [ 'type'      => 'teacher' ],
        //     2 => [ 'type'      => 'student' ],
        //     2 => [ 'type'      => 'student' ]
        // ]);
    }
}
