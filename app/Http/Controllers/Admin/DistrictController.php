<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\District;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class DistrictController extends Controller
{
    protected $customMessages = [
        'name.required' => 'Nama kecamatan harus diisi',
    ];

    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            if ($request->ajax()) {
                $data = District::get();

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', 'districts.action')
                    ->rawColumns(['action'])
                    ->make((true));
            }
            return view('districts.index');
        }
        return back();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'              => 'required|string',
        ], $this->customMessages);

        $data = District::create([
            'name'              => strip_tags(request()->post('name')),
        ]);

        return response()->json($data);
    }

    public function edit($id)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $data = District::findOrFail($id);

            return response()->json($data);
        }
        return back();
    }

    public function update($id)
    {
        request()->validate([
            'name'              => 'required|string',
        ], $this->customMessages);

        $data = District::findOrFail($id);

        $data->update([
            'name'              => strip_tags(request()->post('name')),
        ]);

        return response()->json($data);
    }

    public function destroy($id)
    {
        $data = District::destroy($id);

        return response()->json($data);
    }
}
