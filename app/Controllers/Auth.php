<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function index()
    {
        // Jika user sudah login, langsung arahkan ke dashboard
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    public function process()
    {
        $session = session();
        $userModel = new UserModel();

        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        // Mencari data user berdasarkan email
        $dataUser = $userModel->where('email', $email)->first();

        if ($dataUser) {
            // Jika email ditemukan, verifikasi password
            $pass_db = $dataUser['password'];
            $verify_pass = password_verify($password, $pass_db);

            if ($verify_pass) {
                // Set data session jika password cocok
                $ses_data = [
                    'id'         => $dataUser['id'],
                    'nama'       => $dataUser['nama'],
                    'email'      => $dataUser['email'],
                    'isLoggedIn' => TRUE
                ];
                $session->set($ses_data);
                
                return redirect()->to('/dashboard');
            } else {
                // Password salah
                $session->setFlashdata('msg', 'Password yang Anda masukkan salah.');
                return redirect()->to('/login');
            }
        } else {
            // Email tidak ditemukan
            $session->setFlashdata('msg', 'Email tidak ditemukan di sistem kami.');
            return redirect()->to('/login');
        }
    }

    public function logout()
    {
        $session = session();
        // Menghancurkan semua data session
        $session->destroy();
        
        return redirect()->to('/login');
    }
}