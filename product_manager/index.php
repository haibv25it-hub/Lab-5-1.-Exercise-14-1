<?php
require('../model/database.php');
require('../model/category.php');
require('../model/category_db.php');
require('../model/product.php');
require('../model/product_db.php');

$action = filter_input(INPUT_POST, 'action');

if ($action == NULL) {
    $action = filter_input(INPUT_GET, 'action');

    if ($action == NULL) {
        $action = 'list_products';
    }
}

if ($action == 'list_products') {
    $category_id = filter_input(
        INPUT_GET,
        'category_id',
        FILTER_VALIDATE_INT
    );

    if ($category_id == NULL || $category_id == FALSE) {
        $category_id = 2;
    }

    // Lấy thông tin danh mục và sản phẩm
    $current_category = CategoryDB::getCategory($category_id);
    $categories = CategoryDB::getCategories();

    $products = ProductDB::getProductsByCategory($category_id);

    // Hiển thị danh sách sản phẩm
    include('product_list.php');

} else if ($action == 'delete_product') {

    // Lấy ID sản phẩm và danh mục
    $product_id = filter_input(
        INPUT_POST,
        'product_id',
        FILTER_VALIDATE_INT
    );

    $category_id = filter_input(
        INPUT_POST,
        'category_id',
        FILTER_VALIDATE_INT
    );

    // Xóa sản phẩm
    ProductDB::deleteProduct($product_id);

    // Quay lại danh sách sản phẩm
    header("Location: .?category_id=$category_id");
    exit;

} else if ($action == 'show_add_form') {

    // Lấy danh sách danh mục
    $categories = CategoryDB::getCategories();

    // Hiển thị form thêm sản phẩm
    include('product_add.php');

} else if ($action == 'add_product') {

    // Lấy dữ liệu từ form
    $category_id = filter_input(
        INPUT_POST,
        'category_id',
        FILTER_VALIDATE_INT
    );

    $code = filter_input(INPUT_POST, 'code');
    $name = filter_input(INPUT_POST, 'name');
    $price = filter_input(INPUT_POST, 'price');

    // Kiểm tra dữ liệu
    if (
        $category_id == NULL ||
        $category_id == FALSE ||
        $code == NULL ||
        $name == NULL ||
        $price == NULL ||
        $price == FALSE
    ) {
        $error = "Invalid product data. Check all fields and try again.";

        include('../errors/error.php');

    } else {

        // Lấy danh mục hiện tại
        $current_category = CategoryDB::getCategory($category_id);

        // Tạo đối tượng Product không có tham số
        $product = new Product();

        // Gán dữ liệu bằng các phương thức setter
        $product->setCategory($current_category);
        $product->setCode($code);
        $product->setName($name);
        $product->setPrice($price);

        // Thêm sản phẩm vào database
        ProductDB::addProduct($product);

        // Quay lại danh sách sản phẩm
        header("Location: .?category_id=$category_id");
        exit;
    }
}
?>