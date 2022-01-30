<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show(Request $request) {
        $id = $request->get('id');
        return  Product::where('category_id', $id)->get();
    }
}
