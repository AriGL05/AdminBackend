<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Rental;
use App\Models\Payment;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::all();
        return response()->json($customers);
    }
    public function store(Request $request)
    {

        $request->validated();

        $customer = Customer::create([
            'store_id' => $request->input('store_id'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'address_id' => $request->input('address_id'),
            'create_date' => now(),
        ]);

        return redirect() - back();
    }
    public function update(Request $request, int $id)
    {
        $cust = Customer::find($id);
        if (!$cust) {
            return response()->json(["msg" => "Customer no encontrado"], 404);
        }
        $cust->store_id = $request->input('store_id');
        $cust->first_name = $request->input('first_name');
        $cust->last_name = $request->input('last_name');
        $cust->email = $request->input('email');
        $cust->address_id = $request->input('address_id');
        $cust->save();
    }
    public function destroy(int $id)
    {
        $cust = Customer::find($id);
        if (!$cust) {
            return response()->json(["msg" => "Customer no encontrado"], 404);
        }
        Rental::where('customer_id', $id)->delete();

        Payment::where('customer_id', $id)->delete();
        $cust->delete();
    }
}
