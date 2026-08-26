@php
    use Carbon\Carbon;
    $fmt = fn($v) => $v ? Carbon::parse($v)->format('d-m-Y') : '-';
    $fmt_id = fn($v) => $v ? Carbon::parse($v)->locale('id')->translatedFormat('l, d F Y H:i:s') : '-';

    // Helper aman & unik untuk partial ini
    if (!function_exists('lb_td')) {
        function lb_td($v)
        {
            return $v ?? '-';
        }
    }

    // Formatter tanggal
    $lb_fmt = fn($v) => $v ? Carbon::parse($v)->format('d-m-Y') : '-';
    $lb_fmt_id = fn($v) => $v ? Carbon::parse($v)->locale('id')->translatedFormat('l, d F Y H:i:s') : '-';

    // Data pasien ringkas (jaga-jaga null)
    $lb_nama = $patient['nama'] ?? '-';
    $lb_regNum = $patient['regNum'] ?? '-';
    $lb_idReg = $patient['idReg'] ?? '-';
    $lb_addr = $patient['addr'] ?? '-';
    $lb_kel = $patient['gender'] ?? '-';
    $lb_dob = $patient['dob'] ?? null;

    // Waktu cetak fallback
    $lb_printed = $printedAt ?? null ?: $lb_fmt_id(now('Asia/Jakarta'));

    // Logo → base64 agar pasti tampil di Dompdf
    $lb_logoPathCandidates = [
        public_path('img/logo-rs1.png'),
        //  public_path('images/logo_rs.png'),
        //  public_path('logo.png'),
    ];
    $lb_logoBase64 = '';
    foreach ($lb_logoPathCandidates as $p) {
        if (is_file($p)) {
            $lb_logoBase64 =
                'data:image/' .
                (str_ends_with(strtolower($p), '.svg') ? 'svg+xml' : 'png') .
                ';base64,' .
                base64_encode(file_get_contents($p));
            break;
        }
    }
@endphp

<style>
    /* === SCOPED: prefix .lb- supaya tidak bentrok dengan view lain === */
    .lb * {
        font-family: DejaVu Sans, sans-serif;
    }

    .lb {
        font-size: 9px;
        color: #000;
    }

    .lb table {
        width: 100%;
        border-collapse: collapse;
        line-height: 1.35;
    }

    .lb th,
    .lb td {
        padding: 4px 6px;
        vertical-align: top;
    }

    .lb .nb th,
    .lb .nb td {
        border: 0;
    }

    .lb .nb thead th {
        border-top: 1px solid #000;
        border-bottom: 1px solid #000;
    }

    .lb .right {
        text-align: right;
    }

    .lb .center {
        text-align: center;
    }

    .lb .left {
        text-align: left;
    }

    .lb .section-title {
        font-weight: 700;
        font-size: 10px;
        margin: 10px 0 6px;
        font-style: italic;
    }

    .lb .muted {
        color: #333
    }

    .lb .u {
        text-decoration: underline;
    }

    .lb .page-break {
        page-break-after: always;
    }

    .lb img.logo {
        display: block;
        width: 68px;
        height: auto;
    }

    .lb .fs-14 {
        font-size: 11px;
    }

    .lb .fs-12 {
        font-size: 9px;
    }

    .lb .thead-strong thead th {
        font-weight: 700;
        font-size: 9px;
    }
</style>

<div class="lb">
    {{-- ==================== PER LEMBAR (per idlab) ==================== --}}
    @foreach ($labs ?? [] as $i => $lab)
        @php
            // Ambil QR per lembar dari controller: $qrList[$i] berisi SVG string (hasil helper generateQrCode)
            $qr = $qrList[$i] ?? ['pj' => null, 'usr' => null];

            $qr = $qrList[$i] ?? [
                'pj' => null,
                'usr' => null,
                'double' => null,
            ];

            // Controller sudah menghasilkan data URI
            $img_pj = $qr['pj'] ?? null;
            $img_usr = $qr['usr'] ?? null;
            $img_double = $qr['double'] ?? null;
        @endphp

        @if ($i > 0)
            <div class="page-break"></div>
        @endif

        {{-- ===== HEADER RS ===== --}}
        <table style="margin-bottom:6px;">
            <tr>
                {{-- LOGO KIRI --}}
                <td style="width:90px; vertical-align:top; text-align:center; border:0; padding:0;">
                    @if ($lb_logoBase64 !== '')
                        <img class="logo" src="{{ $lb_logoBase64 }}" alt="Logo RS">
                    @else
                        <div style="font-size:10px;">LOGO</div>
                    @endif
                </td>

                {{-- TEKS TENGAH --}}
                <td style="border:0; padding:0; text-align:center; vertical-align:top;">
                    <div style="font-weight:700; font-size:13px; letter-spacing:0.5px;">
                        {{ $hospital['name'] ?? "INSTALASI LABORATORIUM RUMAH SAKIT 'AISYIYAH BOJONEGORO" }}
                    </div>
                    <div style="margin-top:2px; font-weight:700; text-decoration:underline; text-transform:uppercase;">
                        {{ $hospital['address'] ?? 'JL. PANGLIMA SUDIRMAN 48 BOJONEGORO TELP. 0353-881748 FAX 0353-88597' }}
                    </div>
                    <div style="margin-top:6px; font-style:italic; font-weight:700; font-size:13px;">
                        HASIL PEMERIKSAAN LABORATORIUM
                    </div>
                </td>

                {{-- SPACER KANAN --}}
                <td style="width:90px; border:0; padding:0;"></td>
            </tr>
        </table>

        <div style="border-top:1px solid #000; margin:4px 0 6px;"></div>

        {{-- ===== IDENTITAS ===== --}}
        <table class="nb fs-12" style="margin-top:8px;">
            <colgroup>
                <col style="width:120px;">
                <col style="width:10px;">
                <col>
                <col style="width:40px;">
                <col style="width:140px;">
                <col style="width:10px;">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <td>NAMA PASIEN</td>
                    <td class="center">:</td>
                    <td><strong>{{ lb_td($lb_nama) }}</strong></td>
                    <td></td>
                    <td>ID LAB</td>
                    <td class="center">:</td>
                    <td><strong>{{ lb_td($lab['idlab'] ?? '-') }}</strong></td>
                </tr>

                <tr>
                    <td>ALAMAT</td>
                    <td class="center">:</td>
                    <td><strong>{{ lb_td($lb_addr) }}</strong></td>
                    <td></td>
                    <td>NO. REGISTRASI</td>
                    <td class="center">:</td>
                    <td><strong>{{ lb_td($lb_idReg) }}</strong></td>
                </tr>

                <tr>
                    <td>KEL / TGL LAHIR</td>
                    <td class="center">:</td>
                    <td><strong>{{ lb_td($lb_kel) }}{{ $lb_dob ? ' / ' . $lb_fmt($lb_dob) : '' }}</strong></td>
                    <td></td>
                    <td>NO. REKAM MEDIK</td>
                    <td class="center">:</td>
                    <td><strong>{{ lb_td($lb_regNum) }}</strong></td>
                </tr>

                <tr>
                    <td>UMUR</td>
                    <td class="center">:</td>
                    <td>
                        <strong>
                            {{ lb_td($lab['th'] ?? '-') }} Thn
                            {{ lb_td($lab['bln'] ?? '-') }} bln
                            {{ lb_td($lab['hr'] ?? '-') }} hr
                        </strong>
                    </td>
                    <td></td>
                    <td>PX RUJUKAN</td>
                    <td class="center">:</td>
                    <td><strong>{{ lb_td($lab['rujukan'] ?? '-') }}</strong></td>
                </tr>

                <tr>
                    <td>DOKTER</td>
                    <td class="center">:</td>
                    <td><strong>{{ lb_td($lab['dokter'] ?? '-') }}</strong></td>
                    <td></td>
                    <td>TANGGAL PERIKSA</td>
                    <td class="center">:</td>
                    <td><strong>{{ $lb_fmt($lab['tanggal'] ?? null) }}</strong></td>
                </tr>

                <tr>
                    <td>KELAS</td>
                    <td class="center">:</td>
                    <td><strong>{{ lb_td($lab['kelas'] ?? '-') }}</strong></td>
                    <td></td>
                    <td>RUANGAN</td>
                    <td class="center">:</td>
                    <td><strong>{{ lb_td($lab['ruangan'] ?? '-') }}</strong></td>
                </tr>

                <tr>
                    <td>JAM PERIKSA</td>
                    <td class="center">:</td>
                    <td>
                        <strong>
                            {{ !empty($lab['jamAmbil']) && $lab['jamAmbil'] !== '-' ? date('H:i:s', strtotime($lab['jamAmbil'])) : '-' }}
                        </strong>
                    </td>

                    <td></td>

                    <td>JAM SELESAI</td>
                    <td class="center">:</td>
                    <td>
                        <strong>
                            {{ !empty($lab['jamcheck']) && $lab['jamcheck'] !== '-' ? date('H:i:s', strtotime($lab['jamcheck'])) : '-' }}
                        </strong>
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- ===== HEADER KOLOM ===== --}}
        <table class="nb fs-12 thead-strong" style="margin-top:8px;">
            <thead>
                <tr>
                    <th style="width:5%;">NO</th>
                    <th style="width:20%;">JENIS PEMERIKSAAN</th>
                    <th class="center" style="width:25%;">HASIL</th>
                    <th class="left" style="width:50%;">NORMAL</th>
                </tr>
            </thead>
        </table>

        @php $no = 1; @endphp

        {{-- ===== ITEM PER KATEGORI ===== --}}
        @foreach ($lab['kats'] ?? [] as $kat)
            <div class="section-title">{{ lb_td($kat['kategori'] ?? 'LAIN-LAIN') }}</div>
            <table class="nb fs-14">
                <tbody>
                    @forelse(($kat['items'] ?? []) as $it)
                        <tr>
                            <td class="center" style="width:5%;">{{ $no++ }}</td>
                            <td style="width:20%;"><strong>{{ lb_td($it['nama'] ?? '-') }}</strong></td>
                            <td class="center" style="width:25%;"><strong>{{ lb_td($it['hasil'] ?? '') }}</strong></td>
                            <td class="left" style="width:50%;">{{ lb_td($it['normal'] ?? '') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="center muted">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @endforeach

        {{-- ===== FOOTER TANDA TANGAN + QR ===== --}}
        <table style="width:100%; margin-top:10px;">
            <tr>
                <td style="width:33%; text-align:center; border:0;">
                    <div class="u"><strong>Penanggung Jawab Pelayanan</strong></div>
                    <div style="height:6px;"></div>

                    @if (!empty($img_pj))
                        <img src="{{ $img_pj }}" alt="QR PJ"
                            style="width:80px;height:80px;display:block;margin:0 auto;">
                    @else
                        <div style="height:80px;"></div>
                    @endif

                    <div class="muted" style="margin-top:6px;">{{ lb_td($pj ?? '') }}</div>
                </td>

                <td style="width:33%; text-align:center; border:0;">
                    <div class="u"><strong>Double Check</strong></div>
                    <div style="height:96px;"></div>
                    <div class="muted" style="margin-top:6px;">&nbsp;</div>
                </td>

                <td style="width:33%; text-align:center; border:0;">
                    <div class="u"><strong>Petugas Laboratorium</strong></div>
                    <div style="height:6px;"></div>

                    @if (!empty($img_usr))
                        <img src="{{ $img_usr }}" alt="QR Petugas"
                            style="width:80px;height:80px;display:block;margin:0 auto;">
                    @else
                        <div style="height:80px;"></div>
                    @endif

                    <div class="muted" style="margin-top:6px;">{{ lb_td($lab['usr'] ?? '') }}</div>
                </td>
            </tr>
        </table>
    @endforeach
</div>
