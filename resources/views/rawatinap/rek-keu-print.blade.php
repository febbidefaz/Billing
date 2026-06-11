@php
    use Carbon\Carbon;

    $R = fn($v) => number_format($v ?? 0, 0, ',', '.');
    $fmt = fn($v) => $v ? Carbon::parse($v)->format('d/m/Y') : '-';

    $totalKamar = collect($kamar)->sum('TotalSewa');
    $totalAskep = collect($kamar)->sum('TotalAskep');
    $totalVisit = collect($rekeningVisitKeu)->sum('Biaya');
    $totalUtilitas = collect($rekeningUtilitasKeu)->sum('Netto');
    $totalLab = collect($rekeningLaboratKeu ?? [])->sum('Netto');
    $totalRadiologi = collect($rekeningRadiologiKeu ?? [])->sum('Netto');
    $totalLain = collect($lainlain)->sum('TotalLain');
    $totalOperasi = collect($rekeningOperasiKeu ?? [])->sum('Biaya');
    $totalOperasiIgd = collect($rekeningOperasiIgdKeu)->sum('Biaya');
    $totalOperasiPoli = collect($rekeningOperasiPoliKeu)->sum('Biaya');
    $totalObat = collect($obat)->sum('HutangObat');

    $karcisJasa = ($pasien->Biaya ?? 0) + ($pasien->JasaPrk ?? 0);

    $grandTotal =
        $karcisJasa +
        $totalKamar +
        $totalAskep +
        $totalVisit +
        $totalUtilitas +
        $totalRadiologi +
        $totalLab +
        $totalLain +
        $totalOperasi +
        $totalOperasiIgd +
        $totalOperasiPoli +
        $totalObat +
        ($grandTotalFarmasiApi ?? 0);

    $dijamin = $pasien->DownPay ?? 0;
    $diskon = 0;
    $sisa = $grandTotal - $dijamin;

    $logoFile = public_path('img/logo.png');
    $logoSrc = is_file($logoFile) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoFile)) : null;
@endphp

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Rekening Rawat Inap</title>

    <style>
        @page {
            size: 210mm 330mm;
            margin: 8mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 4px 6px;
            vertical-align: top;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .left {
            text-align: left;
        }

        .bold {
            font-weight: bold;
        }

        .u {
            text-decoration: underline;
        }

        .header-title {
            font-size: 18px;
            font-weight: bold;
            letter-spacing: .4px;
        }

        .header-sub {
            font-size: 11px;
            font-weight: bold;
            text-decoration: underline;
        }

        .rekening-title {
            font-size: 16px;
            font-weight: bold;
            font-style: italic;
            margin-top: 5px;
        }

        .line {
            border-top: 1px solid #000;
            margin: 6px 0;
        }

        .section-title {
            margin-top: 10px;
            margin-bottom: 3px;
            font-weight: bold;
            font-style: italic;
        }

        .nb th {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            font-weight: bold;
        }

        .subtotal {
            font-weight: bold;
            text-align: right;
            margin: 3px 0 6px;
        }

        .totals td {
            padding: 5px 6px;
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

        .subtotal-value {
            font-weight: bold;
            text-decoration: underline;
        }

        tfoot,
        .subtotal-row {
            page-break-inside: avoid;
        }

        tfoot td {
            padding-top: 4px;
            font-weight: bold;
        }

        tfoot .u {
            text-decoration: underline;
        }

        @media print {
            .print-btn {
                display: none;
            }

            .print-wrapper>thead {
                display: table-header-group;
            }

            .print-wrapper>tbody {
                display: table-row-group;
            }

            .nb tr,
            .subtotal-row {
                page-break-inside: avoid;
            }
        }

        .obat-table {
            width: 45% !important;
            margin-left: auto;
            margin-right: auto;
        }

        .obat-table th,
        .obat-table td {
            padding: 2px 4px;
        }

        .mt-2 {
            margin-top: 8px;
        }

        .mt-3 {
            margin-top: 14px;
        }

        .italic {
            font-style: italic;
        }

        .u {
            text-decoration: underline;
        }

        .bold {
            font-weight: bold;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .table-head th {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            font-weight: bold;
        }

        .print-wrapper {
            margin-top: 0;
        }
    </style>
</head>

<body>
    {{-- HEADER --}}
    <table>
        <tr>
            <td style="width:90px; text-align:center;">
                @if ($logoSrc)
                    <img src="{{ $logoSrc }}" width="75">
                @endif
            </td>

            <td class="center">
                <div class="header-title">RUMAH SAKIT 'AISYIYAH</div>
                <div class="header-sub">
                    JL. KH. HASYIM ASY'ARI 17 BOJONEGORO, TELP. (0353)885978
                </div>
                <div class="rekening-title">REKENING RAWAT INAP</div>
            </td>

            <td style="width:90px;"></td>
        </tr>
    </table>

    <button onclick="window.print()" class="print-btn">Print</button>
    <table class="print-wrapper">

        <thead>
            <tr>
                <td>
                    <div class="line"></div>

                    {{-- IDENTITAS --}}
                    <table>
                        <tr>
                            <td style="width:50%; padding:0;">
                                <table>
                                    <tr>
                                        <td style="width:90px;">REGISTER</td>
                                        <td style="width:10px;">:</td>
                                        <td class="bold">{{ $pasien->RegNum ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td>NAMA PASIEN</td>
                                        <td>:</td>
                                        <td class="bold">{{ $pasien->Nama ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td>ALAMAT</td>
                                        <td>:</td>
                                        <td class="bold">{{ $pasien->Addr ?? '-' }}</td>
                                    </tr>
                                </table>
                            </td>

                            <td style="width:50%; padding:0;">
                                <table>
                                    <tr>
                                        <td style="width:90px;">NOMER</td>
                                        <td style="width:10px;">:</td>
                                        <td class="bold">{{ $pasien->ID ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td>L/P</td>
                                        <td>:</td>
                                        <td class="bold">{{ $pasien->Jenis_Kelamin ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td>TGL LAHIR</td>
                                        <td>:</td>
                                        <td class="bold">{{ $fmt($pasien->Tanggal_Lahir ?? null) }}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <div class="line"></div>
                </td>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td>
                    {{-- KAMAR --}}
                    @if (count($kamar ?? []))
                        <table class="nb">
                            <tr class="table-head">
                                <th>KELAS/RUANG</th>
                                <th>MASUK</th>
                                <th>KELUAR</th>
                                <th class="right">LAMA(HR)</th>
                                <th class="right">BIAYA/SEWA</th>
                                <th class="right">DISCOUNT</th>
                            </tr>
                            <tbody>
                                @foreach ($kamar as $k)
                                    <tr>
                                        <td>{{ $k->NamaKamar ?? '-' }}</td>
                                        <td class="center">{{ $fmt($k->TMasuk ?? null) }}</td>
                                        <td class="center">{{ $fmt($k->TKeluar ?? null) }}</td>
                                        <td class="right">{{ $k->LamaRawat ?? 0 }}</td>
                                        <td class="right">{{ $R($k->TotalSewa ?? 0) }}</td>
                                        <td class="right">{{ $R($k->TotalDisc ?? 0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tr class="subtotal-row">
                                <td colspan="3"></td>

                                <td class="right bold u">
                                    SUB TOTAL
                                </td>

                                <td class="right bold u">
                                    {{ $R($totalKamar) }}
                                </td>

                                <td class="right bold u">
                                    {{ $R(collect($kamar)->sum('TotalDisc')) }}
                                </td>
                            </tr>
                        </table>
                    @endif

                    {{-- VISIT --}}
                    @if (count($rekeningVisitKeu ?? []))
                        <div class="section-title">VISIT / KUNJUNGAN DOKTER</div>
                        <table class="nb">
                            <tr class="table-head">
                                <th>DOKTER</th>
                                <th class="left">KELAS</th>
                                <th class="left">RUANG</th>
                                <th class="right">JUMLAH</th>
                                <th class="right">BIAYA</th>
                                <th class="right">DISCOUNT</th>
                            </tr>
                            <tbody>
                                @foreach ($rekeningVisitKeu as $v)
                                    <tr>
                                        <td>{{ $v->Dokter ?? '-' }}</td>
                                        <td>{{ $v->Kelas ?? '-' }}</td>
                                        <td>{{ $v->RoomName ?? '-' }}</td>
                                        <td class="right">{{ $R($v->NTimes ?? 0) }}</td>
                                        <td class="right">{{ $R($v->Biaya ?? 0) }}</td>
                                        <td class="right">{{ $R($v->Discount ?? 0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tr class="subtotal-row">
                                <td colspan="3" class="right bold u">

                                </td>

                                <td class="right bold u">
                                    SUB TOTAL
                                </td>

                                <td class="right bold u">
                                    {{ $R(collect($rekeningVisitKeu ?? [])->sum('Biaya')) }}
                                </td>

                                <td class="right bold u">
                                    {{ $R(collect($rekeningVisitKeu ?? [])->sum('Discount')) }}
                                </td>
                            </tr>
                        </table>

                    @endif

                    {{-- UTILITAS --}}
                    @if (count($rekeningUtilitasKeu ?? []))
                        <div class="section-title">UTILITAS</div>

                        <table class="nb">
                            <tr class="table-head">
                                <th>UTILITAS</th>
                                <th class="left">KELAS</th>
                                <th class="left">RUANG</th>
                                <th>DOKTER</th>
                                <th class="right">JUMLAH</th>
                                <th class="right">BIAYA</th>
                                <th class="right">DISCOUNT</th>
                            </tr>

                            <tbody>
                                @foreach ($rekeningUtilitasKeu as $u)
                                    <tr>
                                        <td>{{ $u->Tindak ?? '-' }}</td>
                                        <td class="left">{{ $u->Kelas ?? '-' }}</td>
                                        <td class="left">{{ $u->RoomName ?? '-' }}</td>
                                        <td>{{ $u->Dokter ?? '-' }}</td>
                                        <td class="right">
                                            {{ $R($u->NTimes ?? 0) }}
                                        </td>
                                        <td class="right">
                                            {{ $R($u->BiayaTindak ?? 0) }}
                                        </td>
                                        <td class="right">
                                            {{ $R($u->Discount ?? 0) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                            <tr class="subtotal-row">
                                <td colspan="5" class="right bold u">
                                    SUB TOTAL
                                </td>

                                <td class="right bold u">
                                    {{ $R(collect($rekeningUtilitasKeu ?? [])->sum('BiayaTindak')) }}
                                </td>

                                <td class="right bold u">
                                    {{ $R(collect($rekeningUtilitasKeu ?? [])->sum('Discount')) }}
                                </td>
                            </tr>
                        </table>
                    @endif

                    {{-- PENUNJANG --}}
                    @if ($totalLab > 0 || $totalRadiologi > 0)
                        <div class="section-title">REKENING JASA PENUNJANG MEDIK</div>
                    @endif

                    {{-- LAB --}}
                    @if (count($rekeningLaboratKeu ?? []))
                        <div class="section-title">- LABORAT</div>
                        <table class="nb">
                            <tr class="table-head">
                                <th>TANGGAL</th>
                                <th class="left">ID LAB</th>
                                <th class="right">PELAYANAN</th>
                                <th class="right">PERUJUK</th>
                                <th class="right">RSA</th>
                                <th class="right">PEMBACA</th>
                                <th class="right">REAGEN</th>
                                <th class="left">ANALIS</th>
                                <th class="left">USER</th>
                                <th class="right">BIAYA</th>
                                <th class="right">DISCOUNT</th>
                            </tr>
                            <tbody>
                                @foreach (collect($rekeningLaboratKeu)->groupBy('Dokter') as $dokter => $items)
                                    <tr>
                                        <td colspan="7" class="bold">
                                            {{ $dokter ?: 'DOKTER TIDAK DIISI' }}
                                        </td>
                                    </tr>

                                    @foreach ($items as $l)
                                        <tr>
                                            <td class="center">{{ $fmt($l->TLab ?? null) }}</td>
                                            <td>{{ $l->IDLab ?? '-' }}</td>
                                            <td class="right">{{ $R($l->JasaPelayanan ?? 0) }}</td>
                                            <td class="right">{{ $R($l->JasaPerujuk ?? 0) }}</td>
                                            <td class="right">{{ $R($l->JasaRS ?? 0) }}</td>
                                            <td class="right">{{ $R($l->Pembaca ?? 0) }}</td>
                                            <td class="right">{{ $R($l->BHPReagen ?? 0) }}</td>
                                            <td>{{ $l->nama ?? '-' }}</td>
                                            <td>{{ $l->Usr ?? '-' }}</td>
                                            <td class="right">{{ $R($l->BiayaLab ?? 0) }}</td>
                                            <td class="right">{{ $R($l->Discount ?? 0) }}</td>
                                        </tr>
                                    @endforeach

                                    <tr>
                                        <td colspan="2" class="right bold u">
                                            SUB {{ $dokter ?: 'DOKTER TIDAK DIISI' }}
                                        </td>

                                        <td class="right bold u">{{ $R($items->sum('JasaPelayanan')) }}</td>
                                        <td class="right bold u">{{ $R($items->sum('JasaPerujuk')) }}</td>
                                        <td class="right bold u">{{ $R($items->sum('JasaRS')) }}</td>
                                        <td class="right bold u">{{ $R($items->sum('Pembaca')) }}</td>
                                        <td class="right bold u">{{ $R($items->sum('BHPReagen')) }}</td>

                                        <td colspan="2"></td>

                                        <td class="right bold u">{{ $R($items->sum('BiayaLab')) }}</td>
                                        <td class="right bold u">{{ $R($items->sum('Discount')) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>

                            <tr class="subtotal-row">
                                <td colspan="2" class="right bold u">
                                    SUB TOTAL LAB
                                </td>

                                <td class="right bold u">
                                    {{ $R(collect($rekeningLaboratKeu ?? [])->sum('JasaPelayanan')) }}
                                </td>
                                <td class="right bold u">
                                    {{ $R(collect($rekeningLaboratKeu ?? [])->sum('JasaPerujuk')) }}</td>
                                <td class="right bold u">{{ $R(collect($rekeningLaboratKeu ?? [])->sum('JasaRS')) }}
                                </td>
                                <td class="right bold u">{{ $R(collect($rekeningLaboratKeu ?? [])->sum('Pembaca')) }}
                                </td>
                                <td class="right bold u">
                                    {{ $R(collect($rekeningLaboratKeu ?? [])->sum('BHPReagen')) }}</td>

                                <td colspan="2"></td>

                                <td class="right bold u">{{ $R(collect($rekeningLaboratKeu ?? [])->sum('BiayaLab')) }}
                                </td>
                                <td class="right bold u">{{ $R(collect($rekeningLaboratKeu ?? [])->sum('Discount')) }}
                                </td>
                            </tr>
                        </table>
                    @endif

                    {{-- RADIOLOGI --}}
                    @if (count($rekeningRadiologiKeu ?? []))
                        <div class="section-title">- RADIOLOGI</div>

                        <table class="nb">
                            <tr class="table-head">
                                <th>TANGGAL</th>
                                <th class="left">PERIKSA</th>
                                <th class="right">PELAKSANA</th>
                                <th class="right">PERUJUK</th>
                                <th class="right">DOKTER</th>
                                <th class="right">FILM</th>
                                <th class="right">RSA</th>
                                <th class="left">USER</th>
                                <th class="right">BIAYA</th>
                                <th class="right">DISCOUNT</th>
                            </tr>
                            <tbody>

                                @foreach (collect($rekeningRadiologiKeu)->groupBy('Dokter') as $dokter => $items)
                                    <tr>
                                        <td colspan="10" class="bold">
                                            {{ $dokter ?: 'DOKTER TIDAK DIISI' }}
                                        </td>
                                    </tr>

                                    @foreach ($items as $r)
                                        <tr>
                                            <td class="center">
                                                {{ $fmt($r->TRad ?? null) }}
                                            </td>

                                            <td>
                                                {{ $r->Periksa ?? '-' }}
                                            </td>

                                            <td class="right">
                                                {{ $R($r->JasaPelaksana ?? 0) }}
                                            </td>

                                            <td class="right">
                                                {{ $R($r->JasaPerujuk ?? 0) }}
                                            </td>

                                            <td class="right">
                                                {{ $R($r->JasaDokter ?? 0) }}
                                            </td>

                                            <td class="right">
                                                {{ $R($r->Film ?? 0) }}
                                            </td>

                                            <td class="right">
                                                {{ $R($r->RSA ?? 0) }}
                                            </td>

                                            <td>
                                                {{ $r->Usr ?? '-' }}
                                            </td>

                                            <td class="right">
                                                {{ $R($r->BiayaRad ?? 0) }}
                                            </td>

                                            <td class="right">
                                                {{ $R($r->Discount ?? 0) }}
                                            </td>
                                        </tr>
                                    @endforeach

                                    <tr>
                                        <td colspan="1" class="right bold u">
                                            SUB {{ $dokter }}
                                        </td>
                                        <td></td>
                                        <td class="right bold u">{{ $R($items->sum('JasaPelaksana')) }}</td>
                                        <td class="right bold u">{{ $R($items->sum('JasaPerujuk')) }}</td>
                                        <td class="right bold u">{{ $R($items->sum('JasaDokter')) }}</td>
                                        <td class="right bold u">{{ $R($items->sum('Film')) }}</td>
                                        <td class="right bold u">{{ $R($items->sum('RSA')) }}</td>

                                        <td></td>

                                        <td class="right bold u">{{ $R($items->sum('BiayaRad')) }}</td>
                                        <td class="right bold u">{{ $R($items->sum('Discount')) }}</td>
                                    </tr>
                                @endforeach

                            </tbody>

                            <tr class="subtotal-row">
                                <td colspan="1" class="right bold u">
                                    SUB TOTAL RADIOLOGI
                                </td>
                                <td></td>
                                <td class="right bold u">
                                    {{ $R(collect($rekeningRadiologiKeu)->sum('JasaPelaksana')) }}</td>
                                <td class="right bold u">{{ $R(collect($rekeningRadiologiKeu)->sum('JasaPerujuk')) }}
                                </td>
                                <td class="right bold u">{{ $R(collect($rekeningRadiologiKeu)->sum('JasaDokter')) }}
                                </td>
                                <td class="right bold u">{{ $R(collect($rekeningRadiologiKeu)->sum('Film')) }}</td>
                                <td class="right bold u">{{ $R(collect($rekeningRadiologiKeu)->sum('RSA')) }}</td>

                                <td></td>

                                <td class="right bold u">
                                    {{ $R(collect($rekeningRadiologiKeu)->sum('BiayaRad')) }}
                                </td>

                                <td class="right bold u">
                                    {{ $R(collect($rekeningRadiologiKeu)->sum('Discount')) }}
                                </td>
                            </tr>
                        </table>
                    @endif

                    {{-- LAIN-LAIN --}}
                    @if (count($lainlain ?? []))
                        <div class="section-title">LAIN-LAIN</div>
                        <table class="nb">
                            <tr class="table-head">
                                <th>KETERANGAN</th>
                                <th>TANGGAL</th>
                                <th class="right">BIAYA</th>
                                <th class="right">DISCOUNT</th>
                            </tr>
                            <tbody>
                                @foreach ($lainlain as $l)
                                    <tr>
                                        <td>{{ $l->Lain ?? '-' }}</td>
                                        <td class="center">{{ $fmt($l->TGL ?? null) }}</td>
                                        <td class="right">{{ $R($l->TotalLain ?? 0) }}</td>
                                        <td class="right">{{ $R($l->TotalDisc ?? 0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tr class="subtotal-row">
                                <td></td>

                                <td class="right bold u">
                                    SUB TOTAL
                                </td>

                                <td class="right bold u">
                                    {{ $R($totalLain) }}
                                </td>

                                <td class="right bold u">
                                    {{ $R(collect($lainlain)->sum('TotalDisc')) }}
                                </td>
                            </tr>
                        </table>

                    @endif

                    {{-- OPERASI --}}
                    @if (count($rekeningOperasiKeu ?? []))
                        <div class="section-title">TINDAKAN / OPERASI</div>

                        <table class="nb">
                            <tr class="table-head">
                                <th>TINDAKAN</th>
                                <th class="left">RUANG</th>
                                <th class="left">KELAS</th>
                                <th class="left">NAMA OP</th>
                                <th class="right">OPER</th>
                                <th class="right">ASS</th>
                                <th class="right">BAHAN</th>
                                <th class="right">ALAT</th>
                                <th class="right">JRS</th>
                                <th class="right">CSSD</th>
                                <th class="right">JML</th>
                                <th class="right">BIAYA</th>
                            </tr>

                            <tbody>
                                @foreach ($rekeningOperasiKeu as $o)
                                    <tr>
                                        <td>{{ $o->Nama_jenis ?? '-' }}</td>
                                        <td class="left">{{ $o->RoomName ?? '-' }}</td>
                                        <td class="left">{{ $o->Kelas ?? '-' }}</td>
                                        <td class="left">{{ $o->Op ?? '-' }}</td>
                                        <td class="right">{{ $R($o->Oper ?? 0) }}</td>
                                        <td class="right">{{ $R($o->Ass ?? 0) }}</td>
                                        <td class="right">{{ $R($o->Bahan ?? 0) }}</td>
                                        <td class="right">{{ $R($o->Alat ?? 0) }}</td>
                                        <td class="right">{{ $R($o->Jasa ?? 0) }}</td>
                                        <td class="right">{{ $R($o->Cssd ?? 0) }}</td>
                                        <td class="right">{{ $R($o->c ?? 0) }}</td>
                                        <td class="right">{{ $R($o->Biaya ?? 0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>

                            <tr class="subtotal-row">
                                <td colspan="4" class="right bold u">
                                    SUB TOTAL OPERASI
                                </td>

                                <td class="right bold u">{{ $R(collect($rekeningOperasiKeu ?? [])->sum('Oper')) }}
                                </td>
                                <td class="right bold u">{{ $R(collect($rekeningOperasiKeu ?? [])->sum('Ass')) }}
                                </td>
                                <td class="right bold u">{{ $R(collect($rekeningOperasiKeu ?? [])->sum('Bahan')) }}
                                </td>
                                <td class="right bold u">{{ $R(collect($rekeningOperasiKeu ?? [])->sum('Alat')) }}
                                </td>
                                <td class="right bold u">{{ $R(collect($rekeningOperasiKeu ?? [])->sum('Jasa')) }}
                                </td>
                                <td class="right bold u">{{ $R(collect($rekeningOperasiKeu ?? [])->sum('Cssd')) }}
                                </td>
                                <td class="right bold u">{{ $R(collect($rekeningOperasiKeu ?? [])->sum('c')) }}</td>
                                <td class="right bold u">{{ $R(collect($rekeningOperasiKeu ?? [])->sum('Biaya')) }}
                                </td>
                            </tr>
                        </table>
                    @endif

                    {{-- OPERASI IGD --}}
                    @if (count($rekeningOperasiIgdKeu ?? []))
                        <div class="section-title">TINDAKAN / OPERASI IGD</div>

                        <table class="nb">
                            <tr class="table-head">
                                <th>TINDAKAN</th>
                                <th class="left">RUANG</th>
                                <th class="left">KELAS</th>
                                <th class="right">OPER</th>
                                <th class="right">ASS</th>
                                <th class="right">BAHAN</th>
                                <th class="right">ALAT</th>
                                <th class="right">JML</th>
                                <th class="right">BIAYA</th>
                            </tr>

                            <tbody>
                                @foreach ($rekeningOperasiIgdKeu as $o)
                                    <tr>
                                        <td>{{ $o->Nama_jenis ?? '-' }}</td>
                                        <td class="left">{{ $o->RoomName ?? '-' }}</td>
                                        <td class="left">{{ $o->Kelas ?? '-' }}</td>
                                        <td class="right">{{ $R($o->Oper ?? 0) }}</td>
                                        <td class="right">{{ $R($o->Ass ?? 0) }}</td>
                                        <td class="right">{{ $R($o->Bahan ?? 0) }}</td>
                                        <td class="right">{{ $R($o->Alat ?? 0) }}</td>
                                        <td class="right">{{ $R($o->c ?? 0) }}</td>
                                        <td class="right">{{ $R($o->Biaya ?? 0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>

                            <tr class="subtotal-row">
                                <td colspan="3" class="right bold u">
                                    SUB TOTAL OPERASI IGD
                                </td>
                                <td class="right bold u">{{ $R(collect($rekeningOperasiIgdKeu ?? [])->sum('Oper')) }}
                                </td>
                                <td class="right bold u">{{ $R(collect($rekeningOperasiIgdKeu ?? [])->sum('Ass')) }}
                                </td>
                                <td class="right bold u">
                                    {{ $R(collect($rekeningOperasiIgdKeu ?? [])->sum('Bahan')) }}</td>
                                <td class="right bold u">{{ $R(collect($rekeningOperasiIgdKeu ?? [])->sum('Alat')) }}
                                </td>
                                <td class="right bold u">{{ $R(collect($rekeningOperasiIgdKeu ?? [])->sum('c')) }}
                                </td>
                                <td class="right bold u">{{ $R($totalOperasiIgd) }}</td>
                            </tr>
                        </table>
                    @endif

                    {{-- OPERASI Poli --}}
                    @if (count($rekeningOperasiPoliKeu ?? []))
                        <div class="section-title">TINDAKAN / OPERASI POLI</div>

                        <table class="nb">
                            <tr class="table-head">
                                <th>TINDAKAN</th>
                                <th class="left">RUANG</th>
                                <th class="left">KELAS</th>
                                <th class="right">OPER</th>
                                <th class="right">ASS</th>
                                <th class="right">BAHAN</th>
                                <th class="right">ALAT</th>
                                <th class="right">JML</th>
                                <th class="right">BIAYA</th>
                            </tr>

                            <tbody>
                                @foreach ($rekeningOperasiPoliKeu as $o)
                                    <tr>
                                        <td>{{ $o->Nama_jenis ?? '-' }}</td>
                                        <td class="left">{{ $o->RoomName ?? '-' }}</td>
                                        <td class="left">{{ $o->Kelas ?? '-' }}</td>
                                        <td class="right">{{ $R($o->Oper ?? 0) }}</td>
                                        <td class="right">{{ $R($o->Ass ?? 0) }}</td>
                                        <td class="right">{{ $R($o->Bahan ?? 0) }}</td>
                                        <td class="right">{{ $R($o->Alat ?? 0) }}</td>
                                        <td class="right">{{ $R($o->c ?? 0) }}</td>
                                        <td class="right">{{ $R($o->Biaya ?? 0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>

                            <tr class="subtotal-row">
                                <td colspan="3" class="right bold u">
                                    SUB TOTAL OPERASI IGD
                                </td>
                                <td class="right bold u">
                                    {{ $R(collect($rekeningOperasiPoliKeu ?? [])->sum('Oper')) }}</td>
                                <td class="right bold u">{{ $R(collect($rekeningOperasiPoliKeu ?? [])->sum('Ass')) }}
                                </td>
                                <td class="right bold u">
                                    {{ $R(collect($rekeningOperasiPoliKeu ?? [])->sum('Bahan')) }}</td>
                                <td class="right bold u">
                                    {{ $R(collect($rekeningOperasiPoliKeu ?? [])->sum('Alat')) }}</td>
                                <td class="right bold u">{{ $R(collect($rekeningOperasiPoliKeu ?? [])->sum('c')) }}
                                </td>
                                <td class="right bold u">{{ $R($totalOperasiPoli) }}</td>
                            </tr>
                        </table>
                    @endif

                    {{-- OBAT --}}
                    @if (count($obat ?? []))
                        <div class="section-title">OBAT</div>

                        <table class="nb">
                            <tr class="table-head">
                                <th style="width:35%;"></th>

                                <th style="width:20%;" class="center">
                                    TANGGAL
                                </th>

                                <th style="width:20%;" class="right">
                                    BIAYA
                                </th>

                                <th style="width:25%;"></th>
                            </tr>
                            <tbody>
                                @foreach ($obat as $o)
                                    <tr>
                                        <td></td>

                                        <td class="center">
                                            {{ $fmt($o->{'Invoice date'} ?? null) }}
                                        </td>

                                        <td class="right">
                                            {{ $R($o->HutangObat ?? 0) }}
                                        </td>

                                        <td></td>
                                    </tr>
                                @endforeach
                            </tbody>

                            <tr class="subtotal-row">
                                <td></td>

                                <td class="right bold u">
                                    SUB TOTAL
                                </td>

                                <td class="right bold u">
                                    {{ $R($totalObat) }}
                                </td>

                                <td></td>
                            </tr>
                        </table>
                    @endif

                    {{-- OBAT PAY --}}
                    @if (count($salesFarmasi ?? []))
                        <div class="section-title">OBAPAY</div>

                        <table class="nb">
                            <tr class="table-head">
                                <th style="width:30%;"></th>

                                <th style="width:20%;" class="center">
                                    TANGGAL
                                </th>

                                <th style="width:25%;" class="center">
                                    NO TRANSAKSI
                                </th>

                                <th style="width:20%;" class="right">
                                    BIAYA
                                </th>

                                <th style="width:5%;"></th>
                            </tr>

                            <tbody>
                                @foreach ($salesFarmasi as $sale)
                                    <tr>
                                        <td></td>

                                        <td class="center">
                                            {{ $fmt($sale['date'] ?? null) }}
                                        </td>

                                        <td class="center">
                                            {{ $sale['transaction_no'] ?? '-' }}
                                        </td>

                                        <td class="right">
                                            {{ $R($sale['summary']['total'] ?? 0) }}
                                        </td>

                                        <td></td>
                                    </tr>
                                @endforeach
                            </tbody>

                            <tr class="subtotal-row">
                                <td></td>

                                <td colspan="2" class="right bold u">
                                    SUB TOTAL
                                </td>

                                <td class="right bold u">
                                    {{ $R($grandTotalFarmasiApi ?? 0) }}
                                </td>

                                <td></td>
                            </tr>
                        </table>
                    @endif

                    {{-- KARCIS JASA --}}
                    <table style="width:100%; margin:10px auto 0 auto;">
                        <tr>
                            <td style="font-style:italic; width:50%;">
                                KARCIS &amp; JASA
                            </td>
                            <td class="right u" style="width:50%;">
                                {{ $R($karcisJasa) }}
                            </td>
                        </tr>
                    </table>

                    <div style="width:100%; margin:2px auto 0 auto; border-top:1px solid #000;"></div>

                    {{-- TOTAL --}}
                    <table style="width:100%; margin:2px auto 0 auto;">
                        <tr>
                            <td style="width:20%;">TOTAL BIAYA</td>
                            <td style="width:20%;" class="right u">
                                {{ $R($grandTotal) }}
                            </td>

                            <td style="width:15%;"></td>

                            <td style="width:25%;">DIJAMIN / DIBAYAR</td>
                            <td style="width:20%;" class="right u">
                                {{ $R($dijamin) }}
                            </td>
                        </tr>

                        <tr>
                            <td>TOTAL DISCOUNT</td>
                            <td class="right u">
                                {{ $R($diskon ?? 0) }}
                            </td>

                            <td></td>

                            <td>SISA BIAYA</td>
                            <td class="right bold">
                                {{ $R($sisa) }}
                            </td>
                        </tr>

                        <tr>
                            <td>JUMLAH UANG</td>
                            <td colspan="4" class="italic">
                                #{{ $terbilangSisa }} RUPIAH#
                            </td>
                        </tr>
                    </table>

                    <div style="width:100%; margin:8px auto 0 auto; border-top:1px solid #000;"></div>

                    {{-- SHIFT DAN KASIR --}}
                    <table style="width:100%; margin:4px auto 0 auto;">
                        <tr>
                            <td style="width:50%; padding-top:35px;">
                                SHIFT
                                <span style="display:inline-block; margin-left:120px;" class="bold u">
                                    {{ $pasien->Shift ?? '-' }}
                                </span>

                                <br>

                                <em style="font-size:10px;">
                                    {{ $tanggalCetak }}
                                </em>
                            </td>

                            <td style="width:50%;" class="center">
                                <div class="bold u">
                                    KASIR
                                </div>

                                <br><br>
                                <br><br>
                                <br><br>

                                <div class="bold u" style="font-size:16px;">
                                    {{ $pasien->Kasir ?? '-' }}
                                </div>
                            </td>
                        </tr>
                    </table>


                </td>
            </tr>
        </tbody>
    </table>

</body>

</html>
