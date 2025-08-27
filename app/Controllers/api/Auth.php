<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\UserDetail as UserDetail;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\User as UserModel;
use App\Models\Otp as OtpModel;
helper('jwt');
class Auth extends BaseController
{
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

        // Validasi input
        if (empty($data['email']) || empty($data['name']) || empty($data['username']) || empty($data['password']) || empty($data['confirm_password'])) {
            return $this->response->setJSON(['status' => false, 'message' => 'Semua field wajib diisi']);
        }
        // Validasi password
        if ($data['password'] !== $data['confirm_password']) {
            return $this->response->setJSON(['status' => false, 'message' => 'Password tidak cocok']);
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
            'name' => $data['name'],
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
        $data = $this->request->getRawInput();
        if ($data['password'] !== $data['confirm_password']) {
            return $this->response->setJSON(['status' => false, 'message' => 'Password tidak cocok']);
        }

        $userModel = new UserModel();
        $user = $userModel->where('email', $data['email'])->first();
        if (!$user) {
            return $this->response->setJSON(['status' => false, 'message' => 'Email tidak ditemukan']);
        }

        $userModel->update($user['user_id'], ['password' => password_hash($data['password'], PASSWORD_BCRYPT)]);
        return $this->response->setJSON(['status' => true, 'message' => 'Password berhasil diubah']);
    }
    public function deleteUser()
    {        
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
        // cek apakah user adalah admin
        if ($user['role'] === 'admin') {   
            return $this->response->setJSON(['status' => false, 'message' => 'Tidak dapat menghapus akun admin']);
        }

        $userModel->delete($user['user_id']);
        return $this->response->setJSON(['status' => true, 'message' => 'User berhasil dihapus']);
    }
    public function logout(){
        // Hapus token JWT dari sisi klien (frontend)
        // Tidak ada aksi di server, cukup menghapus token di sisi klien
        return $this->response->setJSON(['status' => true, 'message' => 'Logout berhasil']);
    }
    public function addAddress(){
        helper('cookie');
        $request = $this->request->getPost();
        $userDetail = new UserDetail();

        $userId = $this->getUserIdFromJWT();
                
        if ($userId) {
            log_message('debug', 'DEBUG CI4: addAddress() dipanggil dengan userId=' . $userId);
            // check if user already has an address
            if($userDetail->where('user_id', $userId)->first()){
                return $this->response->setJSON(['status' => false, 'message' => 'Alamat sudah terisi di akun anda'])->setStatusCode(400);
            }
            // remove cookies if user is logged in
            delete_cookie('guest_identifier');
            $userDetail->insert([
                'user_id' => $userId,
                'address' => $request['address'],
                'city' => $request['city'],
                'postal_code' => $request['postal_code'],
                'country' => $request['country'],
                'phone' => $request['phone']
            ]);
            log_message('debug', 'DEBUG CI4: addAddress() dipanggil dengan request=' . json_encode($request));
            return $this->response->setJSON(['status' => true, 'message' => 'Alamat berhasil ditambahkan (user)']);
        } else {
            
            $guestIdentifier = get_cookie('guest_identifier');
            log_message('debug', 'DEBUG CI4: addAddress() dipanggil dengan guestIdentifier=' . $guestIdentifier);
            if($userDetail->where('guest_identifier', $guestIdentifier)->first()){
                return $this->response->setJSON(['status' => false, 'message' => 'Alamat sudah terisi di akun anda'])->setStatusCode(400);
            }
            if($guestIdentifier) {
                $userDetail->insert([
                'guest_identifier' => $guestIdentifier,
                'address' => $request['address'],
                'city' => $request['city'],
                'postal_code' => $request['postal_code'],
                'country' => $request['country'],
                'phone' => $request['phone']
            ]);
            log_message('debug', 'DEBUG CI4: addAddress() dipanggil dengan request=' . json_encode($request));
            return $this->response->setJSON(['status' => true, 'message' => 'Alamat berhasil ditambahkan (guest)']);
            }
            else {
                return $this->response->setJSON(['status' => false, 'message' => 'User tidak terautentikasi dan tidak ada guest identifier'])->setStatusCode(401);
            }
            
        } 
        
        
        
    }
    public function getAddresses(){
        helper('cookie');

        $userId = $this->getUserIdFromJWT();
        $guestIdentifier = get_cookie('guest_identifier');
        $userDetail = new UserDetail();

        if ($userId) {
            $address = $userDetail->where('user_id', $userId)->first();
        } else if($guestIdentifier) {
            $address = $userDetail->where('guest_identifier', $guestIdentifier)->first();
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'User tidak terautentikasi dan tidak ada guest identifier'])->setStatusCode(401);
        }

        if($address){
            return $this->response->setJSON(['status' => true, 'data' => $address]);
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'Alamat tidak ditemukan'])->setStatusCode(404);
        }
    }
    public function updateAddress(){
        helper('cookie');

        $request = $this->request->getRawInput();
        $userDetail = new UserDetail();

        $userId = $this->getUserIdFromJWT();
        $guestIdentifier = get_cookie('guest_identifier');

        if(empty($request['address']) || empty($request['city']) || empty($request['postal_code']) || empty($request['country']) || empty($request['phone'])){
            return $this->response->setJSON(['status' => false, 'message' => 'Semua field wajib diisi'])->setStatusCode(400);
        }

        if ($userId) {
            $existing = $userDetail->where('user_id', $userId)->first();
            if(!$existing){
                return $this->response->setJSON(['status' => false, 'message' => 'Alamat tidak ditemukan untuk user ini'])->setStatusCode(404);
            }
            $userDetail->update($existing['uid'], [
                'address' => $request['address'],
                'city' => $request['city'],
                'postal_code' => $request['postal_code'],
                'country' => $request['country'],
                'phone' => $request['phone']
            ]);
            return $this->response->setJSON(['status' => true, 'message' => 'Alamat berhasil diperbarui (user)']);
        } else if($guestIdentifier) {
            $existing = $userDetail->where('guest_identifier', $guestIdentifier)->first();
            if(!$existing){
                return $this->response->setJSON(['status' => false, 'message' => 'Alamat tidak ditemukan untuk guest ini'])->setStatusCode(404);
            }
            $userDetail->update($existing['uid'], [
                'address' => $request['address'],
                'city' => $request['city'],
                'postal_code' => $request['postal_code'],
                'country' => $request['country'],
                'phone' => $request['phone']
            ]);
            return $this->response->setJSON(['status' => true, 'message' => 'Alamat berhasil diperbarui (guest)']);
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'User tidak terautentikasi dan tidak ada guest identifier'])->setStatusCode(401);
        }
    }
    public function deleteAddress(){
        helper('cookie');

        $userId = $this->getUserIdFromJWT();
        $guestIdentifier = get_cookie('guest_identifier');
        $userDetail = new UserDetail();

        if ($userId) {
            $existing = $userDetail->where('user_id', $userId)->first();
            if(!$existing){
                return $this->response->setJSON(['status' => false, 'message' => 'Alamat tidak ditemukan untuk user ini'])->setStatusCode(404);
            }
            $userDetail->delete($existing['uid']);
            return $this->response->setJSON(['status' => true, 'message' => 'Alamat berhasil dihapus (user)']);
        } else if($guestIdentifier) {
            $existing = $userDetail->where('guest_identifier', $guestIdentifier)->first();
            if(!$existing){
                return $this->response->setJSON(['status' => false, 'message' => 'Alamat tidak ditemukan untuk guest ini'])->setStatusCode(404);
            }
            $userDetail->delete($existing['uid']);
            return $this->response->setJSON(['status' => true, 'message' => 'Alamat berhasil dihapus (guest)']);
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'User tidak terautentikasi dan tidak ada guest identifier'])->setStatusCode(401);
        }
    }
    private function getUserIdFromJWT()
    {
        $authHeader = $this->request->getHeaderLine('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return null;
        }

        $token = str_replace('Bearer ', '', $authHeader);

        try {
            helper('jwt'); // panggil helper Anda
            $decoded = verifyJWT($token);

            $email = $decoded->email ?? null;
            if (!$email) {
                return null;
            }

            // Query user_id berdasarkan email
            $db = \Config\Database::connect();
            $builder = $db->table('users');
            $user = $builder->select('user_id')
                            ->where('email', $email)
                            ->get()
                            ->getRow();

            return $user ? $user->user_id : null;

        } catch (\Exception $e) {
            return null; // JWT tidak valid atau expired
        }
    }
}
