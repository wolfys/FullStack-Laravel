<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CategoriesController extends Controller
{
    public function index()
    {
        return Category::all();
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Request $request)
    {
        $id = $request->get('id');
        return Category::where('id', $id)->get();
    }

    public function edit(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'name' => 'string',
            'description' => 'string'
        ]);
        $id = $request->get('id');
        $name = $request->get('name');
        $description = $request->get('description');
        $picture = 'category/noimage.jpeg';

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
        }

        DB::table('categories')
            ->where('id', $id)
            ->update(
                [
                    'name' => $name,
                    'description' => $description,
                    'picture' => $picture,
                    'updated_at' => now()
                ]);

        return response()->json(
            [
                'message' => 'Категория успешно обновлена!',
            ]
        );

    }

    public function update(Request $request, Category $category)
    {
        //
    }

    public function destroy(Category $category)
    {
        //
    }
}
