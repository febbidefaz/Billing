<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Kwitansi PHK3</title>

    <style>
        body {
            font-family: Arial, Tahoma, sans-serif;
            font-size: 15px;
            color: #000;
        }

        .print-btn {
            position: fixed;
            top: 10px;
            right: 10px;
            padding: 7px 12px;
            background: #17a2b8;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        @media print {

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .print-btn {
                display: none !important;
            }
        }

        .kwitansi {
            width: 92%;
            margin: 30px auto 0 auto;
        }

        .title {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            font-size: 18px;
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 8px 4px;
            vertical-align: top;
        }

        .label {
            width: 190px;
        }

        .colon {
            width: 15px;
        }

        .italic {
            font-style: italic;
            font-weight: bold;
        }

        .bold {
            font-weight: bold;
        }

        .nominal-box {
            background: #ffe95c;
            font-weight: bold;
            padding: 4px 18px;
            display: inline-block;
            min-width: 170px;
            text-align: right;
        }

        .penjamin {
            border: 1px solid #000;
            display: inline-table;
            margin-left: 25px;
            font-size: 14px;
        }

        .penjamin td {
            border: 1px solid #000;
            padding: 3px 15px;
            text-align: center;
        }

        .ttd {
            width: 400px;
            float: right;
            text-align: center;
            margin-top: 15px;
            margin-right: 60px;
        }

        .kasir-name {
            margin-top: 70px;
        }

        .nominal-box {
            background-color: #ffe95c !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            color-adjust: exact;
            font-weight: bold;
            padding: 4px 18px;
            display: inline-block;
            min-width: 170px;
            text-align: right;
        }
    </style>
</head>

<body>

    <button onclick="window.print()" class="print-btn">Print</button>

    <div class="kwitansi">

        <div class="title">KWITANSI</div>

        <table>
            <tr>
                <td class="label">Telah terima dari</td>
                <td class="colon">:</td>
                <td class="bold">
                    Rumah Sakit 'Aisyiyah Bojonegoro
                </td>
            </tr>

            <tr>
                <td class="label">Uang sejumlah</td>
                <td class="colon">:</td>
                <td class="italic">
                    {{ strtolower($terbilangSisaPhk3) }} rupiah
                </td>
            </tr>

            <tr>
                <td class="label">Untuk pembayaran</td>
                <td class="colon">:</td>
                <td>
                    Piutang Rawat Inap
                    &nbsp;&nbsp;
                    <span class="bold">{{ $pasien->Nama ?? '-' }}</span>
                </td>
            </tr>

            <tr>
                <td class="label">PxRS</td>
                <td class="colon">:</td>
                <td class="italic">
                    {{ $pasien->PxRS ?? '-' }}
                </td>
            </tr>

            <tr>
                <td class="label">No SEP</td>
                <td class="colon">:</td>
                <td class="italic">
                    {{ $pasien->NoSEP ?? '-' }}
                </td>
            </tr>

            <tr>
                <td class="label bold">TERBILANG</td>
                <td class="colon">:</td>
                <td>
                    <span class="nominal-box">
                        Rp {{ number_format($sisaPhk3 ?? 0, 0, ',', '.') }}
                    </span>
                </td>
            </tr>
        </table>

        <div class="ttd">
            Bojonegoro, {{ $tanggalCetak }}
            <br>
            Kasir

            <div class="kasir-name">
                {{ $pasien->Kasir ?? '-' }}
            </div>
        </div>

    </div>

</body>

</html>
