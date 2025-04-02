<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Schema;

class StaffController extends Controller
{
    /**
     * Constructor to apply middleware
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin'); // You'll need to create this middleware
    }

    /**
     * Display a listing of staff members.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            // First check if the staff table exists
            if (!Schema::hasTable('staff')) {
                return response()->json(['error' => 'Staff table does not exist'], 500);
            }

            // For debugging, first try a simple query to check basic access
            $staffCount = DB::table('staff')->count();
            \Log::info('Staff count: ' . $staffCount);

            // Get staff data without joins first for troubleshooting
            $simpleStaff = DB::table('staff')
                ->select('staff_id', 'first_name', 'last_name', 'email', 'active', 'rol_id')
                ->get();

            // Apply UTF-8 encoding check and sanitization
            $sanitizedStaff = [];
            foreach ($simpleStaff as $member) {
                $sanitizedMember = [];
                foreach ((array)$member as $key => $value) {
                    if (is_string($value)) {
                        // Check if valid UTF-8, otherwise replace with sanitized version
                        $sanitizedMember[$key] = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                    } else {
                        $sanitizedMember[$key] = $value;
                    }
                }
                $sanitizedStaff[] = (object)$sanitizedMember;
            }

            return response()->json($sanitizedStaff);
        } catch (\Exception $e) {
            \Log::error('Error fetching staff data: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error fetching staff data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created staff member.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:45',
            'last_name' => 'required|string|max:45',
            'address_id' => 'required|integer|exists:address,address_id',
            'email' => 'required|string|email|max:50|unique:staff',
            'store_id' => 'required|integer|exists:store,store_id',
            'active' => 'required|boolean',
            'username' => 'required|string|max:16|unique:staff',
            'password' => 'required|string|min:6',
            'rol_id' => 'required|integer|in:1,2',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $staffId = DB::table('staff')->insertGetId([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'address_id' => $request->address_id,
            'email' => $request->email,
            'store_id' => $request->store_id,
            'active' => $request->active,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'rol_id' => $request->rol_id,
            'last_update' => now()
        ]);

        return response()->json([
            'message' => 'Staff member created successfully',
            'staff_id' => $staffId
        ], 201);
    }

    /**
     * Get staff member for editing.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $staff = DB::table('staff')
            ->where('staff_id', $id)
            ->first();

        if (!$staff) {
            return response()->json(['error' => 'Staff member not found'], 404);
        }

        // Don't send the password hash
        unset($staff->password);

        return response()->json($staff);
    }

    /**
     * Update staff member.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $staff = DB::table('staff')->where('staff_id', $id)->first();

        if (!$staff) {
            return response()->json(['error' => 'Staff member not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:45',
            'last_name' => 'required|string|max:45',
            'address_id' => 'required|integer|exists:address,address_id',
            'email' => [
                'required',
                'string',
                'email',
                'max:50',
                Rule::unique('staff')->ignore($id, 'staff_id')
            ],
            'store_id' => 'required|integer|exists:store,store_id',
            'active' => 'required|boolean',
            'username' => [
                'required',
                'string',
                'max:16',
                Rule::unique('staff')->ignore($id, 'staff_id')
            ],
            'password' => 'nullable|string|min:6',
            'rol_id' => 'required|integer|in:1,2',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $updateData = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'address_id' => $request->address_id,
            'email' => $request->email,
            'store_id' => $request->store_id,
            'active' => $request->active,
            'username' => $request->username,
            'rol_id' => $request->rol_id,
            'last_update' => now()
        ];

        // Only update password if it's provided
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        DB::table('staff')
            ->where('staff_id', $id)
            ->update($updateData);

        return response()->json(['message' => 'Staff member updated successfully']);
    }

    /**
     * Remove staff member.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $staff = DB::table('staff')->where('staff_id', $id)->first();

        if (!$staff) {
            return response()->json(['error' => 'Staff member not found'], 404);
        }

        // Check if this is the last admin
        if ($staff->rol_id == 1) {
            $adminsCount = DB::table('staff')->where('rol_id', 1)->count();

            if ($adminsCount <= 1) {
                return response()->json([
                    'error' => 'Cannot delete the last administrator',
                    'details' => 'At least one administrator must exist in the system'
                ], 422);
            }
        }

        // Delete or deactivate based on your business rules
        // Option 1: Delete the record
        DB::table('staff')->where('staff_id', $id)->delete();

        // Option 2: Alternatively, just deactivate
        // DB::table('staff')->where('staff_id', $id)->update(['active' => 0]);

        return response()->json(['message' => 'Staff member deleted successfully']);
    }
}
