<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\Product as ProductModel;
use App\Models\Category as CategoryModel;
class Product extends BaseController
{
    // CRUD Category
    public function index(){
        return $this->response->setJSON([
            'status' => true,
            'message' => 'Welcome to Product API'
        ]);
    }
    public function createCategory()
    {
        $data = $this->request->getPost();
        $categoryModel = new CategoryModel();
        // Validasi field
        $requiredFields = ['category'];

        // Cek field yang kosong
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Harap tidak mengosongkan bagian ' . $field
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST); 
            }
        }

        //generate slug
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['category'])));
        if ($categoryModel->where('slug', $slug)->first()) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Tidak dapat membuat kategori dengan nama yang sama'
            ])->setStatusCode(ResponseInterface::HTTP_CONFLICT);    
        } else {
            // Simpan kategori
            $categoryModel->insert([
                'category' => $data['category'],
                'slug' => $slug,
            ]);
            
            return $this->response->setJSON([
                'status' => true,
                'message' => 'Kategori berhasil dibuat'
            ]);
        }
    }

    public function updateCategory()
    {
        $data = $this->request->getRawInput();
        $categoryModel = new CategoryModel();

        // Validasi field
        $requiredFields = ['category_id', 'category'];

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['category'])));

        // Cek field yang kosong
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Harap tidak mengosongkan bagian ' . $field
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }
        }

        if ($categoryModel->where('slug', $slug)->where('category_id !=', $data['category_id'])->first()) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Tidak dapat memperbarui kategori dengan nama yang sama'
            ])->setStatusCode(ResponseInterface::HTTP_CONFLICT);
        } else {
            // Update kategori  
            $categoryModel->update($data['category_id'], [
                'category' => $data['category'],
                'slug' => $slug,
            ]);

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Kategori berhasil diperbarui'
            ]);
        }
        
    }

    public function deleteCategory($slug)
    {
        $slug = $this->request->getVar('slug') ?? $slug;
        $categoryModel = new CategoryModel();

        // Validasi slug
        if (empty($slug)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Slug kategori tidak boleh kosong'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        // Cek apakah kategori ada
        $category = $categoryModel->where('slug', $slug)->first();
        if (!$category) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Kategori tidak ditemukan'
            ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }

        // Hapus kategori
        $categoryModel->where('slug', $slug)->delete();

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Kategori berhasil dihapus'
        ]);
    }


    // CRUD Product
    public function createProduct()
    {
        $data = $this->request->getPost();
        $productModel = new ProductModel();
        $categoryModel = new CategoryModel();

        // Validasi field wajib
        $requiredFields = ['product_name', 'image', 'description', 'price', 'cat_id'];

        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Harap tidak mengosongkan bagian ' . $field
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }
        }

        // Validasi apakah cat_id valid (ada di tabel category)
        $category = $categoryModel->find($data['cat_id']);
        if (!$category) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Kategori tidak ditemukan atau tidak valid'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        // Generate slug
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['product_name'])));
        if ($productModel->where('slug', $slug)->first()) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Tidak dapat membuat produk dengan nama yang sama'
            ])->setStatusCode(ResponseInterface::HTTP_CONFLICT);
        }

        // Simpan produk
        $productModel->insert([
            'product_name' => $data['product_name'],
            'image' => $data['image'],
            'description' => $data['description'],
            'price' => $data['price'],
            'cat_id' => $data['cat_id'],
            'stock' => $data['stock'] ?? 0,
            'slug' => $slug,
        ]);

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Produk berhasil dibuat'
        ]);
    }
    public function updateProduct()
    {
        $data = $this->request->getRawInput(); // Karena update biasanya pakai PUT
        $productModel = new ProductModel();
        $categoryModel = new CategoryModel();

        // Daftar field yang wajib diisi
        $requiredFields = ['product_id', 'product_name', 'image', 'description', 'price', 'cat_id'];

        // Label field untuk pesan Bahasa Indonesia
        $fieldLabels = [
            'product_id' => 'ID Produk',
            'product_name' => 'Nama Produk',
            'image' => 'Gambar',
            'description' => 'Deskripsi',
            'price' => 'Harga',
            'cat_id' => 'Kategori'
        ];

        // Cek apakah ada field wajib yang kosong
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Harap tidak mengosongkan bagian ' . $fieldLabels[$field]
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }
        }

        // Cek apakah produk ada
        $existingProduct = $productModel->find($data['product_id']);
        if (!$existingProduct) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Produk tidak ditemukan'
            ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }

        // Validasi apakah cat_id valid (ada di tabel category)
        $category = $categoryModel->find($data['cat_id']);
        if (!$category) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Kategori tidak ditemukan atau tidak valid'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        // Generate slug dari nama produk baru
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['product_name'])));

        // Cek apakah slug bentukan sudah dipakai produk lain
        $duplicateSlug = $productModel
            ->where('slug', $slug)
            ->where('product_id !=', $data['product_id'])
            ->first();

        if ($duplicateSlug) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Nama produk sudah digunakan oleh produk lain'
            ])->setStatusCode(ResponseInterface::HTTP_CONFLICT);
        }

        // Update produk
        $productModel->update($data['product_id'], [
            'product_name' => $data['product_name'],
            'image' => $data['image'],
            'description' => $data['description'],
            'price' => $data['price'],
            'cat_id' => $data['cat_id'],
            'stock' => $data['stock'] ?? 0,
            'slug' => $slug,
        ]);

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Produk berhasil diperbarui'
        ]);
    }
    public function deleteProduct($slug)
    {
        $slug = $this->request->getVar('slug') ?? $slug;
        $productModel = new ProductModel();

        // Validasi slug
        if (empty($slug)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Slug product tidak boleh kosong'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        // Cek apakah product ada
        $category = $productModel->where('slug', $slug)->first();
        if (!$category) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'product tidak ditemukan'
            ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }

        // Hapus product
        $productModel->where('slug', $slug)->delete();

        return $this->response->setJSON([
            'status' => true,
            'message' => 'product berhasil dihapus'
        ]);
    }
}
