<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Jurusan;
use App\Models\Login;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SiswaImport implements 
    ToCollection, 
    WithHeadingRow, 
    WithValidation, 
    SkipsOnFailure,
    WithStartRow
{
    use SkipsFailures;

    protected $imported = 0;
    protected $updated = 0;
    protected $skipped = 0;
    protected $errors = [];

    /**
     * Mulai dari row 2 (row 1 adalah header)
     */
    public function startRow(): int
    {
        return 2;
    }

    /**
     * Row 1 adalah header
     */
    public function headingRow(): int
    {
        return 1;
    }

    /**
     * Process import dengan SATU transaction untuk semua rows
     */
    public function collection(Collection $rows)
    {
        Log::info("=== IMPORT SISWA START ===");
        Log::info("Total rows: " . $rows->count());

        if ($rows->count() > 0) {
            $firstRow = $rows->first();
            Log::info("Headers detected:", array_keys($firstRow->toArray()));
            Log::info("First row sample:", $firstRow->toArray());
        }

        // SATU transaction untuk semua data
        DB::beginTransaction();

        try {
            foreach ($rows as $index => $row) {
                try {
                    // Skip row kosong
                    if ($this->isEmptyRow($row)) {
                        Log::info("Row " . ($index + 2) . " is empty, skipping");
                        continue;
                    }

                    // Validasi data wajib
                    if (empty($row['nis']) || empty($row['nama'])) {
                        throw new \Exception('NIS dan Nama wajib diisi');
                    }

                    Log::info("Processing row " . ($index + 2), [
                        'nis' => $row['nis'],
                        'nama' => $row['nama']
                    ]);

                    // Find or create Kelas & Jurusan
                    $kelas = $this->findOrCreateKelas($row['kelas'] ?? null);
                    $jurusan = $this->findOrCreateJurusan($row['jurusan'] ?? null);

                    // Parse data
                    $tanggalLahir = $this->parseTanggalLahir($row['tanggal_lahir'] ?? null);
                    $jenisKelamin = $this->parseJenisKelamin($row['jenis_kelamin'] ?? null);
                    $status = $this->parseStatus($row['status'] ?? null);
                    $tahunMasuk = $this->parseTahunMasuk($row['tahun_masuk'] ?? null);

                    // Cek siswa existing berdasarkan NIS
                    $siswa = Siswa::where('nis', $row['nis'])->first();

                    // Data siswa (SESUAI NAMA KOLOM DI DATABASE!)
                    $dataSiswa = [
                        'nama' => strtoupper(trim($row['nama'])),
                        'kelas_id' => $kelas->id,
                        'jurusan_id' => $jurusan->id,
                        'tahun_masuk' => $tahunMasuk,
                        'jenis_kelamin' => $jenisKelamin,
                        'tgl_lahir' => $tanggalLahir,        // ✅ SESUAI DB
                        'alamat' => isset($row['alamat']) ? trim($row['alamat']) : '-',
                        'wali' => isset($row['wali']) ? trim($row['wali']) : '-',
                        'kontak' => isset($row['kontak']) ? trim($row['kontak']) : '-',  // ✅ SESUAI DB
                        'status' => $status,
                    ];

                    if ($siswa) {
                        // UPDATE siswa existing
                        $siswa->update($dataSiswa);
                        $this->updated++;
                        
                        Log::info("Siswa UPDATED", ['nis' => $row['nis']]);
                    } else {
                        // CREATE siswa baru dengan login account
                        $loginId = $this->createLoginAccount($row['nis'], $tanggalLahir);
                        
                        Siswa::create(array_merge(
                            [
                                'nis' => $row['nis'],
                                'login_id' => $loginId,
                            ],
                            $dataSiswa
                        ));
                        
                        $this->imported++;
                        Log::info("Siswa IMPORTED", ['nis' => $row['nis']]);
                    }

                } catch (\Exception $e) {
                    $this->skipped++;
                    $errorMsg = $e->getMessage();
                    
                    $this->errors[] = [
                        'row' => $index + 2,
                        'nis' => $row['nis'] ?? 'N/A',
                        'nama' => $row['nama'] ?? 'N/A',
                        'error' => $errorMsg,
                    ];
                    
                    Log::error("Import FAILED row " . ($index + 2), [
                        'nis' => $row['nis'] ?? 'N/A',
                        'error' => $errorMsg,
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            DB::commit();
            
            $results = $this->getResults();
            Log::info("=== IMPORT COMPLETED ===", $results);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Import transaction FAILED", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Check if row is empty
     */
    protected function isEmptyRow($row)
    {
        $filtered = array_filter($row->toArray(), function($value) {
            return !is_null($value) && trim($value) !== '';
        });
        
        return empty($filtered);
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'nis' => 'required',
            'nama' => 'required|string|max:100',
            'kelas' => 'required|string',
            'jurusan' => 'required|string',
            'tahun_masuk' => 'nullable',
            'jenis_kelamin' => 'required|string',
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            'nis.required' => 'NIS wajib diisi',
            'nama.required' => 'Nama wajib diisi',
            'kelas.required' => 'Kelas wajib diisi',
            'jurusan.required' => 'Jurusan wajib diisi',
            'jenis_kelamin.required' => 'Jenis kelamin wajib diisi',
        ];
    }

    /**
     * Find or create Kelas
     */
    protected function findOrCreateKelas($kelasName)
    {
        if (!$kelasName || trim($kelasName) === '' || $kelasName === '-') {
            throw new \Exception('Kelas tidak boleh kosong');
        }

        $kelasName = strtoupper(trim($kelasName));

        return Kelas::firstOrCreate(
            ['kelas' => $kelasName],
            ['status' => 'aktif']
        );
    }

    /**
     * Find or create Jurusan
     */
    protected function findOrCreateJurusan($jurusanName)
    {
        if (!$jurusanName || trim($jurusanName) === '' || $jurusanName === '-') {
            throw new \Exception('Jurusan tidak boleh kosong');
        }

        $jurusanName = strtoupper(trim($jurusanName));

        return Jurusan::firstOrCreate(
            ['nama' => $jurusanName],  // ✅ Sesuai DB: kolom 'nama' bukan 'nama_jurusan'
            ['status' => 'aktif']
        );
    }

    /**
     * Parse tahun masuk
     */
    protected function parseTahunMasuk($tahun)
    {
        if (!$tahun || trim($tahun) === '' || $tahun === '-') {
            return date('Y'); // Default tahun sekarang
        }

        $tahun = trim($tahun);
        
        // Validasi format tahun (4 digit)
        if (is_numeric($tahun) && strlen($tahun) === 4) {
            return (int) $tahun;
        }

        return date('Y');
    }

    /**
     * Parse tanggal lahir dengan multiple format support
     */
    protected function parseTanggalLahir($tanggal)
    {
        if (!$tanggal || trim($tanggal) === '' || $tanggal === '-') {
            return date('Y-m-d'); // Default hari ini
        }

        // Handle Excel date serial number
        if (is_numeric($tanggal)) {
            try {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggal);
                return $date->format('Y-m-d');
            } catch (\Exception $e) {
                // Bukan serial number, lanjut ke parsing manual
            }
        }

        // Try multiple date formats
        $formats = ['d-m-Y', 'Y-m-d', 'd/m/Y', 'Y/m/d', 'd-M-Y', 'Y-M-d'];

        foreach ($formats as $format) {
            try {
                $date = Carbon::createFromFormat($format, trim($tanggal));
                return $date->format('Y-m-d');
            } catch (\Exception $e) {
                continue;
            }
        }

        // Last resort: strtotime
        $timestamp = strtotime($tanggal);
        if ($timestamp !== false) {
            return date('Y-m-d', $timestamp);
        }

        // Jika semua gagal, gunakan default
        Log::warning("Invalid date format: {$tanggal}, using current date");
        return date('Y-m-d');
    }

    /**
     * Parse jenis kelamin
     */
    protected function parseJenisKelamin($jk)
    {
        if (!$jk || trim($jk) === '') {
            throw new \Exception('Jenis kelamin tidak boleh kosong');
        }

        $jk = strtolower(trim($jk));

        if (in_array($jk, ['l', 'laki', 'laki-laki', 'male', 'm', 'pria'])) {
            return 'L';
        }

        if (in_array($jk, ['p', 'perempuan', 'female', 'f', 'wanita', 'cewe', 'cewek'])) {
            return 'P';
        }

        throw new \Exception("Jenis kelamin tidak valid: '{$jk}'. Gunakan L atau P");
    }

    /**
     * Parse status
     */
    protected function parseStatus($status)
    {
        if (!$status || trim($status) === '' || $status === '-') {
            return 'aktif'; // Default
        }

        $status = strtolower(str_replace(' ', '_', trim($status)));

        // Valid statuses dari DB: 'aktif','tidak_aktif','lulus','keluar'
        $validStatuses = ['aktif', 'tidak_aktif', 'lulus', 'keluar'];
        
        return in_array($status, $validStatuses) ? $status : 'aktif';
    }

    /**
     * Create login account untuk siswa baru
     * Return login_id
     */
    protected function createLoginAccount($nis, $tanggalLahir)
    {
        // Cek apakah username (NIS) sudah ada
        $existingLogin = Login::where('username', $nis)->first();
        
        if ($existingLogin) {
            return $existingLogin->id;
        }

        // Password default: tanggal lahir (ddmmyyyy) atau 12345678
        $password = '12345678'; // Default
        
        if ($tanggalLahir) {
            try {
                $password = Carbon::parse($tanggalLahir)->format('dmY');
            } catch (\Exception $e) {
                $password = '12345678';
            }
        }

        $login = Login::create([
            'username' => $nis,
            'password' => Hash::make($password),
            'role' => 'siswa',
        ]);

        return $login->id;
    }

    /**
     * Get import results
     */
    public function getResults()
    {
        return [
            'imported' => $this->imported,
            'updated' => $this->updated,
            'skipped' => $this->skipped,
            'errors' => $this->errors,
        ];
    }
}