<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;


class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::all();
        return response()->json($customers); // Devolver los datos como respuesta JSON
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'address_id' => 'required|integer',
            'store_id' => 'required|integer',
        ]);

        $customer = Customer::create($validated);
        return response()->json($customer, 201); // Retornar el cliente creado
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return response()->json($customer); // Retorna el cliente encontrado
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'address_id' => 'required|integer',
            'store_id' => 'required|integer',
        ]);

        $customer = Customer::findOrFail($id);
        $customer->update($validated);

        return response()->json($customer); // Retorna el cliente actualizado
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return response()->json(['message' => 'Customer deleted successfully']); // Confirmación de eliminación
    }
}
