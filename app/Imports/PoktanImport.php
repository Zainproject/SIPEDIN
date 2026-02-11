<?php

namespace App\Imports;

use App\Models\Poktan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PoktanImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

            $nama = trim((string)($row['nama_poktan'] ?? ''));
            if ($nama === '') continue;

            Poktan::updateOrCreate(
                ['nama_poktan' => $nama],
                [
                    'ketua'     => $row['ketua'] ?? null,
                    'desa'      => $row['desa'] ?? null,
                    'kecamatan' => $row['kecamatan'] ?? null,
                ]
            );
        }
    }
}
