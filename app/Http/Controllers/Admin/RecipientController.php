<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Recipient;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class RecipientController extends Controller
{
    protected $customMessages = [
        'name.required'         => 'Nama lengkap harus diisi',
        'nik.required'          => 'NIK harus diisi',
        'village_id.required'   => 'Silahkan pilih alamat terlebih dahulu',
    ];

    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            if ($request->ajax()) {
                $data = Recipient::with('village.district:id,name')->get();

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('address', function ($row) {
                        return $row->village->name . ' - ' . $row->village->district->name;
                    })
                    ->addColumn('action', 'recipients.action')
                    ->rawColumns(['action'])
                    ->make((true));
            }
            $villages = Village::get();
            return view('recipients.index', compact('villages'));
        }
        return back();
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik'               => 'required|string',
            'name'              => 'required|string',
            'village_id'        => 'required',
        ], $this->customMessages);

        $data = Recipient::create([
            'nik'              => strip_tags(request()->post('nik')),
            'name'              => strip_tags(request()->post('name')),
            'village_id'       => strip_tags(request()->post('village_id')),
        ]);

        return response()->json($data);
    }

    public function edit($id)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $data = Recipient::with('village.district:id,name')->findOrFail($id);

            return response()->json($data);
        }
        return back();
    }

    public function update($id)
    {
        request()->validate([
            'nik'               => 'required|string',
            'name'              => 'required|string',
            'village_id'        => 'required',
        ], $this->customMessages);

        $data = Recipient::findOrFail($id);

        $data->update([
            'nik'              => strip_tags(request()->post('nik')),
            'name'             => strip_tags(request()->post('name')),
            'village_id'       => strip_tags(request()->post('village_id')),
        ]);

        return response()->json($data);
    }

    public function destroy($id)
    {
        $data = Recipient::destroy($id);

        return response()->json($data);
    }
}
