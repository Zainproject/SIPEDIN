<?php

namespace App\Imports;

use App\Models\Petugas;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PetugasImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

            $nip = trim((string)($row['nip'] ?? ''));
            if ($nip === '') continue;

            Petugas::updateOrCreate(
                ['nip' => $nip],
                [
                    'nama'    => $row['nama'] ?? null,
                    'pangkat' => $row['pangkat'] ?? null,
                    'jabatan' => $row['jabatan'] ?? null,
                ]
            );
        }
    }
}
