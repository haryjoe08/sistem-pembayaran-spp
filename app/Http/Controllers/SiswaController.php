<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Login;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SiswaController extends Controller
{
    public function index()
    {
        $siswas = Siswa::with(['login', 'kelas', 'jurusan', 'tahunAjaran'])->paginate(10);
        return view('admin.master_data.siswa.index', compact('siswas'));
    }

    public function create()
    {
        $kelas = Kelas::all();
        $jurusan = Jurusan::all();
        $tahunAjarans = TahunAjaran::all();

        return view('admin.master_data.siswa.create', compact('kelas', 'jurusan', 'tahunAjarans'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'nis'            => 'required|unique:siswa,nis',
            'nama'           => 'required|string|max:255',
            'tgl_lahir'      => 'required|date',
            'jenis_kelamin'  => 'required|string',
            'kelas_id'       => 'required|exists:kelas,id',
            'jurusan_id'     => 'required|exists:jurusan,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'alamat'         => 'required|string',
            'wali'           => 'required|string',
            'kontak'         => 'required|string',
        ]);

        // Cek apakah NIS sudah ada di login
        $existingLogin = Login::where('username', $request->nis)->first();
        if ($existingLogin) {
            return back()->withErrors(['nis' => 'NIS sudah digunakan untuk login!'])->withInput();
        }

        // Buat password default dari tgl lahir
        $passwordDefault = date('dmY', strtotime($request->tgl_lahir));

        $login = Login::create([
            'username' => $request->nis,
            'password' => Hash::make($passwordDefault),
            'role'     => 'siswa',
        ]);

        // Simpan data siswa
        $siswa = new Siswa();
        $siswa->nis            = $request->nis;
        $siswa->nama           = $request->nama;
        $siswa->tgl_lahir      = $request->tgl_lahir;
        $siswa->jenis_kelamin  = $request->jenis_kelamin;
        $siswa->kelas_id       = $request->kelas_id;
        $siswa->jurusan_id     = $request->jurusan_id;
        $siswa->tahun_ajaran_id = $request->tahun_ajaran_id;
        $siswa->alamat         = $request->alamat;
        $siswa->wali           = $request->wali;
        $siswa->kontak         = $request->kontak;
        $siswa->login_id       = $login->id;
        $siswa->save();

        return redirect()->route('siswa.index')->with('success', 'Siswa berhasil ditambahkan!');
    }


    public function edit(Siswa $siswa)
    {
        $kelas = Kelas::all();
        $jurusan = Jurusan::all();
        $tahunAjarans = TahunAjaran::all();

        return view('admin.master_data.siswa.edit', compact('siswa', 'kelas', 'jurusan', 'tahunAjarans'));
    }

    public function update(Request $request, $nis)
    {
        $request->validate([
            'nama'           => 'required|string|max:100',
            'tgl_lahir'      => 'required|date',
            'jenis_kelamin'  => 'required|in:L,P',
            'kelas_id'       => 'required|exists:kelas,id',
            'jurusan_id'     => 'required|exists:jurusan,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'alamat'         => 'nullable|string',
            'wali'           => 'nullable|string|max:100',
            'kontak'         => 'nullable|string|max:20',
        ]);

        $siswa = Siswa::findOrFail($nis);

        $siswa->update($request->only([
            'nama',
            'tgl_lahir',
            'jenis_kelamin',
            'kelas_id',
            'jurusan_id',
            'tahun_ajaran_id',
            'alamat',
            'wali',
            'kontak',
        ]));

        if ($siswa->login) {
            $siswa->login->update([
                'password' => Hash::make(date('dmY', strtotime($request->tgl_lahir))),
            ]);
        }

        return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil diperbarui');
    }

    public function destroy($nis)
    {
        $siswa = Siswa::findOrFail($nis);

        optional($siswa->login)->delete();

        $siswa->delete();

        return redirect()->route('siswa.index')->with('success', 'Siswa berhasil dihapus!');
    }
}
