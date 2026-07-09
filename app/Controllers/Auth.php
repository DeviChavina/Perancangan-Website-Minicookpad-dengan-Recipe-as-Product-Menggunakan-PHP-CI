<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        helper('cookpad');
        helper(['form', 'url']);
        $this->userModel = new UserModel();
    }

    /**
     * Show login form
     */
    public function login()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/');
        }

        return view('auth/login', ['title' => 'Login - Mini Cookpad']);
    }

    /**
     * Process login
     */
    public function doLogin()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'Email atau password salah');
        }

        if (!password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Email atau password salah');
        }

        // Set session
        session()->set([
            'logged_in'  => true,
            'user_id'    => $user['id'],
            'user_name'  => $user['name'],
            'user_email' => $user['email'],
            'user_role'  => $user['role'],
        ]);

        return redirect()->to('/')->with('success', 'Selamat datang, ' . $user['name'] . '!');
    }

    /**
     * Show registration form
     */
    public function register()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/');
        }

        return view('auth/register', ['title' => 'Daftar - Mini Cookpad']);
    }

    /**
     * Process registration
     */
    public function doRegister()
    {
        $rules = [
            'name'             => 'required|min_length[2]|max_length[255]',
            'email'            => 'required|valid_email|is_unique[users.email]',
            'password'         => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name'     => $this->request->getPost('name'),
            'email'    => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'     => 'USER_FREE',
        ];

        // Insert + tangkap error validation dari model
        $userId = $this->userModel->insert($data);

        if (!$userId) {
            $modelErrors = $this->userModel->errors();
            if (!empty($modelErrors)) {
                return redirect()->back()->withInput()->with('errors', $modelErrors);
            }
            return redirect()->back()->withInput()->with('error', 'Gagal membuat akun. Silakan coba lagi.');
        }

        // Set session
        session()->set([
            'logged_in'  => true,
            'user_id'    => $userId,
            'user_name'  => $data['name'],
            'user_email' => $data['email'],
            'user_role'  => $data['role'],
        ]);

        return redirect()->to('/')->with('success', 'Akun berhasil dibuat! Selamat datang, ' . $data['name'] . '!');
    }

    /**
     * Logout
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/')->with('success', 'Anda telah berhasil logout');
    }
}