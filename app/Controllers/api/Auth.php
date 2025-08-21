<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\User as UserModel;
use App\Models\Otp as OtpModel;
helper('jwt');
class Auth extends BaseController
{
    // public function getUsers()
    // {
    //     $userModel = new UserModel;
    //     $users = $userModel->findAll();

    //     return $this->response->setJSON([
    //         'status' => true,
    //         'data' => $users
    //     ]);
    // }
    // public function getOtps()
    // {
    //     $otpModel = new OtpModel();
    //     $otps = $otpModel->orderBy('expires_at', 'DESC')->findAll();

    //     return $this->response->setJSON([
    //         'status' => true,
    //         'data' => $otps
    //     ]);
    // }
    public function sendOtp()
    {
        $email = $this->request->getPost('email');
        $otp = rand(100000, 999999);
        $expires = date('Y-m-d H:i:s', time() + 300); // 5 menit
        $otpModel = new OtpModel();

        $otpModel->where('email', $email)->delete();
        $otpModel->insert([
            'email' => $email,
            'otp' => $otp,
            'expires_at' => $expires,
            'type' => 'register'
        ]);

        return $this->response->setJSON([
            'status' => true,
            'message' => 'OTP generated (simulasi)',
            'otp' => $otp // kirim ke frontend (simulasi email)
        ]);
    }

    public function verifyOtp()
    {
        $otpModel = new OtpModel();
        $data = $this->request->getPost();
        $otp = $otpModel
            ->where('email', $data['email'])
            ->where('otp', $data['otp'])
            ->where('expires_at >=', date('Y-m-d H:i:s'))
            ->first();

        if ($otp) {
            return $this->response->setJSON(['status' => true]);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'OTP invalid']);
    }

    public function register()
    {
        $data = $this->request->getPost();
        $userModel = new UserModel();

        // Cek apakah email sudah terdaftar
        if ($userModel->where('email', $data['email'])->first()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Email sudah terdaftar']);
        }

        // Validasi OTP
        if (empty($data['otp'])) {
            return $this->response->setJSON(['status' => false, 'message' => 'OTP wajib diisi']);
        }
        $otpModel = new OtpModel();
        $otp = $otpModel
            ->where('email', $data['email'])
            ->where('otp', $data['otp'])
            ->where('type', 'register')
            ->where('expires_at >=', date('Y-m-d H:i:s'))
            ->first();
        if (!$otp) {
            return $this->response->setJSON(['status' => false, 'message' => 'OTP tidak valid']);
        }

        // Insert user jika OTP valid
        $userModel->insert([
            'email' => $data['email'],
            'username' => $data['username'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT)
        ]);
        return $this->response->setJSON(['status' => true, 'message' => 'Register berhasil']);
    }

    public function login()
    {
        $data = $this->request->getPost();
        $remember = $this->request->getPost('remember') === 'true';
        $userModel = new UserModel();
        $user = $userModel->where('email', $data['email'])->first();

        if (!$user || !password_verify($data['password'], $user['password'])) {
            return $this->response->setJSON(['status' => false, 'message' => 'Login gagal']);
        }

        helper('jwt');
        $token = generateJWT($user['email']);

        return $this->response->setJSON([
            'status' => true,
            'token' => $token,
            'expires_in_days' => $remember ? 30 : 1
        ]);
    }

    public function forgotPassword()
    {
        $email = $this->request->getPost('email');
        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->first();
        if (!$user) {
            return $this->response->setJSON(['status' => false, 'message' => 'Email tidak terdaftar']);
        }

        $otp = rand(100000, 999999);
        $expires = date('Y-m-d H:i:s', time() + 300);
        $otpModel = new OtpModel();

        $otpModel->where('email', $email)->delete();
        $otpModel->insert([
            'email' => $email,
            'otp' => $otp,
            'expires_at' => $expires,
            'type' => 'forgot'
        ]);

        return $this->response->setJSON(['status' => true, 'otp' => $otp]); // simulasi email
    }

    public function verifyForgotOtp()
    {
        $otpModel = new OtpModel();
        $data = $this->request->getPost();
        $otp = $otpModel
            ->where('email', $data['email'])
            ->where('otp', $data['otp'])
            ->where('type', 'forgot')
            ->where('expires_at >=', date('Y-m-d H:i:s'))
            ->first();

        if ($otp) {
            return $this->response->setJSON(['status' => true]);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'OTP tidak valid']);
    }

    public function resetPassword()
    {
        $data = $this->request->getPost();
        if ($data['password'] !== $data['confirm_password']) {
            return $this->response->setJSON(['status' => false, 'message' => 'Password tidak cocok']);
        }

        $userModel = new UserModel();
        $user = $userModel->where('email', $data['email'])->first();
        if (!$user) {
            return $this->response->setJSON(['status' => false, 'message' => 'Email tidak ditemukan']);
        }

        $userModel->update($user['id'], ['password' => password_hash($data['password'], PASSWORD_BCRYPT)]);
        return $this->response->setJSON(['status' => true, 'message' => 'Password berhasil diubah']);
    }
    public function deleteUser()
    {
        // $email = $this->request->getPost('email');
        // $userModel = new UserModel();
        // $user = $userModel->where('email', $email)->first();

        // if (!$user) {
        //     return $this->response->setJSON(['status' => false, 'message' => 'User tidak ditemukan']);
        // }

        // $userModel->delete($user['id']);
        // return $this->response->setJSON(['status' => true, 'message' => 'User berhasil dihapus']);

        
        // Ambil token JWT dari header
        $authHeader = $this->request->getHeaderLine('Authorization');
        $token = str_replace('Bearer ', '', $authHeader);

        try {
            $decoded = verifyJWT($token);
            $email = $decoded->email; // email dari token
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => false, 'message' => 'Token tidak valid']);
        }

        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->first();

        if (!$user) {
            return $this->response->setJSON(['status' => false, 'message' => 'User tidak ditemukan']);
        }

        $userModel->delete($user['id']);
        return $this->response->setJSON(['status' => true, 'message' => 'User berhasil dihapus']);
    }
}
