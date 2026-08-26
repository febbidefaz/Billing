@php

    use Carbon\Carbon;

    // =====================================================
    // FORMATTER
    // =====================================================

    $fmt = fn($v) => $v ? Carbon::parse($v)->format('d-m-Y') : '-';

    $fmt_id = fn($v) => $v ? Carbon::parse($v)->locale('id')->translatedFormat('l, d F Y H:i:s') : '-';

    // =====================================================
    // HELPER TEXT
    // =====================================================

    if (!function_exists('rad_edit_td')) {
        function rad_edit_td($v)
        {
            $s = html_entity_decode((string) ($v ?? '-'), ENT_QUOTES | ENT_HTML5, 'UTF-8');

            return trim($s) === '' ? '-' : $s;
        }
    }

    // =====================================================
    // LOGO
    // =====================================================

    $logoPath = public_path('img/logo-rs1.png');

    $logoBase64 = is_file($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : '';

    // =====================================================
    // CLEAN RESULT
    // =====================================================

    if (!function_exists('rad_edit_clean_result')) {
        function rad_edit_clean_result(?string $s): string
        {
            $s = (string) $s;

            // Normalisasi newline
            $s = str_replace(["\r\n", "\r"], "\n", $s);

            // TAB → spasi
            $s = preg_replace("/\t+/", ' ', $s);

            // Bullet "o"
            $s = preg_replace('/(^|\n)(\s*)(o)\s+/u', '$1$2   $3 ', $s);

            // Bullet •
            $s = preg_replace('/\x{2022}\s*/u', '• ', $s);

            // Trim kanan
            $s = preg_replace("/[ \t]+$/m", '', $s);

            return $s;
        }
    }
@endphp


<!DOCTYPE html>

<html>

<head>

    <meta charset="utf-8">


    <style>
        * {
            font-family: DejaVu Sans, sans-serif;
        }

        body {
            font-size: 9px;
            color: #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            line-height: 1.35;
        }

        th,
        td {
            padding: 4px 6px;
            vertical-align: top;
        }

        .nb th,
        .nb td {
            border: 0;
        }

        .nb thead th {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .left {
            text-align: left;
        }

        .section-title {
            font-weight: 700;
            font-size: 10px;
            margin: 10px 0 6px;
            font-style: italic;
        }

        .muted {
            color: #333;
        }

        .u {
            text-decoration: underline;
        }

        .page-break {
            page-break-after: always;
        }

        img.logo {
            display: block;
            width: 68px;
            height: auto;
        }

        .fs-14 {
            font-size: 11px;
        }

        .fs-12 {
            font-size: 9px;
        }

        .thead-strong thead th {
            font-weight: 700;
            font-size: 9px;
        }

        .nb td.label {
            white-space: nowrap;
            color: #000;
        }

        .nb td.value strong {
            font-weight: 700;
        }


        /* HASIL RADIOLOGI */
        pre.result {
            white-space: pre-wrap;
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.35;
            margin: 0;
        }


        /* META USER / SHIFT */
        table.meta {
            width: auto !important;
            border-collapse: collapse;
            table-layout: auto;
            display: inline-table;
        }

        table.meta td {
            padding: 0 4px 2px 0;
            font-size: 9px;
        }

        table.meta td.label {
            white-space: nowrap;
            padding-right: 6px;
        }

        table.meta td.colon {
            width: 8px;
            padding: 0;
            text-align: center;
        }

        table.meta td.val {
            padding-left: 2px;
        }
    </style>

</head>


<body>


    @foreach ($rads as $i => $rad)
        @if ($i > 0)
            <div class="page-break"></div>
        @endif


        {{-- ================================================= --}}
        {{-- HEADER --}}
        {{-- ================================================= --}}

        <table style="margin-bottom:6px;">

            <tr>


                {{-- LOGO --}}
                <td
                    style="
                    width:90px;
                    padding:0;
                    text-align:center;
                    vertical-align:top;
                ">

                    @if ($logoBase64 !== '')
                        <img class="logo" src="{{ $logoBase64 }}" alt="Logo RS">
                    @else
                        <div style="font-size:10px;">
                            LOGO
                        </div>
                    @endif

                </td>


                {{-- JUDUL --}}
                <td
                    style="
                    padding:0;
                    text-align:center;
                    vertical-align:top;
                ">

                    <div
                        style="
                        font-weight:700;
                        font-size:13px;
                        letter-spacing:0.5px;
                    ">

                        {{ $hospitalR['name'] ?? "INSTALASI RADIOLOGI RUMAH SAKIT 'AISYIYAH BOJONEGORO" }}

                    </div>


                    <div
                        style="
                        margin-top:2px;
                        font-weight:700;
                        text-decoration:underline;
                        text-transform:uppercase;
                    ">

                        {{ $hospitalR['address'] ?? '' }}

                    </div>


                    <div
                        style="
                        margin-top:6px;
                        font-style:italic;
                        font-weight:700;
                        font-size:13px;
                    ">

                        HASIL PEMERIKSAAN RADIOLOGI

                    </div>

                </td>


                <td style="
                    width:90px;
                    padding:0;
                ">
                </td>

            </tr>

        </table>


        <div style="
            border-top:1px solid #000;
            margin:4px 0 6px;
        ">
        </div>


        {{-- ================================================= --}}
        {{-- IDENTITAS --}}
        {{-- ================================================= --}}

        <table class="nb fs-12" style="margin-top:8px;">

            <colgroup>

                <col style="width:140px;">
                <col style="width:8px;">
                <col>

                <col style="width:24px;">

                <col style="width:160px;">
                <col style="width:8px;">
                <col>

            </colgroup>


            <tbody>


                <tr>

                    <td class="label">
                        ID RAD
                    </td>

                    <td class="center">
                        :
                    </td>

                    <td class="value">
                        <strong>
                            {{ rad_edit_td($rad['idrad'] ?? '-') }}
                        </strong>
                    </td>


                    <td></td>


                    <td class="label">
                        NAMA PASIEN
                    </td>

                    <td class="center">
                        :
                    </td>

                    <td class="value">
                        <strong>
                            {{ rad_edit_td($patientR['nama'] ?? '-') }}
                        </strong>
                    </td>

                </tr>


                <tr>

                    <td class="label">
                        NO. REGISTRASI
                    </td>

                    <td class="center">
                        :
                    </td>

                    <td class="value">
                        <strong>
                            {{ rad_edit_td($patientR['idReg'] ?? '-') }}
                        </strong>
                    </td>


                    <td></td>


                    <td class="label">
                        ALAMAT
                    </td>

                    <td class="center">
                        :
                    </td>

                    <td class="value" style="word-break:break-word;">

                        <strong>
                            {{ rad_edit_td($patientR['addr'] ?? '-') }}
                        </strong>

                    </td>

                </tr>


                <tr>

                    <td class="label">
                        NO. REKAM MEDIK
                    </td>

                    <td class="center">
                        :
                    </td>

                    <td class="value">
                        <strong>
                            {{ rad_edit_td($patientR['regNum'] ?? '-') }}
                        </strong>
                    </td>


                    <td></td>


                    <td class="label">
                        KEL / TGL LAHIR
                    </td>

                    <td class="center">
                        :
                    </td>

                    <td class="value">

                        <strong>

                            {{ rad_edit_td($patientR['gender'] ?? '-') }}

                            {{ $patientR['dob'] ?? null ? ' / ' . $fmt($patientR['dob']) : '' }}

                        </strong>

                    </td>

                </tr>


                <tr>

                    <td class="label">
                        UMUR
                    </td>

                    <td class="center">
                        :
                    </td>

                    <td class="value">

                        <strong>

                            {{ rad_edit_td($rad['th'] ?? '-') }} Thn

                            {{ rad_edit_td($rad['bln'] ?? '-') }} bln

                            {{ rad_edit_td($rad['hr'] ?? '-') }} hr

                        </strong>

                    </td>


                    <td></td>


                    <td class="label">
                        DOKTER
                    </td>

                    <td class="center">
                        :
                    </td>

                    <td class="value">

                        <strong>
                            {{ rad_edit_td($rad['dokter'] ?? '-') }}
                        </strong>

                    </td>

                </tr>


                <tr>

                    <td class="label">
                        ALAT
                    </td>

                    <td class="center">
                        :
                    </td>

                    <td class="value">

                        <strong>
                            {{ rad_edit_td($rad['alatname'] ?? '-') }}
                        </strong>

                    </td>


                    <td></td>


                    <td class="label">
                        KELAS
                    </td>

                    <td class="center">
                        :
                    </td>

                    <td class="value">

                        <strong>
                            {{ rad_edit_td($rad['klas'] ?? '-') }}
                        </strong>

                    </td>

                </tr>


                <tr>

                    <td></td>
                    <td></td>
                    <td></td>

                    <td></td>


                    <td class="label">
                        TANGGAL PERIKSA
                    </td>

                    <td class="center">
                        :
                    </td>

                    <td class="value">

                        <strong>
                            {{ $fmt($rad['trad'] ?? null) }}
                        </strong>

                    </td>

                </tr>

            </tbody>

        </table>


        {{-- ================================================= --}}
        {{-- PEMERIKSAAN --}}
        {{-- ================================================= --}}

        <table class="nb fs-14">

            <thead>

                <tr>

                    <th style="width:5%;">
                        NO
                    </th>

                    <th style="width:25%;">
                        JENIS
                    </th>

                    <th>
                        HASIL PEMERIKSAAN
                    </th>

                </tr>

            </thead>


            <tbody>


                @forelse(($rad['periks'] ?? []) as $idx => $p)
                    <tr>

                        <td>
                            <strong>
                                {{ $idx + 1 }}
                            </strong>
                        </td>


                        <td>

                            <strong>
                                {{ rad_edit_td($p['periksa'] ?? '-') }}
                            </strong>

                        </td>


                        <td>

                            <pre class="result">{{ rad_edit_clean_result($p['result'] ?? '') }}</pre>

                        </td>

                    </tr>


                @empty


                    <tr>

                        <td colspan="3" class="center muted">

                            Tidak ada data

                        </td>

                    </tr>
                @endforelse


            </tbody>

        </table>


        {{-- ================================================= --}}
        {{-- FOOTER --}}
        {{-- ================================================= --}}

        @php

            $usr = trim((string) ($rad['usr'] ?? ''));

            $shift = trim((string) ($rad['shift'] ?? ''));

            Carbon::setLocale('id');

            $printed_id =
                $printedAt ?? null
                    ? Carbon::parse($printedAt, 'Asia/Jakarta')->locale('id')->translatedFormat('l, d F Y H:i:s')
                    : Carbon::now('Asia/Jakarta')->locale('id')->translatedFormat('l, d F Y H:i:s');
        @endphp


        <table style="
            width:100%;
            margin-top:12px;
        ">

            <tr>


                <td
                    style="
                    width:60%;
                    border:0;
                    vertical-align:top;
                ">

                    <div class="muted" style="font-style:italic;">

                        Dicetak:
                        {{ $printed_id }}

                    </div>


                    <div style="margin-top:6px;">

                        <table class="meta">

                            <tr>

                                <td class="label">
                                    User
                                </td>

                                <td class="colon">
                                    :
                                </td>

                                <td class="val">

                                    {{ $usr !== '' ? $usr : '-' }}

                                </td>

                            </tr>


                            <tr>

                                <td class="label">
                                    Shift
                                </td>

                                <td class="colon">
                                    :
                                </td>

                                <td class="val">

                                    {{ $shift !== '' ? $shift : '-' }}

                                </td>

                            </tr>

                        </table>

                    </div>

                </td>


                <td
                    style="
                    width:40%;
                    border:0;
                    text-align:center;
                ">

                    <div class="u">

                        <strong>
                            Dokter Radiologi
                        </strong>

                    </div>


                    <div style="height:6px;"></div>

                    @php
                        $img = $rad['ttd'] ?? null;
                    @endphp

                    @if (!empty($img))
                        <img src="{{ $img }}" alt="TTD Dokter Radiologi"
                            style="
                            width:80px;
                            height:auto;
                            display:block;
                            margin:0 auto;
                        ">
                    @else
                        <div style="height:90px;"></div>
                    @endif


                    <div style="margin-top:6px;">

                        <strong>
                            {{ rad_edit_td($rad['dr'] ?? '-') }}
                        </strong>

                    </div>

                </td>

            </tr>

        </table>
    @endforeach


</body>

</html>
