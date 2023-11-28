<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class UserAndRoleSeeder extends Seeder
{

    public function __construct()
    {
        $this->run();
    }

    public function run()
    {

        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);

        // user
        $user = User::create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $user->assignRole($adminRole);

        // user
        $user = User::create([
            'name' => 'b2m.gpglobal',
            'email' => 'geir.gylterud@telenorlinx.com',
            'password' => Hash::make('HE1]F+Hcf<37;2'),
        ]);

        $user->assignRole($userRole);


    }
}
