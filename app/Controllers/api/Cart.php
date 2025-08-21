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
    public function addCart(){
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

        // Cek apakah user login via JWT
        $userId = $this->getUserIdFromJWT();

        if ($userId) {
            // User login: Simpan ke database
            $existing = $this->cartModel
                ->where('user_id', $userId)
                ->where('product_id', $productId)
                ->first();

            if ($existing) {
                $existing['quantity'] += $quantity;
                $this->cartModel->update($existing['id'], $existing);
            } else {
                $this->cartModel->insert([
                    'user_id' => $userId,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                ]);
            }

            return $this->respond([
                'status' => true,
                'message' => 'Added to cart (user)'
            ]);
        } else {
            // Guest user: Simpan ke session
            $session = session();
            $cart = $session->get('cart') ?? [];

            // Cek apakah produk sudah ada di session
            $found = false;
            foreach ($cart as &$item) {
                if ($item['product_id'] == $productId) {
                    $item['quantity'] += $quantity;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $cart[] = [
                    'product_id' => $productId,
                    'quantity' => $quantity
                ];
            }

            $session->set('cart', $cart);
            return $this->respond([
                'status' => true,
                'message' => 'Added to cart (guest)'
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

        if ($userId) {
            $cart = $this->cartModel
                ->where('user_id', $userId)
                ->findAll();
        } else {
            $cart = session()->get('cart') ?? [];
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
}
