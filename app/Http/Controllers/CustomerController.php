<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

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

        // Create the customer
        $customer = Customer::create([
            'store_id' => $request->input('store_id'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'address_id' => $request->input('address_id'),
            'active' => $request->input('active'),
            'create_date' => now(),
            'last_update' => now(),
        ]);

        return redirect()->route('Customers');
    }
}
