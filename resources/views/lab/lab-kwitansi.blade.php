@php
    use Carbon\Carbon;

    $logoPath = public_path('img/logo-rs1.png');
    $logoBase64 = '';

    if (is_file($logoPath)) {
        $ext = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));

        $mime = match ($ext) {
            'jpg', 'jpeg' => 'image/jpeg',
            'svg' => 'image/svg+xml',
            default => 'image/png',
        };

        $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
    }

    /*
    |--------------------------------------------------------------------------
    | Format tanggal Indonesia
    |--------------------------------------------------------------------------
    */

    $hari = [
        'Sunday' => 'Minggu',
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu',
    ];

    $bulan = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    $printDate = Carbon::parse($printedAt);

    $tanggalCetak =
        $hari[$printDate->format('l')] .
        ', ' .
        $printDate->format('d') .
        ' ' .
        $bulan[(int) $printDate->format('n')] .
        ' ' .
        $printDate->format('Y') .
        ' ' .
        $printDate->format('H.i.s');

    $tanggalPx = $tanggalPeriksa ? Carbon::parse($tanggalPeriksa)->format('d/m/Y') : '-';
@endphp

<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>Kwitansi Pemeriksaan Laboratorium</title>

    <style>
        /*
        |--------------------------------------------------------------------------
        | A5 Landscape
        |--------------------------------------------------------------------------
        */

        @page {
            size: A5 landscape;
            margin: 8mm 10mm 7mm 10mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;

            font-family: DejaVu Sans, Arial, sans-serif;

            font-size: 10px;

            color: #000;
        }


        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            border: none;
            vertical-align: middle;
        }

        .logo-cell {
            width: 80px;
            text-align: center;
        }

        .logo {
            width: 70px;
            height: auto;
        }

        .header-center {
            text-align: center;
        }

        .hospital-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .hospital-address {
            font-size: 9.5px;
            font-weight: bold;
            font-style: italic;
        }

        .document-title {
            margin-top: 12px;

            text-align: center;

            font-size: 16px;
            font-weight: bold;
        }

        .header-line {
            margin-top: 5px;

            border-top: 2px solid #999;
        }


        /*
        |--------------------------------------------------------------------------
        | INTRO
        |--------------------------------------------------------------------------
        */

        .intro {
            margin-top: 6px;
            margin-bottom: 6px;

            font-size: 11px;
            font-style: italic;
        }


        /*
        |--------------------------------------------------------------------------
        | DATA PASIEN
        |--------------------------------------------------------------------------
        */

        .patient-table {
            width: 72%;

            margin-left: 20px;

            border-collapse: collapse;

            font-size: 10px;
        }

        .patient-table td {
            border: none;

            padding: 2px 3px;

            vertical-align: top;
        }

        .patient-label {
            width: 130px;
        }

        .patient-colon {
            width: 10px;

            text-align: center;
        }

        .patient-value {
            font-weight: bold;
        }


        /*
        |--------------------------------------------------------------------------
        | BOTTOM
        |--------------------------------------------------------------------------
        */

        .bottom-table {
            width: 100%;

            margin-top: 10px;

            border-collapse: collapse;
        }

        .bottom-table td {
            border: none;

            vertical-align: top;
        }


        /*
        |--------------------------------------------------------------------------
        | TOTAL
        |--------------------------------------------------------------------------
        */

        .total-wrapper {
            width: 360px;

            margin-left: 45px;
        }

        .total-table {
            width: 100%;

            border-collapse: collapse;

            font-size: 12px;
        }

        .total-table td {
            padding: 3px 2px;
        }

        .total-label {
            width: 110px;

            font-weight: bold;
        }

        .total-colon {
            width: 10px;

            text-align: center;
        }

        .total-value {
            width: 130px;

            font-weight: bold;

            text-align: right;
        }

        .amount-line {
            border-bottom: 1px solid #000;
        }

        .grand-total {
            font-size: 13px;
        }


        /*
        |--------------------------------------------------------------------------
        | PETUGAS
        |--------------------------------------------------------------------------
        */

        .signature {
            padding-left: 30px;

            font-size: 9px;
        }

        .print-date {
            font-size: 7px;
            font-style: italic;

            margin-bottom: 6px;
        }

        .petugas-title {
            font-size: 10px;
        }

        .petugas-name {
            margin-top: 35px;

            font-size: 10px;
        }


        /*
        |--------------------------------------------------------------------------
        | FOOTER
        |--------------------------------------------------------------------------
        */

        .footer-line {
            margin-top: 4px;

            border-top: 1.5px solid #000;
        }
    </style>

</head>


<body>


    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <table class="header-table">

        <tr>

            <td class="logo-cell">

                @if ($logoBase64)
                    <img src="{{ $logoBase64 }}" class="logo" alt="Logo">
                @endif

            </td>


            <td class="header-center">

                <div class="hospital-name">

                    INSTALASI LABORATORIUM
                    RUMAH SAKIT AISYIYAH BOJONEGORO

                </div>

                <div class="hospital-address">

                    Jl. Panglima Sudirman 48 Bojonegoro
                    &nbsp;
                    Telp. 0353-881748
                    &nbsp;
                    Fax 0353-88597

                </div>


                <div class="document-title">

                    KWITANSI PEMERIKSAAN LABORAT

                </div>

            </td>


            {{-- agar judul tetap center --}}

            <td style="width:80px;"></td>

        </tr>

    </table>


    <div class="header-line"></div>


    {{-- ========================================================= --}}
    {{-- INTRO --}}
    {{-- ========================================================= --}}

    <div class="intro">

        Biaya pemeriksaan laborat untuk pasien atas nama :

    </div>


    {{-- ========================================================= --}}
    {{-- IDENTITAS --}}
    {{-- ========================================================= --}}

    <table class="patient-table">

        <tr>

            <td class="patient-label">
                Nama Pasien
            </td>

            <td class="patient-colon">
                :
            </td>

            <td class="patient-value">

                {{ $nama }}

                @if ($sapaan)
                    , {{ $sapaan }}
                @endif

            </td>

        </tr>


        <tr>

            <td class="patient-label">
                No. Rekam Medis
            </td>

            <td class="patient-colon">
                :
            </td>

            <td class="patient-value">

                {{ $regNum }}

            </td>

        </tr>


        <tr>

            <td class="patient-label">
                No. Registrasi
            </td>

            <td class="patient-colon">
                :
            </td>

            <td class="patient-value">

                {{ $idReg }}

            </td>

        </tr>


        <tr>

            <td class="patient-label">
                Alamat
            </td>

            <td class="patient-colon">
                :
            </td>

            <td class="patient-value">

                {{ $alamat }}

            </td>

        </tr>


        <tr>

            <td class="patient-label">
                No. Pemeriksaan
            </td>

            <td class="patient-colon">
                :
            </td>

            <td class="patient-value">

                {{ $idLab }}

            </td>

        </tr>


        <tr>

            <td class="patient-label">
                Tgl. Pemeriksaan
            </td>

            <td class="patient-colon">
                :
            </td>

            <td class="patient-value">

                {{ $tanggalPx }}

            </td>

        </tr>


        <tr>

            <td class="patient-label">
                Dokter / Perujuk
            </td>

            <td class="patient-colon">
                :
            </td>

            <td class="patient-value">

                {{ $dokter }}

            </td>

        </tr>

    </table>


    {{-- ========================================================= --}}
    {{-- TOTAL + PETUGAS --}}
    {{-- ========================================================= --}}

    <table class="bottom-table">

        <tr>

            <td style="width:65%;">

                <div class="total-wrapper">

                    <table class="total-table">

                        <tr>

                            <td class="total-label">
                                Biaya
                            </td>

                            <td class="total-colon">
                                :
                            </td>

                            <td class="total-value">

                                <div class="amount-line">

                                    {{ number_format($biaya, 2, ',', '.') }}

                                </div>

                            </td>

                        </tr>


                        <tr>

                            <td class="total-label">
                                Diskon
                            </td>

                            <td class="total-colon">
                                :
                            </td>

                            <td class="total-value">

                                <div class="amount-line">

                                    {{ number_format($diskon, 2, ',', '.') }}

                                </div>

                            </td>

                        </tr>


                        <tr>

                            <td class="total-label grand-total">

                                Total

                            </td>

                            <td class="total-colon grand-total">

                                :

                            </td>

                            <td class="total-value grand-total">

                                <div class="amount-line">

                                    {{ number_format($total, 2, ',', '.') }}

                                </div>

                            </td>

                        </tr>

                    </table>

                </div>

            </td>


            {{-- PETUGAS --}}

            <td style="width:35%;">

                <div class="signature">

                    <div class="print-date">

                        {{ $tanggalCetak }}

                    </div>


                    <div class="petugas-title">

                        Petugas

                    </div>


                    <div class="petugas-name">

                        {{ $petugas }}

                    </div>

                </div>

            </td>

        </tr>

    </table>


    <div class="footer-line"></div>


</body>

</html>
