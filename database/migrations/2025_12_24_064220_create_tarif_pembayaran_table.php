<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Tabel pivot untuk tarif jenis pembayaran per tahun ajaran
        Schema::create('tarif_pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_pembayaran_id')->constrained('jenis_pembayaran')->onDelete('cascade');
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->onDelete('cascade');
            $table->decimal('nominal', 15, 2)->comment('Nominal untuk tahun ajaran ini');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            // Unique constraint: 1 jenis pembayaran hanya punya 1 tarif per tahun ajaran
            $table->unique(['jenis_pembayaran_id', 'tahun_ajaran_id'], 'unique_tarif_per_tahun');
        });

        // Migrate data existing dari jenis_pembayaran ke tarif_pembayaran
        $tahunAjaranAktif = DB::table('tahun_ajaran')
            ->where('status', 'aktif')
            ->first();

        if ($tahunAjaranAktif) {
            // Ambil semua jenis pembayaran yang punya nominal
            $jenisPembayaran = DB::table('jenis_pembayaran')->get();

            foreach ($jenisPembayaran as $jp) {
                // Cek apakah ada kolom nominal
                if (property_exists($jp, 'nominal')) {
                    DB::table('tarif_pembayaran')->insert([
                        'jenis_pembayaran_id' => $jp->id,
                        'tahun_ajaran_id' => $tahunAjaranAktif->id,
                        'nominal' => $jp->nominal,
                        'keterangan' => 'Migrasi dari data lama',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('tarif_pembayaran');
    }
};