@php
    use Carbon\Carbon;

    $fmtDate = function ($value, $format = 'd/m/Y') {
        if (!$value) {
            return '-';
        }
        try {
            return Carbon::parse($value)->format($format);
        } catch (\Throwable $e) {
            return (string) $value;
        }
    };

    $printedAt = $printedAt ?? now('Asia/Jakarta');
    $hospital = $hospital ?? [];
    $patient = $patient ?? [];
    $labs = $labs ?? [];

    $logoPath = public_path('img/logo-rs1.png');
    $logoBase64 = '';

    if (is_file($logoPath)) {
        $ext = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
        $mime = $ext === 'svg' ? 'image/svg+xml' : 'image/png';
        $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
    }
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Hasil Pemeriksaan Laboratorium</title>

    <style>
        @page {
            margin: 10px 16px 10px 16px;
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

        .page {
            position: relative;
            min-height: 0;
            padding-bottom: 0;
            page-break-inside: avoid;
        }

        .page-break {
            page-break-after: always;
        }

        .header-table,
        .identity-table,
        .result-table,
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: middle;
            border: 0;
        }

        .logo-cell {
            width: 105px;
            text-align: center;
        }

        .logo {
            width: 88px;
            height: auto;
        }

        .hospital-name {
            margin: 0;
            font-size: 14px;
            font-weight: 700;
            text-align: center;
            letter-spacing: .2px;
        }

        .hospital-address {
            margin-top: 4px;
            font-size: 11px;
            font-weight: 700;
            font-style: italic;
            text-align: center;
        }

        .document-title {
            margin-top: 14px;
            font-size: 16px;
            font-weight: 700;
            text-align: center;
        }

        .header-line {
            margin-top: 8px;
            border-top: 2px solid #888;
        }

        .identity-table {
            margin-top: 4px;
            font-size: 9.5px;
        }

        .identity-table td {
            padding: 1px 3px;
            vertical-align: top;
            border: 0;
        }

        .label {
            width: 120px;
        }

        .colon {
            width: 8px;
            text-align: center;
        }

        .value {
            font-weight: 700;
        }

        .middle-gap {
            width: 28px;
        }

        .result-table {
            margin-top: 7px;
            font-size: 10.5px;
        }

        .result-table thead th {
            padding: 3px 5px;
            font-weight: 700;
            text-align: left;
            border-top: 2px solid #777;
            border-bottom: 2px solid #222;
        }

        .result-table td {
            padding: 4px 5px;
            vertical-align: top;
            border: 0;
        }

        .col-no {
            width: 5%;
        }

        .col-test {
            width: 35%;
        }

        .col-result {
            width: 30%;
            text-align: center !important;
            font-weight: 700;
        }

        .col-normal {
            width: 30%;
        }

        .category-row td {
            padding-top: 10px;
            padding-bottom: 3px;
            font-weight: 700;
            text-decoration: underline;
        }

        .test-name {
            font-weight: 700;
            font-style: italic;
        }

        .flag-high {
            color: #d60000;
            font-weight: 700;
        }

        .flag-low {
            color: #1f5fd6;
            font-weight: 700;
        }

        .footer {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
        }

        .note-row {
            margin-bottom: 3px;
            font-size: 9px;
            font-weight: 700;
        }

        .note-critical {
            color: #d60000;
        }

        .note-doctor {
            margin-left: 12px;
            color: #1f5fd6;
        }

        .footer-table td {
            width: 25%;
            text-align: center;
            vertical-align: top;
            border: 0;
        }

        .footer-title {
            min-height: 24px;
            font-size: 9px;
            font-weight: 700;
        }

        .qr {
            display: block;
            width: 64px;
            height: 64px;
            margin: 3px auto;
        }

        .qr-svg {
            width: 64px;
            height: 64px;
            margin: 3px auto;
            text-align: center;
            overflow: hidden;
        }

        .qr-svg svg {
            display: block;
            width: 64px !important;
            height: 64px !important;
            margin: 0 auto;
        }

        .qr-placeholder {
            height: 70px;
        }

        .verify-badge {
            width: 50px;
            height: 50px;
            margin: 8px auto 10px;
            border-radius: 50%;
            background: #3faf57;
            color: #fff;
            font-size: 34px;
            line-height: 48px;
            font-weight: 700;
            text-align: center;
        }

        .footer-name {
            font-size: 9px;
            font-weight: 700;
            text-decoration: underline;
        }

        .print-info {
            margin-top: 4px;
            font-size: 7.5px;
        }

        .page-number {
            float: right;
        }

        .verify-stamp {
            width: 64px;
            height: 64px;
            margin: 3px auto;
            border: 3px solid #2e8b57;
            border-radius: 50%;
            text-align: center;
            color: #2e8b57;
            font-weight: 700;
            position: relative;
        }

        .verify-check {
            font-size: 34px;
            line-height: 39px;
            font-weight: 700;
        }

        .verify-text {
            font-size: 6px;
            line-height: 8px;
            letter-spacing: 0.3px;
            border-top: 1px solid #2e8b57;
            padding-top: 2px;
            margin: 0 5px;
        }

        .verify-placeholder {
            height: 70px;
        }

        .critical-result {
            color: #d60000;
            font-weight: 700;
        }

        .verify-stamp {
            width: 64px;
            height: 64px;
            margin: 3px auto;
            border: 3px solid;
            border-radius: 50%;
            text-align: center;
            font-weight: 700;
        }

        .verify-success {
            color: #2e8b57;
            border-color: #2e8b57;
        }

        .verify-failed {
            color: #c62828;
            border-color: #c62828;
        }

        .verify-symbol {
            font-size: 32px;
            line-height: 39px;
        }

        .verify-text {
            margin: 0 4px;
            padding-top: 2px;
            border-top: 1px solid currentColor;
            font-size: 5.5px;
            line-height: 7px;
        }
    </style>
</head>

<body>
    @foreach ($labs as $index => $lab)
        @php
            $qr = $qrList[$index] ?? [
                'pj' => null,
                'usr' => null,
                'double' => null,
            ];

            $categories = $lab['kats'] ?? [];
            $number = 1;
        @endphp

        <div class="page">
            <table class="header-table">
                <tr>
                    <td class="logo-cell">
                        @if ($logoBase64)
                            <img src="{{ $logoBase64 }}" class="logo" alt="Logo">
                        @endif
                    </td>

                    <td>
                        <div class="hospital-name">
                            {{ $hospital['name'] ?? 'INSTALASI LABORATORIUM RUMAH SAKIT AISYIYAH BOJONEGORO' }}
                        </div>

                        <div class="hospital-address">
                            {{ $hospital['address'] ?? 'Jl. Panglima Sudirman 48 Bojonegoro Telp. 0353-881748. Fax 0353-88597' }}
                        </div>

                        <div class="document-title">
                            HASIL PEMERIKSAAN LABORAT
                        </div>
                    </td>

                    <td style="width:105px;"></td>
                </tr>
            </table>

            <div class="header-line"></div>

            <table class="identity-table">
                <tr>
                    <td class="label">NAMA PASIEN</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $patient['nama'] ?? '-' }}</td>

                    <td class="middle-gap"></td>

                    <td class="label">ID. LAB</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $lab['idlab'] ?? '-' }}</td>
                </tr>

                <tr>
                    <td class="label">ALAMAT</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $patient['addr'] ?? '-' }}</td>

                    <td></td>

                    <td class="label">NO. REGISTRASI</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $patient['idReg'] ?? '-' }}</td>
                </tr>

                <tr>
                    <td class="label">KEL/ TGL LAHIR</td>
                    <td class="colon">:</td>
                    <td class="value">
                        {{ $patient['gender'] ?? '-' }}
                        &nbsp;&nbsp;&nbsp;
                        {{ $patient['dob'] ? $fmtDate($patient['dob'], 'l, d F Y') : '-' }}
                    </td>

                    <td></td>

                    <td class="label">NO. REKAM MEDIK</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $patient['regNum'] ?? '-' }}</td>
                </tr>

                <tr>
                    <td class="label">UMUR</td>
                    <td class="colon">:</td>
                    <td class="value">
                        {{ $lab['th'] ?? '-' }} Thn
                        &nbsp;&nbsp;&nbsp;
                        {{ $lab['bln'] ?? '-' }} Bln
                        &nbsp;&nbsp;&nbsp;
                        {{ $lab['hr'] ?? '-' }} Hr
                    </td>

                    <td></td>

                    <td class="label">PX RUJUKAN</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $lab['rujukan'] ?? '-' }}</td>
                </tr>

                <tr>
                    <td class="label">DOKTER</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $lab['dokter'] ?? '-' }}</td>

                    <td></td>

                    <td class="label">TANGGAL PERIKSA</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $fmtDate($lab['tanggal'] ?? null) }}</td>
                </tr>

                <tr>
                    <td class="label">KELAS</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $lab['kelas'] ?? '-' }}</td>

                    <td></td>

                    <td class="label">RUANGAN</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $lab['ruangan'] ?? '-' }}</td>
                </tr>

                <tr>
                    <td class="label">JAM PERIKSA</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $lab['jamAmbil'] ?? '-' }}</td>

                    <td></td>

                    <td class="label">JAM SELESAI</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $lab['jamcheck'] ?? '-' }}</td>
                </tr>
            </table>

            <table class="result-table">
                <thead>
                    <tr>
                        <th class="col-no">NO.</th>
                        <th class="col-test">JENIS PERIKSA</th>
                        <th class="col-result">HASIL PEMERIKSAAN</th>
                        <th> </th>
                        <th class="col-normal">NORMAL</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($categories as $category)
                        <tr class="category-row">
                            <td colspan="5">{{ strtoupper($category['kategori'] ?? 'LAIN-LAIN') }}</td>
                        </tr>

                        @foreach ($category['items'] ?? [] as $item)
                            @php
                                $flag = strtoupper((string) ($item['flag'] ?? ''));
                            @endphp

                            <tr>
                                <td>{{ $number++ }}</td>

                                <td class="test-name">
                                    {{ $item['nama'] ?? '-' }}
                                </td>

                                @php
                                    $isCritical = (int) ($item['isOk'] ?? 0) === 1;
                                @endphp

                                <td class="col-result {{ $isCritical ? 'critical-result' : '' }}">
                                    {{ $item['hasil'] ?? '-' }}
                                </td>

                                <td>
                                    @if ($flag === 'H')
                                        <span class="flag-high">H</span>
                                    @elseif ($flag === 'L')
                                        <span class="flag-low">L</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $item['normal'] ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>

            <div class="footer">
                <div class="note-row">
                    <span class="note-critical">NB : Nilai Kritis Tercetak Merah</span>

                    @if (!empty($lab['noteLap']))
                        <span class="note-doctor">
                            NOTE dr. Sp.PK : {{ $lab['noteLap'] }}
                        </span>
                    @endif
                </div>

                <table class="footer-table">
                    <tr>
                        <td>
                            <div class="footer-title">
                                Penanggung Jawab Pelayanan Laboratorium
                            </div>

                            @if (!empty($qr['pj']))
                                <img src="{{ $qr['pj'] }}" class="qr" alt="QR Penanggung Jawab">
                            @else
                                <div class="qr-placeholder"></div>
                            @endif

                            <div class="footer-name">
                                {{ $pj ?? '-' }}
                            </div>
                        </td>

                        <td>
                            <div class="footer-title">Verif</div>

                            @if ((int) ($lab['ver'] ?? 0) === 0)
                                <div class="verify-stamp verify-failed">
                                    <div class="verify-symbol">&#10005;</div>
                                </div>
                            @else
                                <div class="verify-stamp verify-success">
                                    <div class="verify-symbol">&#10003;</div>
                                </div>
                            @endif
                        </td>

                        <td>
                            <div class="footer-title">Double Check</div>

                            @if (!empty($qr['double']))
                                <img src="{{ $qr['double'] }}" class="qr" alt="QR Double Check">
                            @else
                                <div class="qr-placeholder"></div>
                            @endif

                            <div class="footer-name">
                                {{ $lab['user2'] ?? ($lab['usr'] ?? '-') }}
                            </div>
                        </td>

                        <td>
                            <div class="footer-title">Petugas Laboratorium</div>

                            @if (!empty($qr['usr']))
                                <img src="{{ $qr['usr'] }}" class="qr" alt="QR Petugas">
                            @else
                                <div class="qr-placeholder"></div>
                            @endif

                            <div class="footer-name">
                                {{ $lab['user1'] ?? ($lab['usr'] ?? '-') }}
                            </div>
                        </td>

                    </tr>
                </table>


            </div>
        </div>

        @if (!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>

</html>
