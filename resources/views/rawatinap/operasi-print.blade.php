<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Rincian Biaya Operasi</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 8mm;
        }

        body {
            font-family: Tahoma, Arial, sans-serif;
            font-size: 14px;
            color: #000;
            margin: 0;
        }

        .operasi {
            width: 96%;
            margin: 16px auto 0 auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th {
            padding: 3px 6px;
            vertical-align: top;
            line-height: 18px;
        }

        .title {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            padding-bottom: 6px;
            border-bottom: 2px solid #000;
        }

        .section {
            padding: 6px 0;
            border-bottom: 1px solid #000;
        }

        .header-info td {
            border: none;
            padding: 2px 6px;
            line-height: 18px;
        }

        .bold {
            font-weight: bold;
        }

        .italic {
            font-style: italic;
        }

        .rincian {
            table-layout: fixed;
            margin-top: 6px;
        }

        .rincian th {
            border-bottom: 1px solid #000;
            padding: 4px 6px;
            font-weight: bold;
            line-height: 18px;
        }

        .rincian td {
            padding: 3px 6px;
            border-bottom: 1px dotted #aaa;
            line-height: 18px;
        }

        .col-uraian {
            width: 58%;
            text-align: left;
        }

        .col-biaya {
            width: 21%;
            text-align: right;
        }

        .col-potongan {
            width: 21%;
            text-align: right;
        }

        .nominal {
            text-align: right;
            font-weight: bold;
            white-space: nowrap;
        }

        .summary {
            margin-top: 6px;
            border-top: 2px solid #000;
            padding-top: 4px;
        }

        .summary td {
            padding: 3px 6px;
            line-height: 18px;
        }

        .terbilang {
            font-style: italic;
            font-weight: bold;
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
            z-index: 9999;
        }

        .terbilang {
            font-style: italic;
            font-weight: bold;
            text-align: left;
            padding-left: 6px;
        }

        @media print {
            .print-btn {
                display: none !important;
            }

            .operasi {
                margin-top: 0;
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <button onclick="window.print()" class="print-btn">Print</button>

    @php
        $R = fn($v) => number_format($v ?? 0, 0, ',', '.');

        $tglOp = !empty($operasi->TgOp) ? date('d/m/Y', strtotime($operasi->TgOp)) : '-';

        $totalPotongan =
            ($operasi->PotOp ?? 0) +
            ($operasi->PotAss ?? 0) +
            ($operasi->PotAnes ?? 0) +
            ($operasi->PotAssAnes ?? 0) +
            ($operasi->PotAlat ?? 0) +
            ($operasi->PotBahan ?? 0) +
            ($operasi->PotOk ?? 0) +
            ($operasi->PotJasa ?? 0);

        $terbilang = strtoupper($terbilang ?? '');
    @endphp

    <div class="operasi">

        <div class="title">
            RINCIAN BIAYA OPERASI
        </div>

        <div class="section">
            <table class="header-info">
                <tr>
                    <td width="14%" class="bold">ID</td>
                    <td width="36%">: {{ $pasien->ID ?? '-' }}</td>

                    <td width="14%" class="bold">NO. RM</td>
                    <td width="36%">: {{ $pasien->RegNum ?? '-' }}</td>
                </tr>

                <tr>
                    <td class="bold">NAMA PX</td>
                    <td>: {{ $pasien->Nama ?? '-' }}</td>

                    <td class="bold">TGL OPERASI</td>
                    <td>: {{ $tglOp }}</td>
                </tr>

                <tr>
                    <td class="bold">STATUS</td>
                    <td>: {{ $pasien->PxRS ?? '-' }}</td>

                    <td class="bold"></td>
                    <td></td>
                </tr>
            </table>
        </div>

        <table class="rincian">
            <thead>
                <tr>
                    <th class="col-uraian">URAIAN</th>
                    <th class="col-biaya">BIAYA</th>
                    <th class="col-potongan">POTONGAN</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td>Operator : {{ $operasi->Op ?? '-' }}</td>
                    <td class="nominal">{{ $R($operasi->BiayaOp ?? 0) }}</td>
                    <td class="nominal">{{ $R($operasi->PotOp ?? 0) }}</td>
                </tr>

                <tr>
                    <td>Asisten : {{ $operasi->Ass ?? '-' }}</td>
                    <td class="nominal">{{ $R($operasi->BiayaAss ?? 0) }}</td>
                    <td class="nominal">{{ $R($operasi->PotAss ?? 0) }}</td>
                </tr>

                <tr>
                    <td>Anestesi : {{ $operasi->Anes ?? '-' }}</td>
                    <td class="nominal">{{ $R($operasi->BiayaAnes ?? 0) }}</td>
                    <td class="nominal">{{ $R($operasi->PotAnes ?? 0) }}</td>
                </tr>

                <tr>
                    <td>Ass. Anestesi : {{ $operasi->AssAnes ?? '-' }}</td>
                    <td class="nominal">{{ $R($operasi->BiayaAssAnes ?? 0) }}</td>
                    <td class="nominal">{{ $R($operasi->PotAssAnes ?? 0) }}</td>
                </tr>

                <tr>
                    <td>Sewa Alat</td>
                    <td class="nominal">{{ $R($operasi->SewaAlat ?? 0) }}</td>
                    <td class="nominal">{{ $R($operasi->PotAlat ?? 0) }}</td>
                </tr>

                <tr>
                    <td>Bahan</td>
                    <td class="nominal">{{ $R($operasi->Bahan ?? 0) }}</td>
                    <td class="nominal">{{ $R($operasi->PotBahan ?? 0) }}</td>
                </tr>

                <tr>
                    <td>Sewa Kamar Operasi</td>
                    <td class="nominal">{{ $R($operasi->SewaOK ?? 0) }}</td>
                    <td class="nominal">{{ $R($operasi->PotOk ?? 0) }}</td>
                </tr>

                <tr>
                    <td>Jasa Rumah Sakit</td>
                    <td class="nominal">{{ $R($operasi->Jasa ?? 0) }}</td>
                    <td class="nominal">{{ $R($operasi->PotJasa ?? 0) }}</td>
                </tr>

                <tr>
                    <td>CSSD</td>
                    <td class="nominal">{{ $R($operasi->Cssd ?? 0) }}</td>
                    <td class="nominal">0</td>
                </tr>
            </tbody>
        </table>

        <div class="summary">
            <table>
                <tr>
                    <td width="58%" class="bold">TOTAL BIAYA</td>
                    <td width="21%" class="nominal">{{ $R($operasi->Netto ?? 0) }}</td>
                    <td width="21%" class="nominal">{{ $R($totalPotongan) }}</td>
                </tr>
                <tr>
                    <td colspan="3">
                        <span class="bold">TERBILANG</span>
                        <span style="margin-left:10px;">:</span>
                        <span class="terbilang">
                            # {{ $terbilang }} RUPIAH #
                        </span>
                    </td>
                </tr>
            </table>
        </div>

    </div>

</body>

</html>
