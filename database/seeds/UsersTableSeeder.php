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
        $user = \App\Models\User::create([
            'name' => 'super admin',
            'role' => 'super_admin',
            'mobile' => '01148721605',
            'email' => 'super_admin@app.com',
            'password' => bcrypt('123456'),
        ]);

        $user->attachRole('super_admin');

    }//end of run

}//end of seeder
