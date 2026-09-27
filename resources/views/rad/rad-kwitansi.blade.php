@php

    use Carbon\Carbon;

    /*
    |--------------------------------------------------------------------------
    | Logo
    |--------------------------------------------------------------------------
    */

    $logoPath = public_path('img/logo-rs1.png');

    $logoBase64 = '';

    if (is_file($logoPath)) {
        $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
    }

    /*
    |--------------------------------------------------------------------------
    | Format tanggal
    |--------------------------------------------------------------------------
    */

    $fmtDate = function ($value) {
        if (!$value) {
            return '-';
        }

        try {
            return Carbon::parse($value)->format('d/m/Y');
        } catch (\Throwable $e) {
            return (string) $value;
        }
    };

@endphp


<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>
        Kwitansi Pemeriksaan Radiologi
    </title>


    <style>
        /*
    |--------------------------------------------------------------------------
    | A5 LANDSCAPE
    |--------------------------------------------------------------------------
    */

        @page {

            size: A5 landscape;

            margin:
                7mm 9mm 7mm 9mm;

        }


        * {

            box-sizing: border-box;

        }


        body {

            margin: 0;

            font-family:
                DejaVu Sans,
                sans-serif;

            font-size: 9px;

            color: #000;

        }


        /*
    |--------------------------------------------------------------------------
    | HEADER
    |--------------------------------------------------------------------------
    */

        .header-table {

            width: 100%;

            border-collapse:
                collapse;

        }


        .header-table td {

            border: 0;

            vertical-align:
                middle;

        }


        .logo-cell {

            width: 90px;

            text-align:
                center;

            border-right:
                1px solid #f0d000 !important;

        }


        .logo {

            width: 78px;

            height: auto;

        }


        .hospital-name {

            text-align:
                center;

            font-size: 13px;

            font-weight:
                bold;

        }


        .hospital-address {

            margin-top: 5px;

            text-align:
                center;

            font-size: 9px;

            font-weight:
                bold;

            font-style:
                italic;

        }


        .title {

            margin-top: 12px;

            text-align:
                center;

            font-size: 16px;

            font-weight:
                bold;

        }


        .header-line {

            margin-top: 6px;

            border-top:
                3px solid #999;

        }


        /*
    |--------------------------------------------------------------------------
    | IDENTITAS
    |--------------------------------------------------------------------------
    */

        .identity-table {

            width: 100%;

            margin-top: 4px;

            border-collapse:
                collapse;

            table-layout:
                fixed;

        }


        .identity-table td {

            padding:
                1px 3px;

            border: 0;

            vertical-align:
                top;

            line-height:
                1.25;

        }


        .label {

            width: 19%;

            white-space:
                nowrap;

        }


        .colon {

            width: 2%;

            text-align:
                center;

            font-weight:
                bold;

        }


        .value {

            width: 26%;

            font-weight:
                bold;

        }


        .gap {

            width: 5%;

        }


        /*
    |--------------------------------------------------------------------------
    | DAFTAR PEMERIKSAAN
    |--------------------------------------------------------------------------
    */

        .item-table {

            width: 100%;

            margin-top: 5px;

            border-collapse:
                collapse;

        }


        .item-table th {

            padding:
                3px 3px;

            border-top:
                3px solid #999;

            border-bottom:
                2px solid #222;

            text-align:
                left;

            font-weight:
                bold;

        }


        .item-table td {

            padding:
                3px 3px;

            border-bottom:
                1px solid #222;

        }


        .number {

            width: 6%;

        }


        .jenis {

            width: 52%;

        }


        .money {

            width: 21%;

        }

        .item-table th.money,
        .item-table td.money {
            text-align: right !important;
        }


        /*
    |--------------------------------------------------------------------------
    | FOOTER
    |--------------------------------------------------------------------------
    */

        .footer-table {

            width: 100%;

            margin-top: 5px;

            border-collapse:
                collapse;

        }


        .footer-table td {

            border: 0;

            vertical-align:
                top;

        }


        /*
    |--------------------------------------------------------------------------
    | USER
    |--------------------------------------------------------------------------
    */

        .operator {

            width: 60%;

            font-size: 9px;

        }


        .operator-table {

            border-collapse:
                collapse;

        }


        .operator-table td {

            padding:
                2px 4px 2px 0;

        }


        .operator-label {

            width: 60px;

        }


        .operator-value {

            min-width: 100px;

            font-weight:
                bold;

            text-decoration:
                underline;

        }


        /*
    |--------------------------------------------------------------------------
    | TOTAL
    |--------------------------------------------------------------------------
    */

        .total-wrapper {

            width: 40%;

        }


        .total-table {

            width: 100%;

            border-collapse:
                collapse;

            font-size: 9px;

        }


        .total-table td {

            padding:
                2px 3px;

        }


        .total-label {

            font-weight:
                bold;

        }


        .total-value {

            width: 110px;

            text-align:
                right;

            font-weight:
                bold;

            text-decoration:
                underline;

        }


        .grand-netto {

            font-size: 10px;

            font-weight:
                bold;

        }
    </style>

</head>


<body>


    {{-- ============================================================= --}}
    {{-- HEADER --}}
    {{-- ============================================================= --}}

    <table class="header-table">

        <tr>

            <td class="logo-cell">

                @if ($logoBase64)
                    <img src="{{ $logoBase64 }}" class="logo" alt="Logo">
                @endif

            </td>


            <td>

                <div class="hospital-name">

                    INSTALASI RADIOLOGI
                    RUMAH SAKIT AISYIYAH BOJONEGORO

                </div>


                <div class="hospital-address">

                    Jl. Panglima Sudirman 48 Bojonegoro
                    &nbsp;
                    Telp. 0353-881748.
                    Fax 0353-88597

                </div>


                <div class="title">

                    KWITANSI PEMERIKSAAN RADIOLOGI

                </div>

            </td>


            <td style="width:90px;"></td>

        </tr>

    </table>


    <div class="header-line"></div>



    {{-- ============================================================= --}}
    {{-- IDENTITAS --}}
    {{-- ============================================================= --}}

    <table class="identity-table">


        <tr>

            <td class="label">
                ID. RAD
            </td>

            <td class="colon">
                :
            </td>

            <td class="value">

                {{ $idRad }}

            </td>


            <td class="gap"></td>


            <td class="label">
                NAMA PASIEN
            </td>

            <td class="colon">
                :
            </td>

            <td class="value">

                {{ $nama }}

                @if ($sapaan)
                    , {{ $sapaan }}
                @endif

            </td>

        </tr>



        <tr>

            <td class="label">
                NO. REGISTRASI
            </td>

            <td class="colon">
                :
            </td>

            <td class="value">

                {{ $idReg }}

            </td>


            <td></td>


            <td class="label">
                ALAMAT
            </td>

            <td class="colon">
                :
            </td>

            <td class="value">

                {{ $alamat }}

            </td>

        </tr>



        <tr>

            <td class="label">
                NO. REKAM MEDIK
            </td>

            <td class="colon">
                :
            </td>

            <td class="value">

                {{ $regNum }}

            </td>


            <td></td>


            <td class="label">
                JENIS KELAMIN
            </td>

            <td class="colon">
                :
            </td>

            <td class="value">

                {{ $gender }}

            </td>

        </tr>



        <tr>

            <td class="label">
                UMUR
            </td>

            <td class="colon">
                :
            </td>

            <td class="value">

                {{ $umurTahun }} TH

            </td>


            <td></td>


            <td class="label">
                DOKTER
            </td>

            <td class="colon">
                :
            </td>

            <td class="value">

                {{ $dokter }}

            </td>

        </tr>



        <tr>

            <td class="label">
                ALAT
            </td>

            <td class="colon">
                :
            </td>

            <td class="value">

                {{ $alat }}

            </td>


            <td></td>


            <td class="label">
                KELAS
            </td>

            <td class="colon">
                :
            </td>

            <td class="value">

                {{ $kelas }}

            </td>

        </tr>


    </table>



    {{-- ============================================================= --}}
    {{-- PEMERIKSAAN --}}
    {{-- ============================================================= --}}

    <table class="item-table">

        <thead>

            <tr>

                <th class="number">
                    NO.
                </th>

                <th class="jenis">
                    JENIS PERIKSA
                </th>

                <th class="money">
                    BIAYA
                </th>

                <th class="money">
                    DISCOUNT
                </th>

            </tr>

        </thead>


        <tbody>

            @foreach ($pemeriksaan as $item)
                <tr>

                    <td>

                        {{ $item['no'] }}

                    </td>


                    <td>

                        {{ $item['periksa'] }}

                    </td>


                    <td class="money">

                        {{ number_format($item['biaya'], 2, ',', '.') }}

                    </td>


                    <td class="money">

                        {{ number_format($item['discount'], 2, ',', '.') }}

                    </td>

                </tr>
            @endforeach

        </tbody>

    </table>



    {{-- ============================================================= --}}
    {{-- FOOTER --}}
    {{-- ============================================================= --}}

    <table class="footer-table">

        <tr>


            {{-- USER / SHIFT --}}

            <td class="operator">

                <table class="operator-table">

                    <tr>

                        <td class="operator-label">

                            User

                        </td>

                        <td>

                            :

                        </td>

                        <td class="operator-value">

                            {{ $user }}

                        </td>

                    </tr>


                    <tr>

                        <td class="operator-label">

                            Shift

                        </td>

                        <td>

                            :

                        </td>

                        <td class="operator-value">

                            {{ $shift }}

                        </td>

                    </tr>

                </table>

            </td>



            {{-- TOTAL --}}

            <td class="total-wrapper">

                <table class="total-table">

                    <tr>

                        <td class="total-label">

                            GRAND TOTAL

                        </td>

                        <td class="total-value">

                            {{ number_format($grandTotal, 2, ',', '.') }}

                        </td>

                    </tr>


                    <tr>

                        <td class="total-label">

                            TOTAL DISCOUNT

                        </td>

                        <td class="total-value">

                            {{ number_format($totalDiscount, 2, ',', '.') }}

                        </td>

                    </tr>


                    <tr class="grand-netto">

                        <td>

                            GRAND NETTO

                        </td>

                        <td class="total-value">

                            {{ number_format($grandNetto, 2, ',', '.') }}

                        </td>

                    </tr>

                </table>

            </td>


        </tr>

    </table>


</body>

</html>
