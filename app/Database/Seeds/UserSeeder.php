<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'email'    => 'admin@asiafarma.com',
            'password' => password_hash('admin123', PASSWORD_BCRYPT),
            'nama'     => 'Administrator Sistem'
        ];

        // Menyimpan data ke dalam tabel users
        $this->db->table('users')->insert($data);
    }
}