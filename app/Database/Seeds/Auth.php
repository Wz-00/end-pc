<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\User as UserModel;

class Auth extends Seeder
{
    public function run()
    {
        $userModel = new UserModel();
        $userModel ->save([
            'name'     => 'admin',
            'username' => 'admin',
            'email'    => 'admin@gmail.com',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'role'     => 'admin',
        ]);
    }
}
