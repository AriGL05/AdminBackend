<?php

namespace App\Http\Controllers;

use Hash;
use Illuminate\Http\Request;
use App\Models\Staff;
use App\Models\Store;
use App\Models\Roles;
use App\Models\Address;


class StaffController extends Controller
{
    public function index()
    {
        $staff = Staff::select('staff.first_name', 'staff.last_name', 'staff.email', 'staff.rol_id', 'staff.active', 'staff.username')
            ->orderBy('staff.staff_id')->get();
        /*
                $staff = Staff::select('staff.first_name', 'staff.last_name', 'staff.email', 'rol.name as rol', 'staff.active', 'staff.username')
                    ->join('rol', 'staff.rol_id', '=', 'rol.id')
                    ->orderBy('staff.staff_id')->get();
        */
        return response()->json($staff);
    }

    public function edit(int $id)
    {
        $staff = Staff::find($id);
        if (!$staff) {
            return response()->json(["msg" => "Staff no encontrado"], 404);
        }
        return response()->json($staff);
    }

    public function update(Request $request, int $id)
    {
        $staff = Staff::find($id);
        if (!$staff) {
            return response()->json(["msg" => "staff no encontrado"], 404);
        }
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'rol_id' => 'required',
            'active' => 'required',
            'username' => 'required',
        ]);

        $staff->first_name = $request->get('first_name');
        $staff->last_name = $request->get('last_name');
        $staff->email = $request->get('email');
        $staff->rol_id = $request->get('rol_id');
        $staff->active = $request->get('active');
        $staff->username = $request->get('username');

        $staff->save();

        return response()->json(['success' => true, 'message' => 'Staff updated successfully']);
    }

    public function destroy(int $id)
    {
        $staff = Staff::find($id);
        if (!$staff) {
            return response()->json(["msg" => "Staff no encontrado"], 404);
        }
        $staff->active = 0;
    }
}
