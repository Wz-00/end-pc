<?php

namespace App\Controllers\Api;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use App\Models\Cart as CartModel;

class Cart extends ResourceController
{
    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    protected $format = 'json';
    protected $cartModel;
    public function __construct()
    {
        $this->cartModel = new CartModel();
    }
    public function addCart()
    {
        helper('cookie'); // load helper cookie

        $productId = $this->request->getPost('product_id') ?? null;
        $quantity = $this->request->getPost('quantity') ?? 1;

        if (!$productId) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Product ID is required'
            ])->setStatusCode(400);
        }
        if ($quantity < 1) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Quantity must be at least 1'
            ])->setStatusCode(400);
        }

        // cek apakah produk ada di database
        $productModel = new \App\Models\Product();
        $product = $productModel->find($productId);
        if (!$product) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Product not found'
            ])->setStatusCode(404);
        }

        // cek user login via JWT
        $userId = $this->getUserIdFromJWT();

        if ($userId) {
            // user login: simpan ke database pakai user_id
            $existing = $this->cartModel
                ->where('user_id', $userId)
                ->where('product_id', $productId)
                ->first();

            if ($existing) {
                $existing['quantity'] += $quantity;
                $this->cartModel->update($existing['cart_id'], $existing);
            } else {
                $this->cartModel->insert([
                    'user_id'    => $userId,
                    'product_id' => $productId,
                    'quantity'   => $quantity,
                ]);
            }

            return $this->respond([
                'status'  => true,
                'message' => 'Added to cart (user)'
            ]);
        } else {
            // guest user: simpan ke database pakai guest_identifier
            $guestIdentifier = get_cookie('guest_identifier');

            if (!$guestIdentifier) {
                // generate UUID/random string
                $guestIdentifier = bin2hex(random_bytes(16));
                // simpan ke cookie valid 30 hari
                set_cookie('guest_identifier', $guestIdentifier, 60 * 60 * 24 * 30);
            }

            // cek apakah produk sudah ada di cart berdasarkan guest_identifier
            $existing = $this->cartModel
                ->where('guest_identifier', $guestIdentifier)
                ->where('product_id', $productId)
                ->first();

            if ($existing) {
                $existing['quantity'] += $quantity;
                $this->cartModel->update($existing['cart_id'], $existing);
            } else {
                $this->cartModel->insert([
                    'guest_identifier' => $guestIdentifier,
                    'product_id'       => $productId,
                    'quantity'         => $quantity,
                ]);
            }

            return $this->respond([
                'status'          => true,
                'message'         => 'Added to cart (guest)',
                'guest_identifier'=> $guestIdentifier
            ]);
        }
    }

    public function updateCart()
    {
        $request = $this->request->getRawInput();
        $cartId = $request['id'] ?? null;
        $quantity = $request['quantity'] ?? 1;

        if (!$cartId) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Cart ID is required'
            ])->setStatusCode(400);
        }
        if ($quantity < 1) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Quantity must be at least 1'
            ])->setStatusCode(400);
        }
        // Cek apakah user login via JWT
        $userId = $this->getUserIdFromJWT();
        if ($userId) {
            // User login: Update di database
            $cartItem = $this->cartModel
                ->where('user_id', $userId)
                ->where('id', $cartId)
                ->first();

            if (!$cartItem) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Cart item not found'
                ])->setStatusCode(404);
            }

            $cartItem['quantity'] = $quantity;
            $this->cartModel->update($cartItem['id'], $cartItem);

            return $this->respond([
                'status' => true,
                'message' => 'Cart updated (user)'
            ]);
        } else {
            // Guest user: Update di session
            $session = session();
            $cart = $session->get('cart') ?? [];

            foreach ($cart as &$item) {
                if ($item['id'] == $cartId) {
                    $item['quantity'] = $quantity;
                    break;
                }
            }

            $session->set('cart', $cart);
            return $this->respond([
                'status' => true,
                'message' => 'Cart updated (guest)'
            ]);
        }
    }
    public function getCart()
    {
        $userId = $this->getUserIdFromJWT();
        helper('cookie');

        if ($userId) {
            $cart = $this->cartModel
                ->where('user_id', $userId)
                ->findAll();
        } else {
            $guestIdentifier = get_cookie('guest_identifier');
            if ($guestIdentifier) {
                $cart = $this->cartModel
                    ->where('guest_identifier', $guestIdentifier)
                    ->findAll();
            } else {
                $cart = [];
            }
        }

        return $this->respond($cart, 200);
    }
    public function removeFromCart($productId)
    {
        $userId = $this->getUserIdFromJWT();

        if ($userId) {
            $this->cartModel
                ->where('user_id', $userId)
                ->where('product_id', $productId)
                ->delete();

            return $this->respond([
                'status' => true,
                'message' => 'Removed from cart (user)'
            ]);
        } else {
            $cart = session()->get('cart') ?? [];
            $newCart = array_filter($cart, function ($item) use ($productId) {
                return $item['product_id'] != $productId;
            });
            session()->set('cart', array_values($newCart));

            return $this->respond([
                'status' => true,
                'message' => 'Removed from cart (guest)'
            ]);
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
    public function getNodeItems()
    {
        $requestData     = $this->request->getJSON(true);
        $cartIds         = $requestData['cart_id'] ?? null;
        $userId          = $this->getUserIdFromJWT() ?? null;
        $guestIdentifier = $requestData['guest_identifier'] ?? null;

        
        if (!$cartIds || !is_array($cartIds)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'cart_id harus berupa array'
            ])->setStatusCode(400);
        }
        if ($userId && $guestIdentifier) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Hanya boleh mengirim user_id atau guest_identifier, tidak boleh keduanya'
            ])->setStatusCode(400);
        }
        if (!$userId && !$guestIdentifier) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Harus mengirim user_id atau guest_identifier'
            ])->setStatusCode(400);
        }
        log_message('debug', '=== DEBUG CI4 ===');
        log_message('debug', 'cartIds: ' . json_encode($cartIds));
        log_message('debug', 'userId: ' . $userId);
        log_message('debug', 'guestIdentifier: ' . $guestIdentifier);

        $builder = $this->cartModel
            ->select('carts.*, products.product_name, products.price, products.image')
            ->join('products', 'products.product_id = carts.product_id')
            ->whereIn('carts.cart_id', $cartIds);

        // Extra security: filter by user or guest
        if ($userId) {
            $builder->where('carts.user_id', $userId);
        } elseif ($guestIdentifier) {
            $builder->where('carts.guest_identifier', $guestIdentifier);
        }

        $items = $builder->get()->getResultArray();

        if (!$items) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Cart tidak ditemukan'
            ])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $items
        ]);
    }


    public function clearNodeCart()
    {
        $cartIds         = $this->request->getVar('cart_id');
        $userId          = $this->request->getVar('user_id');
        $guestIdentifier = $this->request->getVar('guest_identifier');

        if (!$cartIds || !is_array($cartIds)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'cart_id harus berupa array'
            ])->setStatusCode(400);
        }

        $builder = $this->cartModel;

        // Filter by cart_id
        $builder->whereIn('cart_id', $cartIds);

        // Extra security: filter by user or guest
        if ($userId) {
            $builder->where('user_id', $userId);
        } elseif ($guestIdentifier) {
            $builder->where('guest_identifier', $guestIdentifier);
        }

        $deleted = $builder->delete();

        return $this->response->setJSON([
            'status' => 'success',
            'message' => $deleted ? 'Cart berhasil dihapus' : 'Tidak ada cart yang dihapus'
        ]);
    }
    public function resolveUser()
    {
        $requestData = $this->request->getJSON(true);
        $email = $requestData['email'] ?? null;

        if (!$email) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Email wajib dikirim'
            ])->setStatusCode(400);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('users');
        $user = $builder->select('user_id')
                        ->where('email', $email)
                        ->get()
                        ->getRow();

        if (!$user) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User tidak ditemukan'
            ])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'user_id' => $user->user_id
        ]);
    }

}
