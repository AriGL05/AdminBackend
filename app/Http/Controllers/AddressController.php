<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Address;

class AddressController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'address' => 'required|string',
            'district' => 'required|string',
            'city_id' => 'required|integer',
            'postal_code' => 'required|string',
        ]);
        Address::create([
            'address' => $request->input('address'),
            'district' => $request->input('district'),
            'city_id' => $request->input('city_id'),
            'postal_code' => $request->input('postal_code'),
            'phone' => "8711231234",
        ]);
        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        $address = Address::find($id);
        if (!$address) {
            return response()->json(["msg" => "address no encontrado"], 404);
        }
        $address->address = $request->input('address');
        $address->district = $request->input('district');
        $address->city_id = $request->input('city_id');
        $address->postal_code = $request->input('postal_code');
        $address->phone = "8711231234";
        $address->save();

        return redirect()->back();
    }

    public function destroy($id)
    {
        $address = Address::find($id);
        if (!$address) {
            return response()->json(["msg" => "address no encontrado"], 404);
        }
        $address->delete();
    }

    public function edit($id)
    {
        $address = Address::find($id);
        if (!$address) {
            return response()->json(["msg" => "Address no encontrado"], 404);
        }
        return response()->json($address);
    }
}
