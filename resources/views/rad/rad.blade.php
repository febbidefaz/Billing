@php
    use Carbon\Carbon;

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

    $fmtDateIndo = function ($value) {
        if (!$value) {
            return '-';
        }

        try {
            $date = Carbon::parse($value);

            $hari = [
                0 => 'Minggu',
                1 => 'Senin',
                2 => 'Selasa',
                3 => 'Rabu',
                4 => 'Kamis',
                5 => 'Jumat',
                6 => 'Sabtu',
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

            return $hari[$date->dayOfWeek] .
                ', ' .
                $date->format('d') .
                ' ' .
                $bulan[(int) $date->format('n')] .
                ' ' .
                $date->format('Y H.i.s');
        } catch (\Throwable $e) {
            return (string) $value;
        }
    };

    $logoPath = public_path('img/logo-rs1.png');
    $logoBase64 = '';

    if (is_file($logoPath)) {
        $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
    }
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Hasil Pemeriksaan Radiologi</title>

    <style>
        @page {
            margin: 14px 20px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #111;
        }

        .header-table,
        .identity-table,
        .result-table,
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            border: 0;
            vertical-align: middle;
        }

        .logo-cell {
            width: 100px;
            text-align: center;
        }

        .logo {
            width: 85px;
        }

        .hospital-name {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
        }

        .hospital-address {
            margin-top: 3px;
            text-align: center;
            font-size: 10px;
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
            margin-top: 7px;
            border-top: 2px solid #777;
        }

        .identity-table {
            width: 100%;
            margin-top: 5px;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 9.5px;
        }

        .identity-table td {
            padding: 1px 2px;
            border: 0;
            vertical-align: top;
            line-height: 1.35;
        }

        .label-left {
            width: 17%;
            white-space: nowrap;
        }

        .value-left {
            width: 18%;
            font-weight: bold;
        }

        .identity-gap {
            width: 3%;
        }

        .label-right {
            width: 17%;
            white-space: nowrap;
        }

        .value-right {
            width: 36%;
            font-weight: bold;
        }

        .colon {
            width: 1.5%;
            text-align: center;
            font-weight: bold;
        }

        .value {
            font-weight: bold;
        }

        .result-table {
            margin-top: 8px;
        }

        .result-table th {
            padding: 4px;
            border-top: 2px solid #555;
            border-bottom: 2px solid #222;
            text-align: left;
        }

        .result-table td {
            padding: 5px 4px;
            vertical-align: top;
        }

        .result-text {
            white-space: pre-line;
            line-height: 1.5;
        }

        .footer-table {
            margin-top: 18px;
            page-break-inside: avoid;
        }

        .footer-table td {
            width: 33.33%;
            text-align: center;
            vertical-align: top;
        }

        .footer-title {
            min-height: 22px;
            font-size: 9px;
            font-weight: bold;
        }

        .signature-space {
            height: 55px;
        }

        .footer-name {
            font-weight: bold;
            text-decoration: underline;
        }

        .print-info {
            margin-top: 12px;
            font-size: 7.5px;
        }

        .page-number {
            float: right;
        }

        .operator-cell {
            width: 55% !important;
            padding-left: 10px;
            text-align: left !important;
            vertical-align: top;
        }


        .operator-date {
            margin: 0 0 2px 0;
            padding: 0;
            font-size: 8px;
            font-style: italic;
            text-align: left;
            line-height: 1.1;
        }

        .operator-table {
            width: auto;
            margin: 0;
            border-collapse: separate;
            border-spacing: 0 2px;
            font-size: 9px;
        }

        .operator-table td {
            padding: 0;
            border: 0;
            line-height: 1.05;
        }

        .operator-label {
            width: auto;
            padding-right: 12px !important;
            text-align: left !important;
            font-weight: normal;
            white-space: nowrap;
        }

        .operator-value {
            width: auto;
            padding-left: 0 !important;
            text-align: left !important;
            font-weight: bold;
            text-decoration: underline;
            white-space: nowrap;
        }

        .signature-container {
            width: 100px;
            height: 48px;
            margin: 2px auto;
            text-align: center;
            overflow: hidden;
        }

        .signature-image {
            display: block;
            width: auto;
            height: auto;
            max-width: 100px;
            max-height: 48px;
            margin: 0 auto;
        }

        .signature-space {
            height: 52px;
        }
    </style>
</head>

<body>
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                @if ($logoBase64)
                    <img src="{{ $logoBase64 }}" class="logo" alt="Logo">
                @endif
            </td>

            <td>
                <div class="hospital-name">
                    {{ $hospital['name'] }}
                </div>

                <div class="hospital-address">
                    {{ $hospital['address'] }}
                </div>

                <div class="document-title">
                    HASIL PEMERIKSAAN RADIOLOGI
                </div>
            </td>

            <td style="width:100px;"></td>
        </tr>
    </table>

    <div class="header-line"></div>

    <table class="identity-table">
        <tr>
            <td class="label-left">ID. RAD.</td>
            <td class="colon">:</td>
            <td class="value-left">
                {{ $radiologi['idRad'] ?? '-' }}
            </td>

            <td class="identity-gap"></td>

            <td class="label-right">NAMA PASIEN</td>
            <td class="colon">:</td>
            <td class="value-right">
                {{ $patient['nama'] ?? '-' }}
            </td>
        </tr>

        <tr>
            <td class="label-left">NO. REGISTRASI</td>
            <td class="colon">:</td>
            <td class="value-left">
                {{ $radiologi['idReg'] ?? '-' }}
            </td>

            <td class="identity-gap"></td>

            <td class="label-right">ALAMAT</td>
            <td class="colon">:</td>
            <td class="value-right">
                {{ $patient['alamat'] ?? '-' }}
            </td>
        </tr>

        <tr>
            <td class="label-left">NO. REKAM MEDIK</td>
            <td class="colon">:</td>
            <td class="value-left">
                {{ $patient['regNum'] ?? '-' }}
            </td>

            <td class="identity-gap"></td>

            <td class="label-right">JENIS KELAMIN</td>
            <td class="colon">:</td>
            <td class="value-right">
                {{ $patient['gender'] ?? '-' }}
            </td>
        </tr>

        <tr>
            <td class="label-left">UMUR</td>
            <td class="colon">:</td>
            <td class="value-left">
                {{ $radiologi['umurTahun'] ?? 0 }} TH
                {{ $radiologi['umurBulan'] ?? 0 }} BLN
                {{ $radiologi['umurHari'] ?? 0 }} HR
            </td>

            <td class="identity-gap"></td>

            <td class="label-right">DOKTER</td>
            <td class="colon">:</td>
            <td class="value-right">
                {{ $radiologi['dokter'] ?? '-' }}
            </td>
        </tr>

        <tr>
            <td class="label-left">ALAT</td>
            <td class="colon">:</td>
            <td class="value-left">
                {{ $radiologi['alat'] ?? '-' }}
            </td>

            <td class="identity-gap"></td>

            <td class="label-right">KELAS</td>
            <td class="colon">:</td>
            <td class="value-right">
                {{ $radiologi['kelas'] ?? '-' }}
            </td>
        </tr>

        <tr>
            <td></td>
            <td></td>
            <td></td>

            <td class="identity-gap"></td>

            <td class="label-right">TANGGAL PERIKSA</td>
            <td class="colon">:</td>
            <td class="value-right">
                {{ $fmtDate($radiologi['tanggal'] ?? null) }}
            </td>
        </tr>
    </table>

    <table class="result-table">
        <thead>
            <tr>
                <th style="width:5%;">NO.</th>
                <th style="width:28%;">JENIS PERIKSA</th>
                <th>HASIL PEMERIKSAAN</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($pemeriksaan as $item)
                <tr>
                    <td>{{ $item['no'] }}</td>

                    <td>
                        <strong>{{ $item['periksa'] }}</strong>
                    </td>

                    <td class="result-text">
                        {{ $item['hasil'] }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="footer-table">
        <tr>
            <td colspan="2" class="operator-cell">
                <div class="operator-date">
                    {{ $fmtDateIndo($printedAt) }}
                </div>

                <table class="operator-table">
                    <tr>
                        <td class="operator-label">User</td>
                        <td class="operator-value">
                            {{ $radiologi['user'] ?? '-' }}
                        </td>
                    </tr>

                    <tr>
                        <td class="operator-label">Shift</td>
                        <td class="operator-value">
                            {{ $radiologi['shift'] ?? '-' }}
                        </td>
                    </tr>
                </table>
            </td>
            <td>
                <div class="footer-title">
                    Penanggung Jawab Pelayanan Radiologi
                </div>

                @if (!empty($radiologi['ttd']))
                    <div class="signature-container">
                        <img src="{{ $radiologi['ttd'] }}" class="signature-image" alt="Tanda Tangan Dokter Radiologi">
                    </div>
                @else
                    <div class="signature-space"></div>
                @endif

                <div class="footer-name">
                    {{ $radiologi['dokterRadiologi'] ?? '-' }}
                </div>
            </td>
        </tr>
    </table>


</body>

</html>
