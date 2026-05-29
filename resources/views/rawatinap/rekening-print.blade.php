@php
    use Carbon\Carbon;

    $R = fn($v) => number_format($v ?? 0, 0, ',', '.');
    $fmt = fn($v) => $v ? Carbon::parse($v)->format('d/m/Y') : '-';

    $totalKamar = collect($kamar)->sum('TotalSewa');
    $totalAskep = collect($kamar)->sum('TotalAskep');
    $totalVisit = collect($rekeningVisit)->sum('Netto');
    $totalUtilitas = collect($rekeningUtilitas)->sum('Netto');
    $totalLab = collect($rekeningLaborat ?? [])->sum('Netto');
    $totalRadiologi = collect($rekeningRadiologi ?? [])->sum('Netto');
    $totalLain = collect($lainlain)->sum('TotalLain');
    $totalOperasi = collect($rekeningOperasi ?? [])->sum('Netto');
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
        $totalObat;

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
            size: A4 portrait;
            margin: 10mm;
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
    </style>
</head>

<body>

    <button onclick="window.print()" class="print-btn">Print</button>

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

    {{-- KAMAR --}}
    @if (count($kamar ?? []))
        <div class="section-title">REKENING PERAWATAN RUANGAN</div>
        <table class="nb">
            <thead>
                <tr>
                    <th>KELAS/RUANG</th>
                    <th>MASUK</th>
                    <th>KELUAR</th>
                    <th class="right">LAMA(HR)</th>
                    <th class="right">BIAYA/SEWA</th>
                    <th class="right">DISCOUNT</th>
                </tr>
            </thead>
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
            <tfoot>
                <tr>
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
            </tfoot>
        </table>
    @endif

    {{-- VISIT --}}
    @if (count($rekeningVisit ?? []))
        <div class="section-title">VISIT / KUNJUNGAN DOKTER</div>
        <table class="nb">
            <thead>
                <tr>
                    <th>DOKTER</th>
                    <th class="right">JUMLAH</th>
                    <th class="right">BIAYA</th>
                    <th class="right">DISCOUNT</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rekeningVisit as $v)
                    <tr>
                        <td>{{ $v->Dokter ?? '-' }}</td>
                        <td class="right">{{ $R($v->NTimes ?? 0) }}</td>
                        <td class="right">{{ $R($v->Netto ?? 0) }}</td>
                        <td class="right">{{ $R(($v->Discount ?? 0) * ($v->Pot ?? 0)) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td></td>

                    <td class="right bold u">
                        SUB TOTAL
                    </td>

                    <td class="right bold u">
                        {{ $R($totalVisit) }}
                    </td>

                    <td class="right bold u">
                        {{ $R(collect($rekeningVisit)->sum('Discount')) }}
                    </td>
                </tr>
            </tfoot>
        </table>

    @endif

    {{-- UTILITAS --}}
    @if (count($rekeningUtilitas ?? []))
        <div class="section-title">UTILITAS</div>
        <table class="nb">
            <thead>
                <tr>
                    <th>UTILITAS</th>
                    <th class="right">JUMLAH</th>
                    <th class="right">BIAYA</th>
                    <th class="right">DISCOUNT</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rekeningUtilitas as $u)
                    <tr>
                        <td>{{ $u->Tindak ?? '-' }}</td>
                        <td class="right">{{ $R($u->NTimes ?? 0) }}</td>
                        <td class="right">{{ $R($u->Netto ?? 0) }}</td>
                        <td class="right">{{ $R(($v->Discount ?? 0) * ($v->Pot ?? 0)) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2"></td>

                    <td class="right bold u">
                        SUB TOTAL
                    </td>

                    <td class="right bold u">
                        {{ $R($totalUtilitas) }}
                    </td>
                </tr>
            </tfoot>
        </table>

    @endif

    {{-- PENUNJANG --}}
    @if ($totalLab > 0 || $totalRadiologi > 0)
        <div class="section-title">REKENING JASA PENUNJANG MEDIK</div>
    @endif

    {{-- LAB --}}
    @if (count($rekeningLaborat ?? []))
        <div class="section-title">- LABORAT</div>

        <table class="nb">
            <thead>
                <tr>
                    <th>TANGGAL</th>
                    <th class="right">BIAYA</th>
                    <th class="right">DISCOUNT</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($rekeningLaborat as $l)
                    <tr>
                        <td class="center">
                            {{ $fmt($l->TLab ?? null) }}
                        </td>

                        <td class="right">
                            {{ $R($l->Netto ?? 0) }}
                        </td>

                        <td class="right">
                            {{ $R($l->Discount ?? 0) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr>
                    <td class="right bold u">
                        SUB TOTAL
                    </td>

                    <td class="right bold u">
                        {{ $R($totalLab) }}
                    </td>

                    <td class="right bold u">
                        {{ $R(collect($rekeningLaborat)->sum('Discount')) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    @endif

    {{-- RADIOLOGI --}}
    @if (count($rekeningRadiologi ?? []))
        <div class="section-title">- RADIOLOGI</div>

        <table class="nb">
            <thead>
                <tr>
                    <th>TANGGAL</th>
                    <th class="right">BIAYA</th>
                    <th class="right">DISCOUNT</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($rekeningRadiologi as $r)
                    <tr>
                        <td class="center">
                            {{ $fmt($r->TRad ?? null) }}
                        </td>

                        <td class="right">
                            {{ $R($r->Netto ?? 0) }}
                        </td>

                        <td class="right">
                            {{ $R($r->Discount ?? 0) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr>

                    <td class="right bold u">
                        SUB TOTAL
                    </td>

                    <td class="right bold u">
                        {{ $R($totalRadiologi) }}
                    </td>

                    <td class="right bold u">
                        {{ $R(collect($rekeningRadiologi)->sum('Discount')) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    @endif

    {{-- LAIN-LAIN --}}
    @if (count($lainlain ?? []))
        <div class="section-title">LAIN-LAIN</div>
        <table class="nb">
            <thead>
                <tr>
                    <th>KETERANGAN</th>
                    <th>TANGGAL</th>
                    <th class="right">BIAYA</th>
                    <th class="right">DISCOUNT</th>
                </tr>
            </thead>
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
            <tfoot>
                <tr>
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
            </tfoot>
        </table>

    @endif

    {{-- OPERASI --}}
    @if (count($rekeningOperasi ?? []))
        <div class="section-title">TINDAKAN / OPERASI</div>

        <table class="nb">
            <thead>
                <tr>
                    <th>TINDAKAN</th>
                    <th class="right">JUMLAH</th>
                    <th class="right">BIAYA</th>
                    <th class="right">DISCOUNT</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($rekeningOperasi as $o)
                    <tr>
                        <td>{{ $o->Nama_jenis ?? '-' }}</td>
                        <td class="right">{{ $o->c ?? 0 }}</td>
                        <td class="right">{{ $R($o->Netto ?? 0) }}</td>
                        <td class="right">{{ $R($o->Pot ?? 0) }}</td>
                    </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr>
                    <td></td>

                    <td class="right bold u">
                        SUB TOTAL
                    </td>

                    <td class="right bold u">
                        {{ $R($totalOperasi) }}
                    </td>

                    <td class="right bold u">
                        {{ $R(collect($rekeningOperasi)->sum('Pot')) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    @endif

    {{-- OBAT --}}
    @if (count($obat ?? []))
        <div class="section-title">OBAT</div>

        <table class="nb">
            <thead>
                <tr>
                    <th style="width:35%;"></th>

                    <th style="width:20%;" class="center">
                        TANGGAL
                    </th>

                    <th style="width:20%;" class="right">
                        BIAYA
                    </th>

                    <th style="width:25%;"></th>
                </tr>
            </thead>

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

            <tfoot>
                <tr>
                    <td></td>

                    <td class="right bold u">
                        SUB TOTAL
                    </td>

                    <td class="right bold u">
                        {{ $R($totalObat) }}
                    </td>

                    <td></td>
                </tr>
            </tfoot>
        </table>
    @endif

    {{-- KARCIS JASA --}}
    <table class="nb" style="margin-top:10px;">
        <thead>
            <tr>
                <th class="left">KARCIS &amp; JASA</th>
                <th class="right">{{ $R($karcisJasa) }}</th>
            </tr>
        </thead>
    </table>

    <div class="line"></div>

    {{-- TOTAL --}}
    <table class="totals">
        <tr>
            <td style="width:20%;">TOTAL BIAYA</td>
            <td style="width:20%;" class="right u">{{ $R($grandTotal) }}</td>
            <td style="width:20%;"></td>
            <td style="width:20%;">DIJAMIN / DIBAYAR</td>
            <td style="width:20%;" class="right u">{{ $R($dijamin) }}</td>
        </tr>
        <tr>
            <td>TOTAL DISCOUNT</td>
            <td class="right u">{{ $R($diskon) }}</td>
            <td></td>
            <td>SISA BIAYA</td>
            <td class="right u bold">{{ $R($sisa) }}</td>
        </tr>
    </table>

    <br>

    <table>
        <tr>
            <td style="width:70%;"></td>
            <td class="center">
                KASIR<br><br><br><br>
                ______________________
            </td>
        </tr>
    </table>



</body>

</html>
