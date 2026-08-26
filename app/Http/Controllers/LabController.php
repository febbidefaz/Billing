<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Throwable;

class LabController extends Controller
{
    public function print(int $idLab): Response
    {
        try {
            /*
             * Ambil seluruh hasil pemeriksaan laboratorium
             * berdasarkan IDLab.
             */
            $result = DB::select(
                'EXEC dbo.WebLabHasil_SP ?',
                [$idLab]
            );

            abort_if(
                empty($result),
                404,
                "Data laboratorium {$idLab} tidak ditemukan."
            );

            /*
             * Data pasien dan header sama pada setiap baris.
             */
            $header = $result[0];
            $namaPJ = $header->dr
                ?? 'dr. Istiqomah, M.Sc. Sp.PK';

            $namaPetugas = $header->user1
                ?? $header->Usr
                ?? '-';

            $namaDoubleCheck = $header->user2
                ?? '-';

                $qrPJ = $this->generateQrSvg(
                    "Penanggung Jawab Pelayanan Laboratorium\n" .
                    "Nama: {$namaPJ}\n" .
                    "ID Lab: {$idLab}"
                );
                
                $qrPetugas = $this->generateQrSvg(
                    "Petugas Laboratorium\n" .
                    "Nama: {$namaPetugas}\n" .
                    "ID Lab: {$idLab}"
                );
                
                $qrDouble = $this->generateQrSvg(
                    "Double Check Laboratorium\n" .
                    "Nama: {$namaDoubleCheck}\n" .
                    "ID Lab: {$idLab}"
                );
             
            /*
             * Kelompokkan hasil berdasarkan kategori laboratorium.
             */
            $kategoriLab = collect($result)
                ->groupBy(function ($item) {
                    return $item->Kategori ?: 'LAIN-LAIN';
                })
                ->map(function ($items, $kategori) {
                    return [
                        'kategori' => $kategori ?: 'LAIN-LAIN',

                        'items' => collect($items)
                            ->sortBy(function ($item) {
                                return [
                                    (int) ($item->KateID ?? 0),
                                    (int) ($item->prepid ?? 0),
                                    (int) ($item->ID ?? 0),
                                ];
                            })
                            ->map(function ($item) {
                                return [
                                    'id' => $item->Idd ?? null,

                                    'nama' => $item->Perik ?? '-',

                                    'hasil' => $item->Levels ?? '-',

                                    'lvl' => $item->lvl ?? null,

                                    'normal' => $item->NorL ?? '-',

                                    'metode' => $item->Metode ?? '-',

                                    'note' => $item->Note ?? null,

                                    'flag' => $this->getResultFlag(
                                        $item->lvl ?? null,
                                        $item->batasDown ?? null,
                                        $item->batasUP ?? null
                                    ),

                                    'batasDown' => $item->batasDown ?? null,

                                    'batasUP' => $item->batasUP ?? null,

                                    'isOk' => $item->IsOk ?? null,

                                    'pdf' => $item->pdf ?? null,
                                ];
                            })
                            ->values()
                            ->all(),
                    ];
                })
                ->values()
                ->all();

            $data = [
                'patient' => [
                    'nama' => $header->Nama ?? '-',

                    'regNum' => $header->RegNum ?? '-',

                    'idReg' => $header->IDReg ?? '-',

                    'addr' => $this->formatAlamat(
                        $header->Addr ?? null,
                        $header->Kelurahan ?? null
                    ),

                    'gender' => $this->formatGender(
                        $header->kel ?? null
                    ),

                    'dob' => $header->TGLLahir ?? null,
                ],

                /*
                 * View lab.lab menggunakan foreach ($labs).
                 */
                'labs' => [[
                    'idlab' => $header->IDLab ?? $idLab,

                    'tanggal' => $header->TLab ?? null,

                    'dokter' => $header->Dokter ?? '-',

                    'rujukan' => $header->Rujukan ?? '-',
                  
                    'kelas' => $header-> Kelas ?? '-',

                    'ruangan' => $header->RoomName ?? '-',

                    'jamAmbil' => $this->formatJam(
                        $header->JamAmbil ?? null
                    ),

                    'jamcheck' => $this->formatJam(
                        $header->JamCheck ?? null
                    ),

                    'th' => $header->Th ?? '-',

                    'bln' => $header->Bln ?? '-',

                    'hr' => $header->Hr ?? '-',

                    /*
                     * Nama petugas.
                     */
                    'usr' => $header->user1
                        ?? $header->Usr
                        ?? '-',

                    'user1' => $header->user1
                        ?? $header->Usr
                        ?? '-',

                    'user2' => $header->user2
                        ?? '-',

                    /*
                     * Status verifikasi.
                     */
                    'ver' => (int) (
                        $header->ver
                        ?? $header->Verif
                        ?? 0
                    ),

                    /*
                     * Catatan dokter Sp.PK.
                     */
                    'noteLap' => $header->NoteLap ?? null,

                    /*
                     * TTD analis bila diperlukan di view.
                     */
                    'ttd' => $header->ttd ?? null,

                    /*
                     * Hasil pemeriksaan per kategori.
                     */
                    'kats' => $kategoriLab,
                ]],

                'hospital' => [
                    'name' =>
                        'INSTALASI LABORATORIUM RUMAH SAKIT AISYIYAH BOJONEGORO',

                    'address' =>
                        'Jl. Panglima Sudirman 48 Bojonegoro Telp. 0353-881748. Fax 0353-88597',
                ],

                /*
                 * QR dapat ditambahkan kemudian.
                 */
                'qrList' => [[
                    'pj' => $qrPJ,
                    'usr' => $qrPetugas,
                    'double' => $qrDouble,
                ]],

                'pj' => $header->dr
                    ?? 'dr. Istiqomah, M.Sc. Sp.PK',

                'printedAt' => now('Asia/Jakarta'),
            ];

            /*
             * Lokasi view:
             * resources/views/lab/lab.blade.php
             */
            $pdf = Pdf::loadView('lab.lab', $data)
                ->setPaper('a4', 'portrait')
                ->setOption('isRemoteEnabled', true)
                ->setOption('isHtml5ParserEnabled', true)
                ->setOption('chroot', base_path());

            return $pdf->stream(
                "hasil-laboratorium-{$idLab}.pdf"
            );
        } catch (Throwable $e) {
            Log::error('Gagal mencetak hasil laboratorium', [
                'id_lab' => $idLab,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            if (config('app.debug')) {
                dd([
                    'idLab' => $idLab,
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
            }

            abort(
                500,
                'Hasil pemeriksaan laboratorium gagal dicetak.'
            );
        }
    }

    /**
     * Menentukan hasil tinggi atau rendah.
     */
    private function getResultFlag(
        mixed $level,
        mixed $batasBawah,
        mixed $batasAtas
        ): string {
        if (!is_numeric($level)) {
            return '';
        }

        $hasil = (float) $level;

        $bawah = is_numeric($batasBawah)
            ? (float) $batasBawah
            : null;

        $atas = is_numeric($batasAtas)
            ? (float) $batasAtas
            : null;

        /*
         * Nilai batas 0 dari SP dianggap tidak memiliki batas.
         */
        if ($atas !== null && $atas != 0 && $hasil > $atas) {
            return 'H';
        }

        if ($bawah !== null && $bawah != 0 && $hasil < $bawah) {
            return 'L';
        }

        return '';
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

    private function formatJam(mixed $jam): string
    {
        if (!$jam) {
            return '-';
        }

        $timestamp = strtotime((string) $jam);

        if ($timestamp === false) {
            return (string) $jam;
        }

        return date('H.i', $timestamp);
    }

    private function generateQrSvg(string $text): ?string
    {
        try {
            $svg = (string) QrCode::format('svg')
                ->size(150)
                ->margin(1)
                ->generate($text);
    
            // Hilangkan deklarasi XML
            $svg = preg_replace('/<\?xml.*?\?>\s*/', '', $svg);
    
            return 'data:image/svg+xml;base64,' . base64_encode($svg);
        } catch (Throwable $e) {
            Log::error('QR laboratorium gagal dibuat', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
    
            return null;
        }
    }
    
    public function printEditLab($id)
    {
        try {

            // =====================================================
            // AMBIL HASIL LAB EDIT
            // =====================================================

            $rows = DB::select(
                "EXEC dbo.LaboratResultKwitEditIDReg_SP ?",
                [$id]
            );

            if (empty($rows)) {

                return response(
                    '<h3 style="font-family:Arial;text-align:center;margin-top:50px;">
                        Data Edit Laboratorium belum tersedia.
                    </h3>',
                    404
                );
            }


            // =====================================================
            // DATA PASIEN
            // =====================================================

            $first = $rows[0];

            $patient = [
                'nama'   => $first->Nama ?? '-',
                'regNum' => $first->RegNum ?? '-',
                'idReg'  => $first->IDReg ?? $id,
                'addr'   => $first->Addr ?? '-',

                'gender' => $first->Jenis_Kelamin ?? '-',

                'dob'    => $first->Tanggal_Lahir ?? null,
            ];


            // =====================================================
            // GROUP PER ID LAB
            // =====================================================

            $groupLab = collect($rows)
                ->groupBy('IDLab');


            $labs = [];


            foreach ($groupLab as $idLab => $items) {

                $firstLab = $items->first();


                // =================================================
                // GROUP KATEGORI
                // =================================================

                $kategori = $items
                    ->groupBy('Kategori')
                    ->map(function ($rowsKategori, $namaKategori) {

                        return [

                            'kategori' => $namaKategori,

                            'items' => $rowsKategori
                                ->map(function ($row) {

                                    return [

                                        'nama' =>
                                            $row->Perik ?? '-',

                                        'hasil' =>
                                            $row->Levels ?? '',

                                        'normal' =>
                                            $row->NorL ?? '',

                                        'biaya' =>
                                            $row->Biaya ?? 0,

                                    ];

                                })
                                ->values()
                                ->toArray()
                        ];

                    })
                    ->values()
                    ->toArray();


                // =================================================
                // DATA PER LEMBAR LAB
                // =================================================

                $labs[] = [

                    'idlab' =>
                        $idLab,

                    'tanggal' =>
                        $firstLab->TLab ?? null,

                    'dokter' =>
                        $firstLab->Dokter ?? '-',

                    'rujukan' =>
                        $firstLab->Rujukan ?? '-',

                    'kelas' =>
                        $firstLab->Kelas ?? '-',

                    'ruangan' =>
                        $firstLab->RoomName ?? '-',

                    'jamAmbil' =>
                        $firstLab->Jam_ambil ?? '-',

                    'jamcheck' =>
                        $firstLab->Jam_check ?? '-',

                    'usr' =>
                        $firstLab->Usr ?? '-',

                    'th' =>
                        $firstLab->Th ?? 0,

                    'bln' =>
                        $firstLab->Bln ?? 0,

                    'hr' =>
                        $firstLab->Hr ?? 0,

                    'kats' =>
                        $kategori,
                ];
            }


            // =====================================================
            // DATA RS
            // =====================================================

            $hospital = [

                'name' =>
                    "INSTALASI LABORATORIUM RUMAH SAKIT 'AISYIYAH BOJONEGORO",

                'address' =>
                    'JL. PANGLIMA SUDIRMAN 48 BOJONEGORO TELP. 0353-881748 FAX 0353-88597'
            ];


            // =====================================================
            // QR
            // sementara kosong dulu
            // =====================================================

            $pj = 'dr. Istiqomah, M.Sc., Sp.PK';

            $qrList = [];
            
            foreach ($labs as $lab) {
            
                $qrList[] = [
                    'pj'  => $this->generateQrSvg($pj),
                    'usr' => $this->generateQrSvg($lab['usr'] ?? '-'),
                ];
            }

            $printedAt =
                now('Asia/Jakarta');


            // =====================================================
            // GENERATE PDF
            // =====================================================

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
                'lab.print-edit-lab',
                compact(
                    'patient',
                    'labs',
                    'hospital',
                    'qrList',
                    'pj',
                    'printedAt'
                )
            );


            $pdf->setPaper(
                'A4',
                'portrait'
            );


            return $pdf->stream(
                'Edit-Laboratorium-' .
                ($patient['regNum'] ?? $id) .
                '.pdf'
            );


        } catch (\Throwable $e) {

            dd([
                'id'      => $id,
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine()
            ]);
        }
    }
}