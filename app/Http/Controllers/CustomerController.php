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
        $validated = $request->validate([
            'store_id' => 'required|integer',
            'first_name' => 'required|string|max:45',
            'last_name' => 'required|string|max:45',
            'email' => 'required|email|max:50',
            'address_id' => 'required|integer',
        ]);

        $customer = Customer::create([
            'store_id' => $request->input('store_id'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'address_id' => $request->input('address_id'),
            'create_date' => now(),
        ]);

        return redirect()->route('tablas', ['tipo' => 'customers']);
    }

    public function edit(int $id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json(["msg" => "Customer no encontrado"], 404);
        }
        return response()->json($customer);
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
        return response()->json(['success' => true, 'message' => 'Customer added successfully']);
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
