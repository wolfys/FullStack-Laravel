<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function saveNewMainStreet(Request $request) {

        $request->validate([
            'mainStreet' => 'required|integer'
        ]);

        $user_id =  auth()->user()->id;
        $sql = DB::table('addresses')
            ->where(
                [
                    ['user_id',$user_id],
                    ['main',1]
                ]
            )->first();

        $address = Address::find($sql->id);
        $address->main = 0;
        $address->save();

        $new = $request->get('mainStreet');

        $address_new = Address::find($new);
        $address_new->main = 1;
        $address_new->save();

        return response()->json(
            [
                'message' => 'Главный адрес успешно обновлен.',
            ]
        );

    }
}
