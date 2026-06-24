@php
    use Carbon\Carbon;

    if (!function_exists('rk_td')) {
        function rk_td($v)
        {
            return $v ?? '-';
        }
    }

    $R = fn($v) => number_format($v ?? 0, 0, ',', '.');
    $fmt = fn($v) => $v ? Carbon::parse($v)->format('d-m-Y') : '-';

    $totalKamar = collect($kamar ?? [])->sum('TotalSewa');
    $totalAskep = collect($kamar ?? [])->sum('TotalAskep');
    $totalVisit = collect($rekeningVisit ?? [])->sum('Netto');
    $totalUtilitas = collect($rekeningUtilitas ?? [])->sum('Netto');
    $totalLab = collect($rekeningLaborat ?? [])->sum('Netto');
    $totalRadiologi = collect($rekeningRadiologi ?? [])->sum('Netto');
    $totalLain = collect($lainlain ?? [])->sum('TotalLain');
    $totalOperasi = collect($rekeningOperasi ?? [])->sum('Netto');
    $totalObat = collect($obat ?? [])->sum('HutangObat');

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
        $totalObat +
        ($grandTotalObapayEdit ?? 0);

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
        }

        th,
        td {
            padding: 6px 8px;
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
            margin: 12px 0 6px;
            font-style: italic;
        }

        .line {
            border-top: 1px solid #000;
            margin: 6px 0;
        }

        .u {
            text-decoration: underline;
        }

        .bold {
            font-weight: bold;
        }

        .italic {
            font-style: italic;
        }

        table.karcis {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-top: 10px;
        }

        table.karcis th,
        table.karcis td {
            padding: 6px 8px;
            vertical-align: middle;
        }

        table.totals {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        table.totals td {
            padding: 6px 8px;
            vertical-align: middle;
        }

        .col-lbl {
            width: 20%;
        }

        .col-val {
            width: 10%;
            text-align: right;
        }

        .col-lbl-c {
            width: 40%;
        }

        .col-lbl-r {
            width: 30%;
        }

        .col-val-r {
            width: 20%;
            text-align: right;
        }

        .hr {
            border-top: 1px solid #000;
            margin: 8px 0;
        }

        .muted {
            color: #333;
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <table style="margin-bottom:4px;">
        <tr>
            <td style="width:80px; vertical-align:top; text-align:center; border:0; padding:0;">
                @php
                    $logoSrc =
                        'data:image/png;base64,' .
                        base64_encode(file_get_contents(public_path('vendor/adminlte/dist/img/rsa.png')));
                @endphp

                <img src="{{ $logoSrc }}" alt="RSA" width="68">
            </td>

            <td style="border:0; padding:0; text-align:center; vertical-align:top;">
                <div style="font-weight:700; font-size:14px; letter-spacing:0.5px;">
                    RUMAH SAKIT 'AISYIYAH
                </div>
                <div style="margin-top:2px; font-weight:700; text-decoration:underline; text-transform:uppercase;">
                    JL. KH. ASYHARI 17 &nbsp; BOJONEGORO ,&nbsp; TELP. (0353)885978
                </div>
                <div style="margin-top:6px; font-style:italic; font-weight:700; font-size:14px;">
                    REKENING RAWAT INAP
                </div>
            </td>

            <td style="width:90px; border:0; padding:0;"></td>
        </tr>
    </table>

    <div style="border-top:1px solid #000; margin:4px 0 6px;"></div>

    {{-- IDENTITAS --}}
    <table style="font-size:9px;">
        <tr>
            <td style="width:50%; border:0; padding:0;">
                <table>
                    <tr>
                        <td style="width:95px; border:0; padding:2px 0;">REGISTER</td>
                        <td style="width:10px; border:0; padding:2px 0; text-align:center;">:</td>
                        <td style="border:0; padding:2px 0;">
                            <span class="bold">{{ $pasien->RegNum ?? '-' }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="border:0; padding:2px 0;">NAMA PASIEN</td>
                        <td style="border:0; padding:2px 0; text-align:center;">:</td>
                        <td style="border:0; padding:2px 0;">
                            <span class="bold">{{ $pasien->Nama ?? '-' }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="border:0; padding:2px 0;">ALAMAT</td>
                        <td style="border:0; padding:2px 0; text-align:center;">:</td>
                        <td style="border:0; padding:2px 0;">
                            <span class="bold">{{ $pasien->Addr ?? '-' }}</span>
                        </td>
                    </tr>
                </table>
            </td>

            <td style="width:30%; border:0; padding:0;">
                <table>
                    <tr>
                        <td style="width:95px; border:0; padding:2px 0;">NOMER</td>
                        <td style="width:10px; border:0; padding:2px 0; text-align:center;">:</td>
                        <td style="border:0; padding:2px 0;">
                            <span class="bold">{{ $pasien->ID ?? '-' }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="border:0; padding:2px 0;">L/P</td>
                        <td style="border:0; padding:2px 0; text-align:center;">:</td>
                        <td style="border:0; padding:2px 0;">
                            <span class="bold">{{ $pasien->Jenis_Kelamin ?? '-' }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="border:0; padding:2px 0;">TGL LAHIR</td>
                        <td style="border:0; padding:2px 0; text-align:center;">:</td>
                        <td style="border:0; padding:2px 0;">
                            <span class="bold">{{ $fmt($pasien->Tanggal_Lahir ?? null) }}</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div style="border-top:1px solid #000; margin:10px 0 6px;"></div>

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
                    <th class="right">BIAYA SEWA</th>
                    <th class="right">DISCOUNT</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($kamar as $k)
                    <tr>
                        <td>{{ $k->NamaKamar ?? '-' }}</td>
                        <td class="center">{{ $fmt($k->TMasuk ?? null) }}</td>
                        <td class="center">{{ $fmt($k->TKeluar ?? null) }}</td>
                        <td class="center">{{ $k->LamaRawat ?? 0 }}</td>
                        <td class="right">{{ $R($k->TotalSewa ?? 0) }}</td>
                        <td class="right">{{ $R($k->TotalDisc ?? 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <strong>
            <div class="right">
                SUB TOTAL: {{ $R($totalKamar) }} &nbsp;&nbsp;
                DISCOUNT: {{ $R(collect($kamar)->sum('TotalDisc')) }}
            </div>
        </strong>
    @endif

    {{-- VISIT --}}
    @if (count($rekeningVisit ?? []))
        <div class="section-title">VISIT / KUNJUNGAN DOKTER</div>
        <table class="nb">
            <thead>
                <tr>
                    <th>DOKTER</th>
                    <th class="right">BANYAK</th>
                    <th class="right">BIAYA</th>
                    <th class="right">DISCOUNT</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rekeningVisit as $v)
                    <tr>
                        <td>{{ $v->Dokter ?? '-' }}</td>
                        <td class="right">{{ $v->NTimes ?? 0 }}</td>
                        <td class="right">{{ $R($v->Netto ?? 0) }}</td>
                        <td class="right">{{ $R($v->Discount ?? 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <strong>
            <div class="right">
                SUB TOTAL: {{ $R($totalVisit) }} &nbsp;&nbsp;
                DISCOUNT: {{ $R(collect($rekeningVisit)->sum('Discount')) }}
            </div>
        </strong>
    @endif

    {{-- UTILITAS --}}
    @if (count($rekeningUtilitas ?? []))
        <div class="section-title">UTILITAS</div>
        <table class="nb">
            <thead>
                <tr>
                    <th>UTILITAS</th>
                    <th class="right">BANYAK</th>
                    <th class="right">BIAYA</th>
                    <th class="right">DISCOUNT</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rekeningUtilitas as $u)
                    <tr>
                        <td>{{ $u->Tindak ?? '-' }}</td>
                        <td class="right">{{ $u->NTimes ?? 0 }}</td>
                        <td class="right">{{ $R($u->Netto ?? 0) }}</td>
                        <td class="right">{{ $R($u->Discount ?? 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <strong>
            <div class="right">
                SUB TOTAL: {{ $R($totalUtilitas) }}
            </div>
        </strong>
    @endif

    {{-- LAB --}}
    @if (count($rekeningLaborat ?? []))
        <div class="section-title">REKENING JASA PENUNJANG MEDIK</div>
        <div class="section-title">LABORAT</div>
        <table class="nb">
            <thead>
                <tr>
                    <th>TANGGAL</th>
                    <th class="right">BIAYA/BEBAN</th>
                    <th class="right">DISCOUNT</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rekeningLaborat as $l)
                    <tr>
                        <td class="center">{{ $fmt($l->TLab ?? null) }}</td>
                        <td class="right">{{ $R($l->Netto ?? 0) }}</td>
                        <td class="right">{{ $R($l->Discount ?? 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <strong>
            <div class="right">
                SUB TOTAL: {{ $R($totalLab) }} &nbsp;&nbsp;
                DISCOUNT: {{ $R(collect($rekeningLaborat)->sum('Discount')) }}
            </div>
        </strong>
    @endif

    {{-- RADIOLOGI --}}
    @if (count($rekeningRadiologi ?? []))
        <div class="section-title">RADIOLOGI</div>
        <table class="nb">
            <thead>
                <tr>
                    <th>TANGGAL</th>
                    <th class="right">BIAYA/BEBAN</th>
                    <th class="right">DISCOUNT</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rekeningRadiologi as $r)
                    <tr>
                        <td class="center">{{ $fmt($r->TRad ?? null) }}</td>
                        <td class="right">{{ $R($r->Netto ?? 0) }}</td>
                        <td class="right">{{ $R($r->Discount ?? 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <strong>
            <div class="right">
                SUB TOTAL: {{ $R($totalRadiologi) }} &nbsp;&nbsp;
                DISCOUNT: {{ $R(collect($rekeningRadiologi)->sum('Discount')) }}
            </div>
        </strong>
    @endif

    {{-- LAIN-LAIN --}}
    @if (count($lainlain ?? []))
        <div class="section-title">LAIN-LAIN</div>
        <table class="nb">
            <thead>
                <tr>
                    <th>URAIAN</th>
                    <th class="right">BIAYA</th>
                    <th class="right">DISCOUNT</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lainlain as $l)
                    <tr>
                        <td>{{ $l->Lain ?? '-' }}</td>
                        <td class="right">{{ $R($l->TotalLain ?? 0) }}</td>
                        <td class="right">{{ $R($l->TotalDisc ?? 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <strong>
            <div class="right">
                SUB TOTAL: {{ $R($totalLain) }} &nbsp;&nbsp;
                DISCOUNT: {{ $R(collect($lainlain)->sum('TotalDisc')) }}
            </div>
        </strong>
    @endif

    {{-- OPERASI --}}
    @if (count($rekeningOperasi ?? []))
        <div class="section-title">TINDAKAN</div>
        <table class="nb">
            <thead>
                <tr>
                    <th>TINDAKAN/OPERASI</th>
                    <th class="right">BANYAK</th>
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
        </table>
        <strong>
            <div class="right">
                SUB TOTAL: {{ $R($totalOperasi) }} &nbsp;&nbsp;
                DISCOUNT: {{ $R(collect($rekeningOperasi)->sum('Pot')) }}
            </div>
        </strong>
    @endif

    {{-- OBAT --}}
    @if (count($obat ?? []))
        <div class="section-title">BIAYA OBAT</div>
        <table class="nb">
            <thead>
                <tr>
                    <th></th>
                    <th>TANGGAL</th>
                    <th class="right">HUTANG OBAT</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($obat as $o)
                    <tr>
                        <td></td>
                        <td class="center">{{ $fmt($o->{'Invoice date'} ?? null) }}</td>
                        <td class="right">{{ $R($o->HutangObat ?? 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <strong>
            <div class="right">SUB TOTAL: {{ $R($totalObat) }}</div>
        </strong>
    @endif

    {{-- OBAPAY EDIT --}}
    @if (($obapayEditPrint ?? collect())->count())
        <div class="section-title">BIAYA OBAPAY</div>
        <table class="nb">
            <thead>
                <tr>
                    <th>TANGGAL</th>
                    <th class="left">ID TRANSAKSI</th>
                    <th class="right">BIAYA OBAPAY</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($obapayEditPrint as $sale)
                    <tr>
                        <td class="center">{{ $fmt($sale->Tanggal ?? null) }}</td>
                        <td>{{ $sale->SaleID ?? '-' }}</td>
                        <td class="right">{{ $R($sale->Total ?? 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <strong>
            <div class="right">
                SUB TOTAL: {{ $R($grandTotalObapayEdit ?? 0) }}
            </div>
        </strong>
    @endif

    {{-- RINGKASAN TOTAL --}}
    <table class="karcis">
        <thead>
            <tr>
                <th colspan="3" class="left">KARCIS &amp; JASA</th>
                <th></th>
                <th class="right">{{ $R($karcisJasa) }}</th>
            </tr>
        </thead>
    </table>

    <div style="border-top:1px solid #000; margin:4px 0 6px;"></div>

    <table class="totals">
        <tbody>
            <tr>
                <td class="col-lbl">TOTAL BIAYA</td>
                <td class="col-val u">{{ $R($grandTotal) }}</td>
                <td class="col-lbl-c"></td>
                <td class="col-lbl-r">DIJAMIN / DIBAYAR</td>
                <td class="col-val-r u">{{ $R($dijamin) }}</td>
            </tr>
            <tr>
                <td class="col-lbl">TOTAL DISCOUNT</td>
                <td class="col-val u">{{ $R($diskon) }}</td>
                <td class="col-lbl-c"></td>
                <td class="col-lbl-r">SISA BIAYA</td>
                <td class="col-val-r u"><strong>{{ $R($sisa) }}</strong></td>
            </tr>
            <tr>
                <td class="col-lbl" style="vertical-align:top;">JUMLAH UANG</td>
                <td class="col-val" colspan="4" style="text-align:left; padding-left:8px;">
                    #{{ mb_strtoupper($terbilangSisa ?? '') }} RUPIAH#
                </td>
            </tr>
            <tr>
                <td colspan="5" style="padding-top:6px; padding-bottom:6px;">
                    <div class="hr"></div>
                </td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="u" style="text-align:center;">KASIR</td>
            </tr>
        </tbody>
    </table>

    <table style="width:100%; border-collapse:collapse; margin-top:6px;">
        <tr>
            <td style="border:0; width:20%;">SHIFT</td>
            <td style="border:0; width:15%; text-align:center; text-decoration:underline;">
                {{ $pasien->Shift ?? '-' }}
            </td>
            <td style="border:0; width:45%;"></td>
            <td style="border:0; width:20%; text-align:right; text-decoration:underline;"></td>
        </tr>
        <tr>
            <td colspan="2" style="border:0; padding-top:4px;">
                <div class="muted" style="font-style:italic;">
                    {{ $tanggalCetak ?? now()->format('d-m-Y H:i:s') }}
                </div>
            </td>
            <td style="border:0;"></td>
            <td style="border:0; text-align:right; font-weight:bold; text-decoration:underline;">
                {{ $pasien->Kasir ?? '-' }}
            </td>
        </tr>
    </table>

</body>

</html>
