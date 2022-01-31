<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index()
    {
        return Address::all();
    }

    public function saveNewStreet(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'street' => 'required|string'
        ]);

        $main = ($request->get('mainStreet') == true) ? 1 : 0;

        $address = new Address();
        $address->user_id = auth()->user()->id;
        $address->address = $request->get('street');
        $address->main = $main;
        $address->created_at = now();

        $address->save();

        return response()->json(
            [
                'message' => 'Адрес успешно добавлен.',
            ]
        );
    }
}
