<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class VillageController extends Controller
{
    protected $customMessages = [
        'name.required' => 'Nama desa harus diisi',
        'district_id.required' => 'Silahkan pilih kecamatann terlebih dahulu',
    ];

    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            if ($request->ajax()) {
                $data = Village::with('district:id,name')->get();

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('district_name', function ($row) {
                        return $row->district->name;
                    })
                    ->addColumn('action', 'villages.action')
                    ->rawColumns(['action'])
                    ->make((true));
            }
            $districts = District::get();
            return view('villages.index', compact('districts'));
        }
        return back();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'              => 'required|string',
            'district_id'       => 'required|',
        ], $this->customMessages);

        $data = Village::create([
            'name'              => strip_tags(request()->post('name')),
            'district_id'       => strip_tags(request()->post('district_id')),
        ]);

        return response()->json($data);
    }

    public function edit($id)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $data = Village::with('district:id,name')->findOrFail($id);

            return response()->json($data);
        }
        return back();
    }

    public function update($id)
    {
        request()->validate([
            'name'              => 'required|string',
        ], $this->customMessages);

        $data = Village::findOrFail($id);

        $data->update([
            'name'              => strip_tags(request()->post('name')),
        ]);

        return response()->json($data);
    }

    public function destroy($id)
    {
        $data = Village::destroy($id);

        return response()->json($data);
    }
}
