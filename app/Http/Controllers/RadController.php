<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class RadController extends Controller
{
    public function print(int $idRad): Response
    {
        try {
            $result = DB::select(
                'EXEC dbo.RadiologiResultKwitNew_SP ?',
                [$idRad]
            );

            abort_if(
                empty($result),
                404,
                "Data radiologi {$idRad} tidak ditemukan."
            );

            $header = $result[0];

            $pemeriksaan = collect($result)
                ->map(function ($item, $index) {
                    return [
                        'no' => $index + 1,
                        'periksa' => $item->Periksa ?? '-',
                        'hasil' => $this->formatResult(
                            $item->Result ?? null
                        ),
                        'alat' => $item->AlatName ?? '-',
                    ];
                })
                ->values()
                ->all();

            $data = [
                'patient' => [
                    'regNum' => $header->RegNum ?? '-',
                    'nama' => $header->Nama ?? '-',
                    'gender' => $this->formatGender(
                        $header->Jenis_Kelamin ?? null
                    ),
                    'alamat' => $this->formatAlamat(
                        $header->Addr ?? null,
                        $header->Kelurahan ?? null
                    ),
                    'tanggalLahir' => $header->Tanggal_Lahir ?? null,
                ],

                'radiologi' => [
                    'idRad' => $header->IDRad ?? $idRad,
                    'idReg' => $header->IDReg ?? '-',
                    'tanggal' => $header->TRad ?? null,
                    'dokter' => $header->Dokter ?? '-',
                    'umurTahun' => $header->TH ?? 0,
                    'umurBulan' => $header->Bln ?? 0,
                    'umurHari' => $header->Hr ?? 0,
                    'alat' => $header->AlatName ?? '-',
                    'kelas' => $header->Klas ?? '-',
                    'user' => $header->Usr ?? '-',
                    'shift' => $header->Shift ?? '-',
                    'dokterRadiologi' => $header->dr ?? '-',     
                    'ttd' => $this->formatSignatureImage(
                        $header->Ttd ?? null
                    ),
                    'details' => $header->Details ?? null,
                ],

                'pemeriksaan' => $pemeriksaan,

                'hospital' => [
                    'name' =>
                        'INSTALASI RADIOLOGI RUMAH SAKIT AISYIYAH BOJONEGORO',

                    'address' =>
                        'Jl. Panglima Sudirman 48 Bojonegoro Telp. 0353-881748. Fax 0353-88597',
                ],

                'printedAt' => now('Asia/Jakarta'),
            ];

            $pdf = Pdf::loadView(
                'rad.rad',
                $data
            )
                ->setPaper('a4', 'portrait')
                ->setOption('isRemoteEnabled', true)
                ->setOption('isHtml5ParserEnabled', true);

            return $pdf->stream(
                "hasil-radiologi-{$idRad}.pdf"
            );
        } catch (Throwable $e) {
            Log::error('Gagal mencetak hasil radiologi', [
                'id_rad' => $idRad,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            if (config('app.debug')) {
                dd([
                    'idRad' => $idRad,
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
            }

            abort(
                500,
                'Hasil pemeriksaan radiologi gagal dicetak.'
            );
        }
    }

    private function formatAlamat(
        mixed $alamat,
        mixed $kelurahan
    ): string {
        return collect([
            trim((string) $alamat),
            trim((string) $kelurahan),
        ])
            ->filter()
            ->unique()
            ->implode(' ') ?: '-';
    }

    private function formatGender(mixed $gender): string
    {
        $value = strtoupper(trim((string) $gender));

        return match ($value) {
            'L',
            'LAKI-LAKI',
            'LAKI LAKI',
            'M',
            'MALE' => 'L',

            'P',
            'PEREMPUAN',
            'F',
            'FEMALE' => 'P',

            default => $gender ?: '-',
        };
    }

    private function formatResult(mixed $result): string
    {
        if (!$result) {
            return '-';
        }

        $text = str_replace(
            ["\r\n", "\r"],
            "\n",
            (string) $result
        );

        return trim($text);
    }

    private function formatSignatureImage(mixed $ttd): ?string
    {
        if (empty($ttd)) {
            return null;
        }

        /*
        * Jika sudah berupa data URI.
        */
        if (
            is_string($ttd) &&
            str_starts_with($ttd, 'data:image/')
        ) {
            return $ttd;
        }

        /*
        * Jika isi database berupa path file.
        */
        if (is_string($ttd) && is_file($ttd)) {
            $mime = mime_content_type($ttd) ?: 'image/png';

            return 'data:' . $mime . ';base64,' .
                base64_encode(file_get_contents($ttd));
        }

        /*
        * Jika isi database berupa path relatif di public.
        */
        if (is_string($ttd)) {
            $publicFile = public_path(
                ltrim(str_replace('\\', '/', $ttd), '/')
            );

            if (is_file($publicFile)) {
                $mime = mime_content_type($publicFile)
                    ?: 'image/png';

                return 'data:' . $mime . ';base64,' .
                    base64_encode(
                        file_get_contents($publicFile)
                    );
            }
        }

        /*
        * Jika isi database sudah berupa base64 tanpa prefix.
        */
        if (is_string($ttd)) {
            $clean = preg_replace(
                '/\s+/',
                '',
                $ttd
            );

            $decoded = base64_decode(
                $clean,
                true
            );

            if ($decoded !== false) {
                $mime = $this->detectImageMime($decoded);

                if ($mime !== null) {
                    return 'data:' . $mime . ';base64,' .
                        base64_encode($decoded);
                }
            }
        }

        /*
        * Jika isi SQL Server berupa VARBINARY / IMAGE.
        */
        if (is_string($ttd)) {
            $mime = $this->detectImageMime($ttd);

            if ($mime !== null) {
                return 'data:' . $mime . ';base64,' .
                    base64_encode($ttd);
            }
        }

        Log::warning('Format tanda tangan radiologi tidak dikenali');

        return null;
    }

    private function detectImageMime(string $binary): ?string
    {
        if (str_starts_with($binary, "\x89PNG")) {
            return 'image/png';
        }

        if (str_starts_with($binary, "\xFF\xD8\xFF")) {
            return 'image/jpeg';
        }

        if (str_starts_with($binary, 'GIF87a')) {
            return 'image/gif';
        }

        if (str_starts_with($binary, 'GIF89a')) {
            return 'image/gif';
        }

        if (
            substr($binary, 0, 4) === 'RIFF' &&
            substr($binary, 8, 4) === 'WEBP'
        ) {
            return 'image/webp';
        }

        return null;
    }

    public function printEditRadiologi($id)
    {
        try {

            // =====================================================
            // AMBIL DATA RADIOLOGI EDIT
            // BERDASARKAN ID REGISTRASI
            // =====================================================

            $rows = DB::select(
                "EXEC dbo.WebRadiologiResultKwitEditIDReg_SP ?",
                [$id]
            );


            if (empty($rows)) {

                return response(
                    '<h3 style="
                        font-family:Arial;
                        text-align:center;
                        margin-top:50px;
                    ">
                        Data Edit Radiologi belum tersedia.
                    </h3>',
                    404
                );
            }


            // =====================================================
            // DATA PASIEN
            // =====================================================

            $first = $rows[0];


            $patientR = [

                'nama' =>
                    $first->Nama ?? '-',

                'regNum' =>
                    $first->RegNum ?? '-',

                'idReg' =>
                    $first->IDReg ?? $id,

                'addr' =>
                    trim(
                        ($first->Addr ?? '')
                        . ' '
                        . ($first->Kelurahan ?? '')
                    ) ?: '-',

                'gender' =>
                    $first->Jenis_Kelamin ?? '-',

                'dob' =>
                    $first->Tanggal_Lahir ?? null,

            ];


            // =====================================================
            // GROUP PER ID RAD
            // =====================================================

            $groupRad = collect($rows)
                ->groupBy('IDRad');


            $rads = [];


            foreach ($groupRad as $idRad => $items) {

                $firstRad =
                    $items->first();


                // ================================================
                // ITEM PEMERIKSAAN
                // ================================================

                $periks =
                    $items
                        ->map(function ($row) {

                            return [

                                'radio_id' =>
                                    $row->Radio_ID ?? null,

                                'periksa_id' =>
                                    $row->PeriksaID ?? null,

                                'periksa' =>
                                    $row->Periksa ?? '-',

                                'result' =>
                                    $row->Result ?? '',

                                'biaya' =>
                                    $row->Biaya ?? 0,

                            ];

                        })
                        ->values()
                        ->toArray();


                // ================================================
                // DATA PER ID RAD
                // ================================================

                $rads[] = [

                    'idrad' =>
                        $idRad,

                    'trad' =>
                        $firstRad->TRad ?? null,

                    'dokter' =>
                        $firstRad->Dokter ?? '-',

                    'alatname' =>
                        $firstRad->AlatName ?? '-',

                    'klas' =>
                        $firstRad->Klas ?? '-',

                    'usr' =>
                        $firstRad->Usr ?? '-',

                    'shift' =>
                        $firstRad->Shift ?? '-',

                    'dr' =>
                        $firstRad->dr ?? '-',

                    'ttd' =>
                        $this->formatSignatureImage(
                            $firstRad->Ttd ?? null
                        ),

                    'th' =>
                        $firstRad->TH ?? 0,

                    'bln' =>
                        $firstRad->Bln ?? 0,

                    'hr' =>
                        $firstRad->Hr ?? 0,

                    'periks' =>
                        $periks,

                ];
            }


            // =====================================================
            // DATA RS
            // =====================================================

            $hospitalR = [

                'name' =>
                    "INSTALASI RADIOLOGI RUMAH SAKIT 'AISYIYAH BOJONEGORO",

                'address' =>
                    'JL. PANGLIMA SUDIRMAN 48 BOJONEGORO TELP. 0353-881748 FAX 0353-88597'

            ];


            $printedAt =
                now('Asia/Jakarta');


            // =====================================================
            // GENERATE PDF
            // =====================================================

            $pdf = Pdf::loadView(
                'rad.print-edit-rad',
                compact(
                    'patientR',
                    'rads',
                    'hospitalR',
                    'printedAt'
                )
            );


            $pdf->setPaper(
                'A4',
                'portrait'
            );


            $pdf->setOption(
                'isRemoteEnabled',
                true
            );


            $pdf->setOption(
                'isHtml5ParserEnabled',
                true
            );


            $pdf->setOption(
                'chroot',
                base_path()
            );


            return $pdf->stream(
                'Edit-Radiologi-'
                . ($patientR['regNum'] ?? $id)
                . '.pdf'
            );


        } catch (\Throwable $e) {

            dd([

                'id' =>
                    $id,

                'message' =>
                    $e->getMessage(),

                'file' =>
                    $e->getFile(),

                'line' =>
                    $e->getLine()

            ]);
        }
    }

}