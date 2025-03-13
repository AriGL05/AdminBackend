<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Exception;

class AddressController extends Controller
{
    public function index(Request $request)
    {
        $addresses = Address::select('address.address_id', 'address.address', 'address.district', 'city.city as city', 'address.postal_code')
            ->join('city', 'address.city_id', '=', 'city.city_id')
            ->orderBy('address.address_id') // Add this line to order by film_id descending
            ->get();
        return response()->json($addresses);
    }
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
        return response()->json(['success' => true, 'message' => 'Addres added successfully']);
    }

    /**
     * Update the specified address.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            // Log the raw request data for debugging
            Log::info("Received update request for address ID: $id", [
                'raw_data' => $request->all()
            ]);

            // Validate the request data
            $validatedData = $request->validate([
                'address' => 'required|string|max:50',
                'district' => 'required|string|max:20',
                'city_id' => 'required|integer|exists:city,city_id',
                'postal_code' => 'nullable|string|max:10',
            ]);

            // Sanitize the input data to prevent UTF-8 encoding issues
            $sanitizedData = [
                'address' => $this->forceAscii($validatedData['address']),
                'district' => $this->forceAscii($validatedData['district']),
                'city_id' => intval($validatedData['city_id']),
                'postal_code' => isset($validatedData['postal_code']) ? $this->forceAscii($validatedData['postal_code']) : null,
            ];

            Log::info("Sanitized data for address update", ['data' => $sanitizedData]);

            // Find the address
            $address = Address::find($id);

            // Check if the address exists
            if (!$address) {
                Log::error("Address with ID: $id not found for update");
                return response()->json(['error' => 'Address not found'], 404);
            }

            // Update the address with sanitized data
            $address->update($sanitizedData);

            // Create a clean response with only the necessary data
            $responseData = [
                'message' => 'Address updated successfully',
                'address' => [
                    'address_id' => $address->address_id,
                    'address' => $this->forceAscii($address->address),
                    'district' => $this->forceAscii($address->district),
                    'city_id' => intval($address->city_id),
                    'postal_code' => $this->forceAscii($address->postal_code),
                ]
            ];

            // Return success response
            return response()->json($responseData, 200, [
                'Content-Type' => 'application/json; charset=utf-8',
            ]);
        } catch (Exception $e) {
            // Log the error
            Log::error("Failed to update address: " . $e->getMessage(), [
                'address_id' => $id,
                'exception' => $e
            ]);

            // Return error response
            return response()->json([
                'error' => 'Failed to update address',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete the specified address.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            Log::info("Attempting to delete address with ID: $id");

            // Find the address
            $address = Address::find($id);

            if (!$address) {
                Log::warning("Address with ID: $id not found for deletion");
                return response()->json(["error" => "Dirección no encontrada"], 404);
            }

            // Check for related records (customers, staff, or stores)
            $hasCustomers = $address->customer()->exists();
            $hasStaff = $address->staff()->exists();
            $hasStores = $address->store()->exists();

            if ($hasCustomers || $hasStaff || $hasStores) {
                Log::warning("Cannot delete address ID: $id - has related records", [
                    'customers' => $hasCustomers,
                    'staff' => $hasStaff,
                    'stores' => $hasStores
                ]);

                return response()->json([
                    "error" => "No es posible eliminar esta dirección porque está siendo utilizada por clientes, personal o tiendas",
                    "details" => [
                        "has_customers" => $hasCustomers,
                        "has_staff" => $hasStaff,
                        "has_stores" => $hasStores
                    ]
                ], 422); // Unprocessable Entity
            }

            // Delete the address
            $address->delete();

            Log::info("Successfully deleted address with ID: $id");
            return response()->json(["success" => true, "message" => "Dirección eliminada correctamente"]);

        } catch (QueryException $e) {
            // Handle database query exceptions (like foreign key constraint violations)
            $errorCode = $e->errorInfo[1] ?? null;

            // Error code 1451 is typically a foreign key constraint violation in MySQL
            if ($errorCode == 1451) {
                Log::error("Foreign key constraint violation when deleting address ID: $id", [
                    'error_code' => $errorCode,
                    'exception' => $e->getMessage()
                ]);

                return response()->json([
                    "error" => "No es posible eliminar esta dirección porque está referenciada por otros registros",
                    "details" => $e->getMessage()
                ], 422); // Unprocessable Entity
            }

            // Handle other database exceptions
            Log::error("Database error when deleting address ID: $id", [
                'exception' => $e->getMessage()
            ]);

            return response()->json([
                "error" => "Ocurrió un error en la base de datos",
                "message" => $e->getMessage()
            ], 500);

        } catch (Exception $e) {
            // Handle general exceptions
            Log::error("Unexpected error when deleting address ID: $id", [
                'exception' => $e->getMessage()
            ]);

            return response()->json([
                "error" => "Ocurrió un error inesperado",
                "message" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the data for editing the specified address.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        try {
            // Log that we're attempting to edit an address
            Log::info("Attempting to fetch address with ID: $id for editing");

            // Find the address by ID
            $address = Address::find($id);

            // Check if the address exists
            if (!$address) {
                Log::error("Address with ID: $id not found");
                return response()->json(['error' => 'Address not found'], 404);
            }

            // Create a minimal sanitized array with just the required fields
            // This avoids potential encoding issues with unnecessary fields
            $minimalData = [
                'address_id' => $address->address_id,
                'address' => $this->forceAscii($address->address),
                'district' => $this->forceAscii($address->district),
                'city_id' => intval($address->city_id),
                'postal_code' => $this->forceAscii($address->postal_code),
            ];

            // Log the sanitized data
            Log::info("Successfully sanitized address data", ['data' => $minimalData]);

            // Return the minimal data as JSON with explicit options
            return response()->json($minimalData, 200, [
                'Content-Type' => 'application/json; charset=utf-8',
            ]);
        } catch (Exception $e) {
            // Log the error
            Log::error("Error retrieving address data: " . $e->getMessage(), [
                'address_id' => $id,
                'exception' => $e
            ]);

            // Return a basic error response with minimal data
            return response()->json([
                'error' => 'Failed to retrieve address data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Force a string to ASCII-only characters.
     * This is a very aggressive sanitization but ensures JSON compatibility.
     *
     * @param string|null $value
     * @return string|null
     */
    private function forceAscii($value)
    {
        if ($value === null) {
            return null;
        }

        // First try to fix encoding issues
        $value = mb_convert_encoding($value, 'UTF-8', 'auto');

        // Strip all non-ASCII characters (very aggressive)
        $value = preg_replace('/[^\x20-\x7E]/u', '', $value);

        // If the value is empty after sanitization, provide a fallback
        if (empty(trim($value))) {
            return "[data sanitized]";
        }

        return $value;
    }

    /**
     * Sanitize address data to fix potential UTF-8 encoding issues.
     * This method is kept for reference but we're using forceAscii instead.
     *
     * @param Address $address
     * @return array
     */
    private function sanitizeAddressData(Address $address)
    {
        // Create a sanitized array of address data
        $sanitized = [
            'address_id' => $address->address_id,
            'address' => $this->forceAscii($address->address),
            'address2' => $this->forceAscii($address->address2),
            'district' => $this->forceAscii($address->district),
            'city_id' => $address->city_id,
            'postal_code' => $this->forceAscii($address->postal_code),
            'phone' => $this->forceAscii($address->phone),
        ];

        return $sanitized;
    }
}
