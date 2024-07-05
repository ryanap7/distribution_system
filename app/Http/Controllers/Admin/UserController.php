<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            if ($request->ajax()) {
                $data = User::with('village:id,name')->get();

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('village_name', function ($row) {
                        $villageName = $row->village ? $row->village->name : '-';

                        return $villageName;
                    })
                    ->addColumn('action', 'users.action')
                    ->rawColumns(['action'])
                    ->make((true));
            }
            $villages = Village::get();
            return view('users.index', compact('villages'));
        }
        return back();
    }
    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
            'village_id' => 'required',
            'role' => 'required|in:admin,perangkat_desa',
        ]);

        $user = User::create([
            'name' =>  strip_tags(request()->post('name')),
            'email' => strip_tags(request()->post('email')),
            'phone_number' => strip_tags(request()->post('phone_number')),
            'password' => Hash::make(strip_tags(request()->post('password'))),
            'village_id' => strip_tags(request()->post('village_id')),
            'role' => strip_tags(request()->post('role')),
        ]);

        return response()->json($user);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('users.show', compact('user'));
    }

    public function edit($id)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $data = User::with('village:id,name')->findOrFail($id);

            return response()->json($data);
        }
        return back();
    }

    public function update($id)
    {
        $user = User::findOrFail($id);

        $user->name     = strip_tags(request()->post('name'));
        $user->email    = strip_tags(request()->post('email'));
        $user->phone_number = strip_tags(request()->post('phone_number'));

        if (request()->post('password')) {
            $user->password = Hash::make(request()->post('password'));
        }

        $user->role = strip_tags(request()->post('role'));

        $user->save();

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        $data = User::destroy($id);

        return response()->json($data);
    }
}
