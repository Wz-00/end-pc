<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Config\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;
class OnlyAdmin implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null){
        $authHeader = $request->getHeaderLine('Authorization');

        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return service('response')->setJSON([
                'status' => 'error',
                'message' => 'Token tidak ditemukan.'
            ])->setStatusCode(401);
        }

        $token = $matches[1];

        try {
            $key = getenv('JWT_SECRET') ?? 'your_secret_key';
            $decoded = JWT::decode($token, new Key($key, 'HS256'));

            // Ambil email dan cek ke DB
            $email = $decoded->email;
            $userModel = new User();
            $user = $userModel->where('email', $email)->first();

            if (!$user || $user['role'] !== 'admin') {
                return service('response')->setJSON([
                    'status' => 'error',
                    'message' => 'Akses hanya untuk admin.'
                ])->setStatusCode(403);
            }

        } catch (\Exception $e) {
            return service('response')->setJSON([
                'status' => 'error',
                'message' => 'Token tidak valid: ' . $e->getMessage()
            ])->setStatusCode(401);
        }

        return null; // lanjutkan permintaan
    }


    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
