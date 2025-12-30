<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Login;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SiswaExport;
use App\Imports\SiswaImport;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = Siswa::with(['kelas', 'jurusan']);

        // Filter Status (Default: aktif)
        $status = $request->input('status', 'aktif'); // Default aktif
        if ($status !== 'semua') {
            $query->where('status', $status);
        }

        // Filter Search (NIS atau Nama)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nis', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        // Filter Kelas
        if ($request->filled('kelas')) {
            $query->where('kelas_id', $request->kelas);
        }

        // Filter Jurusan
        if ($request->filled('jurusan')) {
            $query->where('jurusan_id', $request->jurusan);
        }

        $siswas = $query->orderBy('nis', 'asc')->paginate(10);

        // Append query string ke pagination links
        $siswas->appends($request->except('page'));

        return view('admin.master_data.siswa.index', compact('siswas'));
    }
    /**
     * Show detail siswa
     */
    public function show($nis)
    {
        $siswa = Siswa::with(['kelas', 'jurusan', 'login'])
            ->where('nis', $nis)
            ->firstOrFail();

        // Summary Tagihan
        $tagihans = $siswa->tagihan;
        $totalTagihan = $tagihans->sum('total_tagihan');
        $totalDibayar = $tagihans->sum('sudah_dibayar');
        $totalTunggakan = $tagihans->where('status', 'belum lunas')
            ->sum(function ($t) {
                return $t->total_tagihan - $t->sudah_dibayar;
            });

        $tagihanLunas = $tagihans->where('status', 'lunas')->count();
        $tagihanBelumLunas = $tagihans->where('status', 'belum lunas')->count();

        // Tagihan Terbaru (5)
        $tagihanTerbaru = $siswa->tagihan()
            ->with('jenisTagihan')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Transaksi/Pembayaran Terbaru (10)
        $transaksiTerbaru = \App\Models\Transaksi::where('siswa_nis', $nis)
            ->with('tagihan.jenisTagihan')
            ->orderBy('tanggal', 'desc')
            ->take(10)
            ->get();

        // Payment Orders (jika ada)
        $paymentOrders = \App\Models\PaymentOrder::where('siswa_nis', $nis)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.master_data.siswa.show', compact(
            'siswa',
            'totalTagihan',
            'totalDibayar',
            'totalTunggakan',
            'tagihanLunas',
            'tagihanBelumLunas',
            'tagihanTerbaru',
            'transaksiTerbaru',
            'paymentOrders'
        ));
    }
    public function create()
    {
        $kelas = Kelas::all()->where('status', 'aktif');
        $jurusan = Jurusan::all()->where('status', 'aktif');
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
            'tahun_masuk' => 'required|integer',
            'alamat'         => 'required|string',
            'wali'           => 'required|string',
            'kontak' => 'required|digits_between:10,12',

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
        $siswa->tahun_masuk = $request->tahun_masuk;
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

        return view('admin.master_data.siswa.edit', compact('siswa', 'kelas', 'jurusan'));
    }

    public function update(Request $request, $nis)
    {
        $request->validate([
            'nama'           => 'required|string|max:100',
            'tgl_lahir'      => 'required|date',
            'jenis_kelamin'  => 'required|in:L,P',
            'kelas_id'       => 'required|exists:kelas,id',
            'jurusan_id'     => 'required|exists:jurusan,id',
            'tahun_masuk' => 'required|integer',
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
            'tahun_masuk',
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

    public function showNaikKelas()
    {
        $kelas = Kelas::orderBy('kelas')->get();

        return view('admin.kenaikan_kelas.naik-kelas', compact('kelas'));
    }

    /**
     * Get siswa by kelas (AJAX)
     */
    public function getSiswaByKelas($kelasId)
    {
        try {
            $siswa = Siswa::where('kelas_id', $kelasId)
                ->where('status', 'aktif')
                ->orderBy('nama')
                ->get(['nis', 'nama', 'jenis_kelamin']);

            return response()->json([
                'success' => true,
                'data' => $siswa,
                'count' => $siswa->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process naik kelas (bulk)
     */
    public function prosesNaikKelas(Request $request)
    {
        $request->validate([
            'kelas_asal_id' => 'required|exists:kelas,id',
            'action_type' => 'required|in:naik_kelas,lulus',
            'kelas_tujuan_id' => 'required_if:action_type,naik_kelas|nullable|exists:kelas,id|different:kelas_asal_id',
            'siswa_ids' => 'required|array|min:1',
            'siswa_ids.*' => 'exists:siswa,nis',
        ], [
            'kelas_asal_id.required' => 'Kelas asal harus dipilih',
            'action_type.required' => 'Pilih tipe aksi (Naik Kelas atau Lulus)',
            'kelas_tujuan_id.required_if' => 'Kelas tujuan harus dipilih untuk naik kelas',
            'kelas_tujuan_id.different' => 'Kelas tujuan harus berbeda dengan kelas asal',
            'siswa_ids.required' => 'Pilih minimal 1 siswa',
            'siswa_ids.min' => 'Pilih minimal 1 siswa',
        ]);

        DB::beginTransaction();
        try {
            $kelasAsal = Kelas::findOrFail($request->kelas_asal_id);
            $actionType = $request->action_type;
            $updated = 0;
            $message = '';

            if ($actionType === 'lulus') {
                // LULUS: Update status jadi 'lulus'
                $updated = Siswa::whereIn('nis', $request->siswa_ids)
                    ->where('kelas_id', $request->kelas_asal_id)
                    ->where('status', 'aktif')
                    ->update([
                        'status' => 'lulus'
                    ]);

                $message = "✅ Berhasil meluluskan {$updated} siswa dari {$kelasAsal->kelas}";
            } else {
                // NAIK KELAS: Update kelas_id
                $kelasTujuan = Kelas::findOrFail($request->kelas_tujuan_id);

                $updated = Siswa::whereIn('nis', $request->siswa_ids)
                    ->where('kelas_id', $request->kelas_asal_id)
                    ->where('status', 'aktif')
                    ->update([
                        'kelas_id' => $request->kelas_tujuan_id
                    ]);

                $message = "✅ Berhasil menaikkan {$updated} siswa dari {$kelasAsal->kelas} ke {$kelasTujuan->kelas}";
            }

            DB::commit();

            return redirect()->route('siswa.naik-kelas')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Update status siswa (single)
     */
    public function updateStatus(Request $request, $nis)
    {
        $request->validate([
            'status' => 'required|in:aktif,tidak_aktif,lulus,keluar',
        ]);

        try {
            $siswa = Siswa::where('nis', $nis)->firstOrFail();
            $statusLama = $siswa->status;

            $siswa->update([
                'status' => $request->status
            ]);

            $statusBaru = ucfirst(str_replace('_', ' ', $request->status));

            return redirect()->back()
                ->with('success', "✅ Status siswa {$siswa->nama} berhasil diubah menjadi {$statusBaru}");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }

    /**
     * Bulk update status
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'siswa_ids' => 'required|array|min:1',
            'siswa_ids.*' => 'exists:siswa,nis',
            'status' => 'required|in:aktif,tidak_aktif,lulus,pindah,keluar',
        ]);

        DB::beginTransaction();
        try {
            $updated = Siswa::whereIn('nis', $request->siswa_ids)
                ->update(['status' => $request->status]);

            DB::commit();

            $statusText = ucfirst(str_replace('_', ' ', $request->status));
            return back()->with('success', "Berhasil mengubah status {$updated} siswa menjadi {$statusText}");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    public function export(Request $request)
    {
        $status = $request->input('status');
        $kelasId = $request->input('kelas_id');
        $jurusanId = $request->input('jurusan_id');

        $fileName = 'data-siswa-' . date('Y-m-d-His') . '.xlsx';

        return Excel::download(
            new SiswaExport($status, $kelasId, $jurusanId),
            $fileName
        );
    }

    /**
     * Download template Excel
     */
    public function downloadTemplate()
    {
        $fileName = 'template-import-siswa.xlsx';
        $filePath = public_path('templates/' . $fileName);

        // Cek apakah template ada
        if (file_exists($filePath)) {
            return response()->download($filePath);
        }

        // Jika tidak ada, generate template baru
        return $this->generateTemplate();
    }

    /**
     * Generate template Excel
     */
    protected function generateTemplate()
{
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    
    // ========================================
    // SHEET 1: DATA SISWA (HARUS SHEET PERTAMA!)
    // ========================================
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Data Siswa'); // 🔧 Kasih nama yang jelas

    // Header
    $headers = [
        'NIS',
        'Nama',
        'Kelas',
        'Jurusan',
        'Tahun Masuk',
        'Jenis Kelamin',
        'Tanggal Lahir',
        'Alamat',
        'Wali',
        'Kontak',
        'Status',
    ];

    $sheet->fromArray([$headers], null, 'A1');

    // Contoh data
    $exampleData = [
        [
            '12345',
            'JOHN DOE',
            'X',
            'IPA',
            '2024',
            'L',
            '01-01-2010',
            'Jl. Contoh No. 123',
            'Casmono',
            '081234567890',
            'aktif',
        ],
        [
            '12346',
            'JANE SMITH',
            'X',
            'IPS',
            '2024',
            'P',
            '15-03-2010',
            'Jl. Contoh No. 456',
            'Casmudi',
            '081234567891',
            'aktif',
        ],
    ];

    $sheet->fromArray($exampleData, null, 'A2');

    // Styling header
    $headerStyle = [
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => '4472C4'],
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        ],
    ];

    $sheet->getStyle('A1:K1')->applyFromArray($headerStyle);

    // Width kolom
    $sheet->getColumnDimension('A')->setWidth(15);
    $sheet->getColumnDimension('B')->setWidth(30);
    $sheet->getColumnDimension('C')->setWidth(12);
    $sheet->getColumnDimension('D')->setWidth(15);
    $sheet->getColumnDimension('E')->setWidth(15);
    $sheet->getColumnDimension('F')->setWidth(15);
    $sheet->getColumnDimension('G')->setWidth(15);
    $sheet->getColumnDimension('H')->setWidth(40);
    $sheet->getColumnDimension('I')->setWidth(25);
    $sheet->getColumnDimension('J')->setWidth(18);
    $sheet->getColumnDimension('K')->setWidth(15);

    // 🔧 PERBAIKAN: Tambahkan border dan freeze pane
    $sheet->getStyle('A1:K3')->getBorders()->getAllBorders()
        ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    $sheet->freezePane('A2'); // Freeze header row

    // ========================================
    // SHEET 2: INSTRUKSI (PINDAH KE BELAKANG!)
    // ========================================
    $instructionSheet = $spreadsheet->createSheet();
    $instructionSheet->setTitle('Instruksi');

    $instructions = [
        ['INSTRUKSI IMPORT DATA SISWA'],
        [''],
        ['⚠️ PENTING: Pastikan sheet aktif adalah "Data Siswa", bukan "Instruksi"!'],
        ['⚠️ WAJIB: Hapus baris contoh (John Doe & Jane Smith) sebelum import data asli!'],
        [''],
        ['PENJELASAN KOLOM:'],
        ['1. NIS: Nomor Induk Siswa (WAJIB, unik, hanya angka)'],
        ['2. Nama: Nama lengkap siswa (WAJIB, otomatis uppercase)'],
        ['3. Kelas: X, XI, atau XII (WAJIB)'],
        ['4. Jurusan: IPA, IPS, atau lainnya (WAJIB)'],
        ['5. Tahun Masuk: Tahun masuk siswa (contoh: 2024, default: tahun sekarang)'],
        ['6. Jenis Kelamin: L atau P (WAJIB)'],
        ['7. Tanggal Lahir: Format dd-mm-yyyy (contoh: 01-01-2010)'],
        ['8. Alamat: Alamat lengkap (opsional, default: -)'],
        ['9. Wali: Nama wali/orang tua (opsional, default: -)'],
        ['10. Kontak: Nomor HP wali/siswa (opsional, default: -)'],
        ['11. Status: aktif, tidak_aktif, lulus, keluar (default: aktif)'],
        [''],
        ['CATATAN PENTING:'],
        ['✓ JANGAN ubah urutan dan nama header kolom'],
        ['✓ Jika NIS sudah ada, data akan di-UPDATE'],
        ['✓ Jika NIS baru, otomatis dibuatkan akun login (username=NIS, password=tanggal lahir ddmmyyyy)'],
        ['✓ Kelas/Jurusan yang belum ada akan otomatis dibuat'],
        [''],
        ['FORMAT YANG VALID:'],
        ['• Tanggal: 01-01-2010, 2010-01-01, 01/01/2010'],
        ['• Jenis Kelamin: L, P, Laki-laki, Perempuan, Male, Female'],
        ['• Status: aktif, tidak_aktif, lulus, keluar'],
        [''],
        ['TROUBLESHOOTING:'],
        ['• Import gagal? Cek log error di sistem'],
        ['• Data tidak masuk? Pastikan tidak ada row kosong di tengah data'],
        ['• NIS sudah ada? Data akan di-update, tidak error'],
    ];

    $instructionSheet->fromArray($instructions, null, 'A1');
    $instructionSheet->getColumnDimension('A')->setWidth(90);
    $instructionSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    $instructionSheet->getStyle('A3')->getFont()->setBold(true)->setSize(12)
        ->getColor()->setRGB('FF0000'); // Warning merah

    // 🔧 PERBAIKAN: Set sheet "Data Siswa" sebagai active sheet
    $spreadsheet->setActiveSheetIndex(0);

    // Generate file
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="template-import-siswa.xlsx"');
    header('Cache-Control: max-age=0');

    $writer->save('php://output');
    exit;
}


    /**
     * Show import form
     */
    public function importForm()
    {
        return view('admin.master_data.siswa.import');
    }

    /**
     * Process import
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:5120', // Max 5MB
        ], [
            'file.required' => 'File Excel harus diupload',
            'file.mimes' => 'File harus berformat Excel (.xlsx atau .xls)',
            'file.max' => 'Ukuran file maksimal 5MB',
        ]);


        try {
            $import = new SiswaImport();
            Excel::import($import, $request->file('file'));

            $results = $import->getResults();
            $failures = $import->failures();
            // DEBUG: Lihat detail errors
            if (!empty($results['errors'])) {
                \log()::error('Import Errors:', $results['errors']);

                // Tampilkan di session untuk debugging
                session()->flash('debug_errors', $results['errors']);
            }
            // Build success message
            $message = "Import selesai! ";
            $message .= "Berhasil import: {$results['imported']}, ";
            $message .= "Diupdate: {$results['updated']}, ";
            $message .= "Dilewati: {$results['skipped']}";

            return redirect()
                ->route('siswa.index')
                ->with('success', $message)
                ->with('import_failures', $failures)
                ->with('import_errors', $results['errors']);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();

            return redirect()
                ->back()
                ->with('error', 'Terdapat kesalahan validasi pada file Excel')
                ->with('import_failures', $failures);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }
}
