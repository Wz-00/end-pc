<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Auth extends Seeder
{
    public function run()
    {
        //buatkan seeder untuk akun admin dan dimasukkan ke tabel users

        $data = [
            'name'     => 'Admin',
            'username' => 'admin',
            'email'    => 'admin@gmail.com',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'role'     => 'admin',
        ];
        $this->db->table('users')->insert($data);
    }
}
