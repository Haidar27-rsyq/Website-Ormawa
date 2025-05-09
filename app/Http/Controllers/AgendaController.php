<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Admin;
use App\Models\Rutin;
use App\Models\Agenda;
use App\Models\Anggota;
use Illuminate\Http\Request;
use App\Models\Anggota_Agenda;

use Illuminate\Support\Facades\Auth;
use function PHPUnit\Framework\isEmpty;
use \Cviebrock\EloquentSluggable\Services\SlugService;

class AgendaController extends Controller
{
    public function storeKegiatan(Request $request)
    {

        $this->validate($request, [
            'nama_kegiatan' => 'required',
            'tempat_kegiatan' => 'required',
            'gambar' => 'required',
            'tanggal_mulai' => 'required',
            'tanggal_selesai' => 'required',
            'kabinet' => 'required',
        ]);


        $file = $request->file('gambar');
        $nama_file = time() . "_" . $file->getClientOriginalName();
        $tujuan_upload = 'gambar_kegiatan';
        $file->move($tujuan_upload, $nama_file);
        $organisasi = User::find(Auth::user()->id);
        Agenda::create([
            'tipe' => 'kegiatan',
            'nama_organisasi' => $organisasi->nama_organisasi,
            'nama_kegiatan' => $request->nama_kegiatan,
            'tanggal_mulai' => $request->tanggal_mulai,
            'keterangan' => $request->keterangan,
            'tanggal_selesai' => $request->tanggal_selesai,
            'tempat_kegiatan' => $request->tempat_kegiatan,
            'kabinet' => $request->kabinet,
            'gambar' => $nama_file,
        ]);

        return redirect('/admin');
    }

    // public function arsipIndex()
    // {
    //     if (Auth::user()->role == 'admin') {
    //         $org = Auth::user()->nama_organisasi;
    //         $kegiatan = Agenda::where('nama_organisasi', $org)->get();
    //         $anggota = User::where('nama_organisasi', $org)->get();
    //         $rutin = Rutin::all();
    //         // $anggota = User::orderBy('name')->get(); 
    //         $user = Admin::find(Auth::user()->id);
    //         return view('admin.arsip.index', ['user' => $user, 'rutin' => $rutin, 'anggota' => $anggota, 'kegiatan' => $kegiatan]);
    //     } elseif (Auth::user()->role == 'super_admin') {
    //         $kegiatan = Agenda::all();
    //         $anggota = User::all();
    //         $admin = Admin::all();
    //         // $anggota = User::orderBy('name')->get(); 
    //         $user = User::find(Auth::user()->id);
    //         return view('admin.super.admin2', ['user' => $user, 'anggota' => $anggota, 'kegiatan' => $kegiatan, 'admin' => $admin]);
    //     }
    // }




    // bagian Arsip
    public function arsipCreate()
    {
        $user = Admin::find(Auth::user()->id);
        return view('admin.arsip.create', compact('user'));

    }
    public function arsipStore(Request $request)
    {

        $this->validate($request, [
            'nama_kegiatan' => 'required',
            'tempat_kegiatan' => 'required',
            'gambar' => 'required|file|image|mimes:jpeg,png,jpg|max:2000',
            'proposal' => 'required|file|mimes:pdf',
            'tanggal_mulai' => 'required',
            'keterangan' => 'required',
        ]);
        $slug = SlugService::createSlug(Agenda::class, 'slug', $request->nama_kegiatan);
        $gambar = request()->file('gambar')->store('file-gambar');
        $proposal = request()->file('proposal')->store('file-proposal');
        // $organisasi = User::find(Auth::user()->id);
        $organisasi = Admin::find(Auth::guard('admin')->user()->id);
        Agenda::create([
            'nama_organisasi' => $organisasi->nama_organisasi,
            'nama_kegiatan' => $request->nama_kegiatan,
            'keterangan' => $request->keterangan,
            'slug' => $slug,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tempat_kegiatan' => $request->tempat_kegiatan,
            'gambar' => $gambar,
            'proposal' => $proposal,
        ]);

        return redirect('/admin/arsip')->with('success', 'Arsip Kegiatan Berhasil Ditambahkan');
    }



    public function arsipUpdate($id, Request $request)
    {

        $this->validate($request, [
            'nama_kegiatan' => 'required',
            'tempat_kegiatan' => 'required',

            'lpj' => 'file|mimes:pdf',
            'tanggal_mulai' => 'required',
        ]);
        $kegiatan = Agenda::find($id);
        if ($request->gambar) {
            $gambar = request()->file('gambar')->store('file-gambar');
        } else {
            $gambar = $kegiatan->gambar;
        }
        if ($request->proposal) {
            $proposal = request()->file('proposal')->store('file-proposal');
        } else {
            $proposal = $kegiatan->proposal;
        }
        if ($request->lpj) {
            $lpj = request()->file('lpj')->store('file-lpj');
        } else {
            $lpj = $kegiatan->lpj;
        }


        $slug = SlugService::createSlug(Agenda::class, 'slug', request()->nama_kegiatan);
        Agenda::find($id)->update([
            'nama_kegiatan' => $request->nama_kegiatan,
            'slug' => $slug,
            'tanggal_mulai' => $request->tanggal_mulai,
            'keterangan' => $request->keterangan,
            'tempat_kegiatan' => $request->tempat_kegiatan,
            'gambar' => $gambar,
            'proposal' => $proposal,
            'lpj' => $lpj,

        ]);

        $agenda = Agenda::find($id);
        // Validasi input dari request  
        $request->validate([
            'panitia' => 'array|nullable', // memastikan `panitia` adalah array dan bisa null  
            'panitia.*' => 'exists:users,id', // memastikan setiap ID ada dalam tabel anggota  
        ]);

        // Mencari agenda berdasarkan ID  
        $agenda = Agenda::findOrFail($id);

        // Dapatkan anggota yang terhubung dengan agenda  
        $currentPanitiaIds = $agenda->users()->pluck('user_id')->toArray();

        // Dapatkan anggota yang saat ini dipilih dari request  
        $panitiaIds = $request->input('panitia', []);

        // Anggota yang perlu dihapus (yang ada di database tetapi tidak dicentang)  
        $idsToDetach = array_diff($currentPanitiaIds, $panitiaIds);

        // Anggota yang perlu ditambahkan (yang dicentang tetapi tidak ada di database)  
        $idsToAttach = array_diff($panitiaIds, $currentPanitiaIds);

        // Hapus relasi untuk anggota yang tidak dicentang  
        $agenda->users()->detach($idsToDetach);

        // Tambahkan relasi untuk anggota yang baru dicentang  
        $agenda->users()->attach($idsToAttach);

        // {{-- update -- }} selesai update
        return redirect('/admin/arsip')->with('success', 'Berhasil Update Data Panitia');
    }
    //end bagian arsip





    public function detail($id)
    {
        $kegiatan = Agenda::find($id);
        $panitia = User::all();

        // return view ('kegiatan');
        return view('admin/kegiatan', [
            'kegiatan' => $kegiatan,
            'panitia' => $panitia,

        ]);
    }

    public function newsDestroy($id)
    {
        $kegiatan = Agenda::find($id);
        // $panitia = User::all();

        $kegiatan->delete();

        return redirect()->back()->with('success', 'Data News berhasil dihapus.');

    }
}

