<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API documentation</title>
    <link rel="stylesheet" href="css/home.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <!-- Auth Endpoint -->
        <div class="auth-endpoint my-5">
            <h2>Auth Endpoint</h2>
            <!-- request otp for register -->
            <div class="row">
                <div class="col-1 method">
                    <p>POST</p>
                </div>
                <div class="col-11 endpoint">
                    <a href="" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="false" aria-controls="collapse1"><code>/api/auth/sendOtp</code></a>
                </div>
                <div class="collapse collapse-horizontal" id="collapse1">
                    <div class="endpoint-information">
                        <b>Request to get OTP to email for register</b>
                        <p>Request Key: <code>email</code></p>
                        <p>JSON: <code>
                            {
                                "status": true,
                                "message": "OTP generated (simulasi)",
                                "otp": otp
                            }
                        </code></p>
                    </div>
                </div>
            </div>
            <!-- register -->
            <div class="row">
                <div class="col-1 method">
                    <p>POST</p>
                </div>
                <div class="col-11 endpoint">
                    <a href="" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2"><code>/api/auth/register</code></a>
                </div>
                <div class="collapse collapse-horizontal" id="collapse2">
                    <div class="endpoint-information">
                        <b>Register endpoint</b>
                        <p>Request Key: <code>email</code>, <code>otp</code>, <code>name</code>, <code>username</code>, <code>password</code></p>
                        <p>JSON: <code>
                            {
                                "status": true,
                                "message": "Register berhasil"
                            }
                        </code></p>
                    </div>
                </div>
            </div>
            <!-- login -->
            <div class="row">
                <div class="col-1 method">
                    <p>POST</p>
                </div>
                <div class="col-11 endpoint">
                    <a href="" data-bs-toggle="collapse" data-bs-target="#collapse-3" aria-expanded="false" aria-controls="collapse-3"><code>/api/auth/login</code></a>
                </div>
                <div class="collapse collapse-horizontal" id="collapse-3">
                    <div class="endpoint-information">
                        <b>Login endpoint</b>
                        <p>Request Key: <code>email</code>, <code>password</code>, <code>remember(true or false)</code></p>
                        <p>JSON: <code>
                            {
                                "status": true,
                                "token": jwt_token,
                                "expires_in_days": expire_time
                            }
                        </code></p>
                    </div>
                </div>
            </div>
            <!-- request otp for reset password -->
            <div class="row">
                <div class="col-1 method">
                    <p>POST</p>
                </div>
                <div class="col-11 endpoint">
                    <a href="" data-bs-toggle="collapse" data-bs-target="#collapse-4" aria-expanded="false" aria-controls="collapse-4"><code>/api/auth/password/forgot</code></a>
                </div>
                <div class="collapse collapse-horizontal" id="collapse-4">
                    <div class="endpoint-information">
                        <b>Request OTP for forgot password</b>
                        <p>Request Key: <code>email</code></p>
                        <p>JSON: <code>
                            {
                                "status": true,
                                "otp": otp
                            }
                        </code></p>
                    </div>
                </div>
            </div>
            <!-- verify otp for reset password -->
            <div class="row">
                <div class="col-1 method">
                    <p>POST</p>
                </div>
                <div class="col-11 endpoint">
                    <a href="" data-bs-toggle="collapse" data-bs-target="#collapse-5" aria-expanded="false" aria-controls="collapse-5"><code>/api/auth/password/verify</code></a>
                </div>
                <div class="collapse collapse-horizontal" id="collapse-5">
                    <div class="endpoint-information">
                        <b>Send otp for reset password</b>
                        <p>Request Key: <code>email</code>, <code>otp</code></p>
                        <p>JSON: <code>
                            {
                                "status": true
                            }
                        </code></p>
                    </div>
                </div>
            </div>
            <!-- reset password -->
            <div class="row">
                <div class="col-1 method">
                    <p>PUT</p>
                </div>
                <div class="col-11 endpoint">
                    <a href="" data-bs-toggle="collapse" data-bs-target="#collapse-6" aria-expanded="false" aria-controls="collapse-6"><code>/api/auth/password/reset</code></a>
                </div>
                <div class="collapse collapse-horizontal" id="collapse-6">
                    <div class="endpoint-information">
                        <b>Reset Password Endpoint</b>
                        <p>Request Key: <code>email</code>, <code>password</code>, <code>confirm_password</code></p>
                        <p>JSON: <code>
                            {
                                "status": true,
                                "message": "Password berhasil diubah"
                            }
                        </code></p>
                    </div>
                </div>
            </div>
            <!-- delete User -->
            <div class="row">
                <div class="col-1 method">
                    <p>DELETE</p>
                </div>
                <div class="col-11 endpoint">
                    <a href="" data-bs-toggle="collapse" data-bs-target="#collapse-7" aria-expanded="false" aria-controls="collapse-7"><code>/api/auth/delete</code></a>
                </div>
                <div class="collapse collapse-horizontal" id="collapse-7">
                    <div class="endpoint-information">
                        <b>Delete Account</b>
                        <p>Request Key: <code>Authorization:</code> <code>Bearer</code> <code>jwt_token</code></p>
                        <p>JSON: <code>
                            {
                                "status": true,
                                "message": "User berhasil dihapus"
                            }
                        </code></p>
                    </div>
                </div>
            </div>
            <!-- logout -->
            
        </div>
        <!-- Category Endpoint -->
        <div class="category-endpoint my-5">
            <h2>Category Endpoint</h2>
            <!-- get all category -->
            <div class="row">
                <div class="col-1 method">
                    <p>GET</p>
                </div>
                <div class="col-11 endpoint">
                    <a href="" data-bs-toggle="collapse" data-bs-target="#collapse-category-get" aria-expanded="false" aria-controls="collapse-category-get"><code>/api/category</code></a>
                </div>
                <div class="collapse collapse-horizontal" id="collapse-category-get">
                    <div class="endpoint-information">
                        <b>Get all categories</b>
                        <p>JSON: <code>
                            [
                                {
                                    "category_id": 1,
                                    "slug": "electronics",
                                    "category": "Electronics"
                                },
                                ...
                            ]
                        </code></p>
                    </div>
                </div>
            </div>
            <!-- get specific category -->
            <div class="row">
                <div class="col-1 method">
                    <p>GET</p>
                </div>
                <div class="col-11 endpoint">
                    <a href="" data-bs-toggle="collapse" data-bs-target="#collapse-category-id" aria-expanded="false" aria-controls="collapse-category-id"><code>/api/category/{slug}</code></a>
                </div>
                <div class="collapse collapse-horizontal" id="collapse-category-id">
                    <div class="endpoint-information">
                        <b>Get category by slug</b>
                        <p>JSON: <code>
                            {
                                "category_id": 1,
                                "slug": "electronics",
                                "category": "Electronics"
                            }
                        </code></p>
                    </div>
                </div>
            </div>
            <!-- get product by category -->
            <div class="row">
                <div class="col-1 method">
                    <p>GET</p>
                </div>
                <div class="col-11 endpoint">
                    <a href="" data-bs-toggle="collapse" data-bs-target="#collapse-category-products" aria-expanded="false" aria-controls="collapse-category-products"><code>/api/category/{slug}/products</code></a>
                </div>
                <div class="collapse collapse-horizontal" id="collapse-category-products">
                    <div class="endpoint-information">
                        <b>Get products by category slug</b>
                        <p>JSON: <code>
                            [
                                {
                                    "product_id": 1,
                                    "name": "Product Name",
                                    "price": 10000,
                                    "description": "Product Description",
                                    "image": "image_url"
                                },
                                ...
                            ]
                        </code></p>
                    </div>
                </div>
            </div>
            <!-- Create Category -->
            <div class="row">
                <div class="col-1 method">
                    <p>POST</p>
                </div>
                <div class="col-11 endpoint">
                    <a href="" data-bs-toggle="collapse" data-bs-target="#collapse-category-create" aria-expanded="false" aria-controls="collapse-category-create"><code>/api/category/create</code></a>
                </div>
                <div class="collapse collapse-horizontal" id="collapse-category-create">
                    <div class="endpoint-information">
                        <b>Create a new category</b>
                        <p>Request Key: <code>category (str)</code></p>
                        <p>JSON: <code>
                            {
                                "status": true,
                                "message": "Category created successfully"
                            }
                        </code></p>
                    </div>
                </div>
            </div>
            <!-- update category -->
            <div class="row">
                <div class="col-1 method">
                    <p>PUT</p>
                </div>
                <div class="col-11 endpoint">
                    <a href="" data-bs-toggle="collapse" data-bs-target="#collapse-category-update" aria-expanded="false" aria-controls="collapse-category-update"><code>/api/category/update</code></a>
                </div>
                <div class="collapse collapse-horizontal" id="collapse-category-update">
                    <div class="endpoint-information">
                        <b>Update a category</b>
                        <p>Request Key: <code>category_id (int)</code>, <code>category (str)</code></p>
                        <p>JSON: <code>
                            {
                                "status": true,
                                "message": "Category updated successfully"
                            }
                        </code></p>
                    </div>
                </div>
            </div>
            <!-- delete category -->
            <div class="row">
                <div class="col-1 method">
                    <p>DELETE</p>
                </div>
                <div class="col-11 endpoint">
                    <a href="" data-bs-toggle="collapse" data-bs-target="#collapse-category-delete" aria-expanded="false" aria-controls="collapse-category-delete"><code>/api/category/delete/{slug}</code></a>
                </div>
                <div class="collapse collapse-horizontal" id="collapse-category-delete">
                    <div class="endpoint-information">
                        <b>Delete a category by slug</b>
                        <p>JSON: <code>
                            {
                                "status": true,
                                "message": "Category deleted successfully"
                            }
                        </code></p>
                    </div>
                </div>
            </div> 
        </div>
        <!-- Product Endpoint -->
        <div class="product-endpoint my-5">
            <h2>Product Endpoint</h2>
            <!-- get all product -->
            <div class="row">
                <div class="col-1 method">
                    <p>GET</p>
                </div>
                <div class="col-11 endpoint">
                    <a href="" data-bs-toggle="collapse" data-bs-target="#collapse-product-get" aria-expanded="false" aria-controls="collapse-product-get"><code>/api/product</code></a>
                </div>
                <div class="collapse collapse-horizontal" id="collapse-product-get">
                    <div class="endpoint-information">
                        <b>Get all products</b>
                        <p>JSON: <code>
                            [
                                {
                                    "product_id": 1,
                                    "name": "Product Name",
                                    "price": 10000,
                                    "description": "Product Description",
                                    "image": "image_url"
                                },
                                ...
                            ]
                        </code></p>
                    </div>
                </div>
            </div>
            <!-- get product by slug -->
            <div class="row">
                <div class="col-1 method">
                    <p>GET</p>
                </div>
                <div class="col-11 endpoint">
                    <a href="" data-bs-toggle="collapse" data-bs-target="#collapse-product-id" aria-expanded="false" aria-controls="collapse-product-id"><code>/api/product/{slug}</code></a>
                </div>
                <div class="collapse collapse-horizontal" id="collapse-product-id">
                    <div class="endpoint-information">
                        <b>Get product by slug</b>
                        <p>JSON: <code>
                            {
                                "product_id": 1,
                                "name": "Product Name",
                                "price": 10000,
                                "description": "Product Description",
                                "image": "image_url"
                            }
                        </code></p>
                    </div>
                </div>
            </div>
            <!-- Create a new product -->
            <div class="row">
                <div class="col-1 method">
                    <p>POST</p>
                </div>
                <div class="col-11 endpoint">
                    <a href="" data-bs-toggle="collapse" data-bs-target="#collapse-product-create" aria-expanded="false" aria-controls="collapse-product-create"><code>/api/product/create</code></a>
                </div>
                <div class="collapse collapse-horizontal" id="collapse-product-create">
                    <div class="endpoint-information">
                        <b>Create a new product</b>
                        <p>Request Key: <code>product_name (str)</code>, <code>price (int)</code>, <code>description (str)</code>, <code>image (str)</code>, <code>cat_id (int)</code>, <code>stock (int)</code></p>
                        <p>JSON: <code>
                            {
                                "status": true,
                                "message": "Product created successfully"
                            }
                        </code></p>
                    </div>
                </div>
            </div>
            <!-- update product -->
            <div class="row">
                <div class="col-1 method">
                    <p>PUT</p>
                </div>
                <div class="col-11 endpoint">
                    <a href="" data-bs-toggle="collapse" data-bs-target="#collapse-product-update" aria-expanded="false" aria-controls="collapse-product-update"><code>/api/product/update</code></a>
                </div>
                <div class="collapse collapse-horizontal" id="collapse-product-update">
                    <div class="endpoint-information">
                        <b>Update a product</b>
                        <p>Request Key: <code>product_id (int)</code>, <code>product_name (str)</code>, <code>price (int)</code>, <code>description (str)</code>, <code>image (str)</code>, <code>cat_id (int)</code>, <code>stock (int)</code></p>
                        <p>JSON: <code>
                            {
                                "status": true,
                                "message": "Product updated successfully"
                            }
                        </code></p>
                    </div>
                </div>
            </div>
            <!-- delete product -->
            <div class="row">
                <div class="col-1 method">
                    <p>DELETE</p>
                </div>
                <div class="col-11 endpoint">
                    <a href="" data-bs-toggle="collapse" data-bs-target="#collapse-product-delete" aria-expanded="false" aria-controls="collapse-product-delete"><code>/api/product/delete/{slug}</code></a>
                </div>
                <div class="collapse collapse-horizontal" id="collapse-product-delete">
                    <div class="endpoint-information">
                        <b>Delete a product by slug</b>
                        <p>JSON: <code>
                            {
                                "status": true,
                                "message": "Product deleted successfully"
                            }
                        </code></p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Cart Endpoint -->
        <div class="cart-endpoint my-5">
            <h2>Cart Endpoint</h2>
            <!-- add to cart -->
            <div class="row">
                <div class="col-1 method">
                    <p>POST</p>
                </div>
                <div class="col-11 endpoint">
                    <a href="" data-bs-toggle="collapse" data-bs-target="#collapse-cart-add" aria-expanded="false" aria-controls="collapse-cart-add"><code>/api/cart/add</code></a>
                </div>
                <div class="collapse collapse-horizontal" id="collapse-cart-add">
                    <div class="endpoint-information">
                        <b>Add to cart</b>
                        <p>Request Key: <code>product_id (int)</code>, <code>quantity (int)</code>, <code>Authorization: Bearer JWT_token (optional if user login)</code></p>
                        <p>JSON: <code>
                            {
                                "status": true,
                                "message": "Added to cart"
                            }
                        </code></p>
                    </div>
                </div>
            </div>
            <!-- update cart -->
            <div class="row">
                <div class="col-1 method">
                    <p>PUT</p>
                </div>
                <div class="col-11 endpoint">
                    <a href="" data-bs-toggle="collapse" data-bs-target="#collapse-cart-update" aria-expanded="false" aria-controls="collapse-cart-update"><code>/api/cart/update</code></a>
                </div>
                <div class="collapse collapse-horizontal" id="collapse-cart-update">
                    <div class="endpoint-information">
                        <b>Update cart item quantity</b>
                        <p>Request Key: <code>id (int)</code>, <code>quantity (int)</code>, <code>Authorization: Bearer JWT_token (optional if user login)</code></p>
                        <p>JSON: <code>
                            {
                                "status": true,
                                "message": "Cart updated"
                            }
                        </code></p>
                    </div>
                </div>
            </div>
            <!-- get cart -->
            <div class="row">
                <div class="col-1 method">
                    <p>GET</p>
                </div>
                <div class="col-11 endpoint">
                    <a href="" data-bs-toggle="collapse" data-bs-target="#collapse-cart-get" aria-expanded="false" aria-controls="collapse-cart-get"><code>/api/cart</code></a>
                </div>
                <div class="collapse collapse-horizontal" id="collapse-cart-get">
                    <div class="endpoint-information">
                        <b>Get cart items</b>
                        <p><code>Authorization: Bearer JWT_token (optional if user login)</code></p>
                        <p>JSON: <code>
                            [
                                {
                                    "id": 1,
                                    "user_id": 1,
                                    "product_id": 1,
                                    "quantity": 2
                                },
                                ...
                            ]
                        </code></p>
                    </div>
                </div>
            </div>
            <!-- remove from cart -->
            <div class="row">
                <div class="col-1 method">
                    <p>DELETE</p>
                </div>
                <div class="col-11 endpoint">
                    <a href="" data-bs-toggle="collapse" data-bs-target="#collapse-cart-remove" aria-expanded="false" aria-controls="collapse-cart-remove"><code>/api/cart/delete/{productId}</code></a>
                </div>
                <div class="collapse collapse-horizontal" id="collapse-cart-remove">
                    <div class="endpoint-information">
                        <b>Remove item from cart</b>
                        <p>JSON: <code>
                            {
                                "status": true,
                                "message": "Removed from cart"
                            }
                        </code></p>
                    </div>
                </div>
            </div>
        </div>  
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
</body>

</html>