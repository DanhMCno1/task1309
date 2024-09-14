<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function getProducts()
    {
        // Lấy danh sách sản phẩm từ cơ sở dữ liệu
        $products = Product::all();
        // Trả về kết quả dưới dạng JSON
        return response()->json($products);
    }
}
