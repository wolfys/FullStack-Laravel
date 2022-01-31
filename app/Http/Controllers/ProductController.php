<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function show(Request $request) {
        $id = $request->get('id');
        return  Product::where('category_id', $id)->get();
    }

    public function all(): \Illuminate\Support\Collection
    {
        return DB::table('products')
            ->select('products.*','categories.name as categories')
            ->join('categories','categories.id','=','products.category_id')
            ->get();
    }

    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'name' => 'required|string|unique:products',
            'description' => 'required|string',
            'category' => 'required|integer',
            'sum' => 'required|integer'
        ]);

        $id = $request->get('id');
        $name = $request->get('name');
        $category = $request->get('category');
        $description = $request->get('description');
        $sum = $request->get('sum');

        $picture = '/category/noimage.jpeg';

        if ($request->file('picture')) {
            $request->validate([
                'picture' => 'required|file|max:10240|mimes:jpg,png,gif,webp'
            ]);
            $fileName = time() . "_" . $request->file("picture")->getClientOriginalName();
            $request->file("picture")->storeAs('category', $fileName, 'public');
            $picture = "/product/" . $fileName;
        }

        DB::table('products')
            ->where('id', $id)
            ->update(
                [
                    'name' => $name,
                    'description' => $description,
                    'picture' => $picture,
                    'price' => $sum,
                    'category_id' => $category,
                    'updated_at' => now()
                ]);

        return response()->json(
            [
                'message' => 'Продукт успешно добавлена!',
            ]
        );
    }

    public function edit(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'sum' => 'required|integer',
            'category' => 'required|integer',
        ]);
        $id = $request->get('id');
        $name = $request->get('name');
        $category = $request->get('category');
        $description = $request->get('description');
        $sum = $request->get('sum');

        if ($request->file('picture')) {
            $request->validate([
                'picture' => 'required|file|max:10240|mimes:jpg,png,gif,webp'
            ]);
            $category_picture = DB::table('categories')->where('id', $id)->first();
            if($category_picture->picture != 'category/noimage.jpeg') {
                Storage::disk('public')->delete($category_picture->picture);
            }

            $fileName = time() . "_" . $request->file("picture")->getClientOriginalName();
            $request->file("picture")->storeAs('category', $fileName, 'public');
            $picture = "/category/" . $fileName;
        } else {
            $category_picture = DB::table('products')->where('id', $id)->first();
            $picture = $category_picture->picture;
        }

        DB::table('products')
            ->where('id', $id)
            ->update(
                [
                    'name' => $name,
                    'description' => $description,
                    'picture' => $picture,
                    'price' => $sum,
                    'category_id' => $category,
                    'updated_at' => now()
                ]);

        return response()->json(
            [
                'message' => 'Категория успешно обновлена!',
            ]
        );

    }
}
