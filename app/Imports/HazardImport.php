<?php

namespace App\Imports;

use Throwable;
use Carbon\Carbon;
use App\Models\Hazard;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class HazardImport implements ToModel, WithHeadingRow, SkipsOnError, WithChunkReading
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Jika semua kolom kosong, baru skip barisnya (untuk mencegah baris kosong total masuk)
        if (count(array_filter($row, fn($value) => !is_null($value) && $value !== '')) === 0) {
            return null;
        }

        // Parsing tanggal: contoh "2025-08-01 : 12:00"
        $tanggal = null;
        if (!empty($row['tanggal'])) {
            $tanggal = str_replace([' : ', '–'], [' ', '-'], $row['tanggal']);
            try {
                $tanggal = Carbon::parse($tanggal)->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                $tanggal = now();
            }
        }

        return new Hazard([
            'no_referensi'                => $row['no_referensi'] ?? null,
            'event_type_id'               => $row['event_type_id'] ?? null,
            'event_sub_type_id'           => $row['event_sub_type_id'] ?? null,
            'status'                      => $row['status'] ?? 'submitted',
            'department_id'               => $row['department_id'] ?? null,
            'contractor_id'               => $row['contractor_id'] ?? null,
            'penanggung_jawab_id'         => $row['penanggung_jawab_id'] ?? null,
            'pelapor_id'                  => $row['pelapor_id'] ?? null,
            'manualPelaporName'           => $row['manualpelaporname'] ?? null,
            'location_id'                 => $row['location_id'] ?? null,
            'location_specific'           => $row['location_specific'] ?? null,
            'tanggal'                     => $tanggal,
            'description'                 => $row['description'] ?? null,
            'doc_deskripsi'               => $row['doc_deskripsi'] ?? null,
            'immediate_corrective_action' => $row['immediate_corrective_action'] ?? null,
            'doc_corrective'              => $row['doc_corrective'] ?? null,
            'key_word'                    => $row['key_word'] ?? null,
            'kondisi_tidak_aman_id'       => $row['kondisi_tidak_aman_id'] ?? null,
            'tindakan_tidak_aman_id'      => $row['tindakan_tidak_aman_id'] ?? null,
            'consequence_id'              => $row['consequence_id'] ?? null,
            'likelihood_id'               => $row['likelihood_id'] ?? null,
            'risk_level'                  => $row['risk_level'] ?? null,
        ]);
    }

    public function chunkSize(): int
    {
        return 500; // proses per 500 baris untuk efisiensi
    }

    public function onError(Throwable $error)
    {
        // Abaikan error per baris agar import tidak berhenti
    }
}
