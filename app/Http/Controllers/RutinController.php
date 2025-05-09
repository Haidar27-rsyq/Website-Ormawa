<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Admin;
use App\Models\Rutin;
use App\Models\Agenda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RutinController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    //     //
    // }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function rutinCreate()
    {
        $user = Admin::find(Auth::user()->id);
        return view('admin.rutinitas.create',compact('user'));
    }

    public function rutinIndex()
    {
        if (Auth::user()->role == 'admin') {
            $org = Auth::user()->nama_organisasi;
            $kegiatan = Agenda::where('nama_organisasi', $org)->get();
            $anggota = User::where('nama_organisasi', $org)->get();
            $rutin = Rutin::all();
            // $anggota = User::orderBy('name')->get(); 
            $user = Admin::find(Auth::user()->id);
            return view('admin.rutinitas.index', ['user' => $user, 'rutin' => $rutin, 'anggota' => $anggota, 'kegiatan' => $kegiatan]);
        } elseif (Auth::user()->role == 'super_admin') {
            $kegiatan = Agenda::all();
            $anggota = User::all();
            $admin = Admin::all();
            // $anggota = User::orderBy('name')->get(); 
            $user = User::find(Auth::user()->id);
            return view('admin.super.admin2', ['user' => $user, 'anggota' => $anggota, 'kegiatan' => $kegiatan, 'admin' => $admin]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'hari' => 'required|string|max:255',
            'unit' => 'required|string|max:255',
            'tempat_kegiatan' => 'required|string|max:255',
        ]);

        Rutin::create([
            'hari' => $request->hari,
            'unit' => $request->unit,
            'tempat_kegiatan' => $request->tempat_kegiatan,
        ]);

        return redirect('/admin/rutin')->with('success', 'Data rutinitas berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Rutin  $rutin
     * @return \Illuminate\Http\Response
     */
    // public function show(Rutin $rutin)
    // {
    //     //
    // }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Rutin  $rutin
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $rutin = Rutin::find($id);
        return view('admin.rutinitas-edit', ['rutin' => $rutin]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Rutin  $rutin
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        $request->validate([
            'hari' => 'required|string|max:255',
            'unit' => 'required|string|max:255',
            'tempat_kegiatan' => 'required|string|max:255',
        ]);

        $rutin = Rutin::findOrFail($id);
        $rutin->update($request->only('hari', 'unit', 'tempat_kegiatan'));
        return redirect('/admin/rutin')->with('success', 'Data rutin berhasil ditambahkan.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Rutin  $rutin
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $rutin = Rutin::findOrFail($id);
        $rutin->delete();

        return redirect()->back()->with('success', 'Data rutin berhasil dihapus.');
    }
}
