<?php

namespace App\Http\Controllers;

use Hamcrest\Core\AllOf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        return response()->json(auth()->user());
    }

    public function saveMainInfo(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $id = auth()->user()->id;
        $name = $request->get('name');

        DB::table('users')->where('id',$id)->update(['name' => $name, 'updated_at' => now()]);

        return response()->json(
            [
                'message' => 'Профиль успешно обновлена!',
            ]
        );
    }
    public function saveNewPassword(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
           'newPassword' => 'min:6|required_with:newPassword2|same:newPassword2',
           'newPassword2' => 'min:6'
        ]);
        $id = auth()->user()->id;
        $old_password = $request->get('password');
        $password_hash = auth()->user()->password;

        if(Hash::check($old_password,$password_hash)) {
        $new_password_hash = Hash::make($request->get('newPassword'));
        DB::table('users')->where('id',$id)->update(['password' => $new_password_hash, 'updated_at' => now()]);
        } else {
            return response()->json(['errors' => 'Не верный текущий пароль'], 401);
        }
        return response()->json(
            [
                'message' => 'Пароль успешно обновлен.',
            ]
        );
    }
    public function saveNewAvatar(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'picture' => 'required|file|max:10240|mimes:jpg,png,gif,webp'
        ]);
        $id = auth()->user()->id;
        $category_picture = DB::table('users')->where('id', $id)->first();
        if($category_picture->picture != '/avatar/noavatar.jpg') {
            Storage::disk('public')->delete($category_picture->picture);
        }
        $fileName = time() . "_" . $request->file("picture")->getClientOriginalName();
        $request->file("picture")->storeAs('avatar', $fileName, 'public');
        $picture = "/avatar/" . $fileName;
        DB::table('users')->where('id',$id)->update(['picture' => $picture, 'updated_at' => now()]);

        return response()->json(
            [
                'message' => 'Аватар успешно обновлен.',
            ]
        );
    }
}
