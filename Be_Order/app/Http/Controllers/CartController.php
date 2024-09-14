<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartItem;

class CartController extends Controller
{
        public function addToCart(Request $request)
    {
        // Lấy giỏ hàng từ session, nếu không có thì khởi tạo mảng rỗng
        $cart = session()->get('cart', []);

        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1);

        // Kiểm tra xem sản phẩm đã có trong giỏ hàng hay chưa
        if (isset($cart[$productId])) {
            // Nếu sản phẩm đã tồn tại, tăng số lượng
            $cart[$productId]['quantity'] += $quantity;
        } else {
            // Nếu sản phẩm chưa tồn tại, thêm mới
            $cart[$productId] = [
                'product_id' => $productId,
                'quantity' => $quantity,
                'price' => $request->input('price'), // Lấy giá sản phẩm từ request
                'name' => $request->input('name'),   // Tên sản phẩm
                'image' => $request->input('image')  // Ảnh sản phẩm
            ];
        }

        // Cập nhật lại giỏ hàng trong session
        session()->put('cart', $cart);

        // Trả về giỏ hàng hiện tại sau khi cập nhật
        return response()->json(['success' => true, 'cart' => $cart]);
    }


    public function removeFromCart($productId)
    {
        // Lấy giỏ hàng từ session
        $cart = session()->get('cart', []);

        // Kiểm tra xem sản phẩm có trong giỏ hàng hay không
        if (isset($cart[$productId])) {
            unset($cart[$productId]); // Xóa sản phẩm khỏi giỏ hàng
            session()->put('cart', $cart); // Cập nhật lại session
        }

        return response()->json(['success' => true, 'cart' => $cart]);
    }

    public function updateCartItemQuantity(Request $request, $productId)
    {
        // Lấy giỏ hàng từ session (hoặc từ cơ sở dữ liệu nếu bạn lưu giỏ hàng trong DB)
        $cart = session()->get('cart', []);

        // Kiểm tra xem sản phẩm có trong giỏ hàng hay không
        if (isset($cart[$productId])) {
            // Cập nhật số lượng sản phẩm
            $newQuantity = $request->input('quantity');

            // Nếu số lượng mới lớn hơn 0, cập nhật giỏ hàng
            if ($newQuantity > 0) {
                $cart[$productId]['quantity'] = $newQuantity;
                session()->put('cart', $cart); // Cập nhật lại session

                return response()->json(['success' => true, 'cartItem' => $cart[$productId]]);
            } else {
                // Nếu số lượng <= 0, xóa sản phẩm khỏi giỏ hàng
                unset($cart[$productId]);
                session()->put('cart', $cart);

                return response()->json(['success' => true, 'message' => 'Product removed from cart']);
            }
        }

        return response()->json(['error' => 'Product not found in cart'], 404);
    }

    public function index()
    {
        // Lấy giỏ hàng từ session
        $cart = session()->get('cart', []);

        // Trả về giỏ hàng dưới dạng JSON
        return response()->json($cart);
    }

    // Thêm sản phẩm vào giỏ hàng
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        // Kiểm tra nếu sản phẩm đã có trong giỏ hàng
        $cartItem = CartItem::where('product_id', $request->product_id)->first();

        if ($cartItem) {
            // Tăng số lượng nếu đã tồn tại
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            // Tạo sản phẩm mới trong giỏ hàng
            CartItem::create($validatedData);
        }

        return response()->json(['message' => 'Added to cart successfully']);
    }

    // Xóa sản phẩm khỏi giỏ hàng
    public function destroy($id)
    {
        $cartItem = CartItem::findOrFail($id);
        $cartItem->delete();

        return response()->json(['message' => 'Removed from cart successfully']);
    }
}
