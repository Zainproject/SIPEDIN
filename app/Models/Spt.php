<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Petugas;
use App\Models\Poktan;

class Spt extends Model
{
    use HasFactory;

    /**
     * =========================
     * MASS ASSIGNMENT
     * =========================
     */
    protected $fillable = [
        // step 1
        'petugas',
        'tujuan',
        'poktan_nama',
        'deskripsi_kota',
        'deskripsi_lainnya',

        'keperluan',
        'kehadiran',

        'alat_angkut',
        'berangkat_dari',

        // step 2
        'tanggal_berangkat',
        'tanggal_kembali',

        // lama (legacy)
        'biaya_perhari',

        // biaya baru
        'keterangan_biaya',
        'harga_biaya',
        'lama_hari',
        'subtotal_perhari',
        'total_biaya',

        // step 3
        'bulan',
        'tahun',
        'mak',
        'nomor_surat',
        'nomor_kwitansi',

        // step 4
        'arahan',
        'masalah',
        'saran',
        'lainnya',
    ];

    /**
     * =========================
     * CAST JSON
     * =========================
     */
    protected $casts = [
        'petugas' => 'array',
        'tujuan' => 'array',
        'poktan_nama' => 'array',
        'deskripsi_kota' => 'array',
        'deskripsi_lainnya' => 'array',

        'keterangan_biaya' => 'array',
        'harga_biaya' => 'array',

        'arahan' => 'array',
        'masalah' => 'array',
        'saran' => 'array',
        'lainnya' => 'array',
    ];

    /**
     * =========================
     * HELPER NORMALISASI ARRAY
     * (aman untuk JSON / string / koma)
     * =========================
     */
    protected function normalizeToArray($val): array
    {
        if (is_array($val)) return $val;

        if (is_string($val)) {
            $val = trim($val);
            if ($val === '') return [];

            // JSON string
            $decoded = json_decode($val, true);
            if (is_array($decoded)) return $decoded;

            // comma / semicolon / pipe separated
            $val = str_replace([';', '|'], ',', $val);
            return array_values(array_filter(array_map('trim', explode(',', $val))));
        }

        return [];
    }

    /**
     * =========================
     * PETUGAS LIST (AMAN)
     * =========================
     */
    public function petugasList()
    {
        $list = $this->normalizeToArray($this->attributes['petugas'] ?? $this->petugas);

        if (count($list) === 0) return collect();

        return Petugas::whereIn('nip', $list)->get();
    }

    /**
     * =========================
     * POKTAN LIST (AMAN)
     * =========================
     */
    public function poktanList()
    {
        $list = $this->normalizeToArray($this->attributes['poktan_nama'] ?? $this->poktan_nama);

        if (count($list) === 0) return collect();

        return Poktan::whereIn('nama_poktan', $list)->get();
    }

    /**
     * =========================
     * ACCESSOR: LAMA HARI
     * =========================
     */
    public function getLamaHariAttribute($value)
    {
        if (!is_null($value)) return (int) $value;

        if (!$this->tanggal_berangkat || !$this->tanggal_kembali) return 0;

        return Carbon::parse($this->tanggal_berangkat)
            ->diffInDays(Carbon::parse($this->tanggal_kembali)) + 1;
    }

    /**
     * =========================
     * ACCESSOR: SUBTOTAL PER HARI
     * =========================
     */
    public function getSubtotalPerhariAttribute($value)
    {
        if (!is_null($value)) return (float) $value;

        if (is_array($this->harga_biaya) && count($this->harga_biaya)) {
            return collect($this->harga_biaya)->sum(fn($v) => (float) $v);
        }

        // fallback legacy
        return (float) ($this->biaya_perhari ?? 0);
    }

    /**
     * =========================
     * ACCESSOR: TOTAL BIAYA
     * =========================
     */
    public function getTotalBiayaAttribute($value)
    {
        if (!is_null($value)) return (float) $value;

        return (float) ($this->lama_hari * $this->subtotal_perhari);
    }

    /**
     * =========================
     * RELASI (OPTIONAL / LEGACY)
     * =========================
     * Dipakai kalau suatu saat kamu benar-benar pakai FK
     */
    public function petugasRel()
    {
        return $this->belongsTo(Petugas::class, 'petugas_id');
    }

    public function poktanRel()
    {
        return $this->belongsTo(Poktan::class, 'poktan_id');
    }
}
