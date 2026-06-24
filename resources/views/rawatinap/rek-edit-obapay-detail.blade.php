@php
    use Carbon\Carbon;

    $fmt = fn($v) => $v ? Carbon::parse($v)->format('d/m/Y') : '-';
    $rp = fn($n) => number_format((float) $n, 0, ',', '.');

    if (!function_exists('ob_td')) {
        function ob_td($v)
        {
            $s = html_entity_decode((string) ($v ?? '-'), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            return trim($s) === '' ? '-' : $s;
        }
    }

    $grand = 0;
@endphp

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">

    <style>
        @page obat {
            size: A4 landscape;
            margin: 10px 10px;
        }

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
            padding: 3px 4px;
            vertical-align: top;
            word-wrap: break-word;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        img.logo {
            width: 78px;
            height: auto;
            display: block;
        }

        .tight th,
        .tight td {
            padding: 2px 4px;
            vertical-align: top;
        }

        thead th.head {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }

        tfoot td {
            border-top: 1px solid #000;
        }

        .identitas {
            table-layout: auto;
        }

        .identitas td {
            padding: 2px 4px;
            line-height: 1.25;
            white-space: normal;
        }

        .identitas strong {
            font-weight: 700;
        }

        .grid,
        .bridge {
            table-layout: fixed;
        }
    </style>
</head>

<body>

    <table style="margin-bottom:6px;">
        <tr>
            <td style="width:75px; vertical-align:top; text-align:center; border:0; padding:0;">
                @php
                    $logoSrc =
                        'data:image/png;base64,' .
                        base64_encode(file_get_contents(public_path('vendor/adminlte/dist/img/rsa.png')));
                @endphp

                <img src="{{ $logoSrc }}" alt="RSA" width="65">
            </td>

            <td style="padding:0; text-align:center;">
                <div style="font-weight:700; font-size:14px;">RUMAH SAKIT 'AISYIYAH</div>
                <div style="margin-top:2px; font-weight:700; text-transform:uppercase; text-decoration:underline;">
                    JL. KH. ASYHARI 17 BOJONEGORO
                </div>
                <div style="margin-top:6px; font-style:italic; font-weight:700; font-size:14px;">
                    RINCIAN OBAPAY
                </div>
            </td>

            <td style="width:90px;"></td>
        </tr>
    </table>

    <div style="border-top:1px solid #000; margin:4px 0 6px;"></div>

    <table class="tight identitas">
        <tr>
            <td style="width:130px;">REGISTRASI</td>
            <td style="width:8px;" class="center">:</td>
            <td style="width:220px;"><strong>{{ $pasien->ID ?? (request('id') ?? '-') }}</strong></td>

            <td style="width:130px;">TANGGAL LAHIR</td>
            <td style="width:8px;" class="center">:</td>
            <td><strong>{{ $fmt($pasien->Tanggal_Lahir ?? null) }}</strong></td>
        </tr>

        <tr>
            <td>REKAM MEDIK</td>
            <td class="center">:</td>
            <td><strong>{{ ob_td($pasien->RegNum ?? '-') }}</strong></td>

            <td>ALAMAT</td>
            <td class="center">:</td>
            <td><strong>{{ ob_td($pasien->Addr ?? '-') }}</strong></td>
        </tr>

        <tr>
            <td>PASIEN</td>
            <td class="center">:</td>
            <td><strong>{{ ob_td($pasien->Nama ?? '-') }}</strong></td>

            <td>TELP</td>
            <td class="center">:</td>
            <td><strong>{{ ob_td($pasien->Telepon ?? '-') }}</strong></td>
        </tr>
    </table>

    <table class="tight grid">
        <thead>
            <tr>
                <th class="head" style="width:75px">TANGGAL</th>
                <th class="head center" style="width:115px">INVOICE ID</th>
                <th class="head" style="width:90px">KODE OBAT</th>
                <th class="head" style="width:330px">NAMA OBAT</th>
                <th class="head center" style="width:70px">BANYAKNYA</th>
                <th class="head center" style="width:70px">DISCOUNT</th>
                <th class="head right" style="width:90px">HARGA</th>
                <th class="head right" style="width:95px">JUMLAH</th>
            </tr>
        </thead>
    </table>

    <table class="tight bridge" style="margin-top:-1px;">
        <thead style="visibility:hidden;height:0;">
            <tr>
                <th style="width:75px"></th>
                <th style="width:115px"></th>
                <th style="width:90px"></th>
                <th style="width:330px"></th>
                <th style="width:70px"></th>
                <th style="width:70px"></th>
                <th style="width:90px"></th>
                <th style="width:95px"></th>
            </tr>
        </thead>

        <tbody>
            @foreach ($obapayEditFull ?? [] as $sale)
                @php
                    $subInvoice = 0;
                @endphp

                @foreach ($sale->items ?? [] as $idx => $item)
                    @php
                        $lineTotal = (float) ($item->TotalEdit ?? ($item->Total ?? 0));
                        $subInvoice += $lineTotal;
                        $grand += $lineTotal;
                    @endphp

                    <tr>
                        <td>{{ $idx == 0 ? $fmt($sale->Tanggal ?? null) : '' }}</td>
                        <td class="center">{{ $idx == 0 ? ob_td($sale->SaleID ?? '-') : '' }}</td>
                        <td>{{ ob_td($item->Code ?? '') }}</td>
                        <td>{{ ob_td($item->NamaItem ?? '') }}</td>
                        <td class="center">{{ number_format((float) ($item->Qty ?? 0), 0, ',', '.') }}</td>
                        <td class="center">0</td>
                        <td class="right">{{ $rp($item->Harga ?? 0) }}</td>
                        <td class="right">{{ $rp($lineTotal) }}</td>
                    </tr>
                @endforeach

                <tr>
                    <td colspan="7" class="right" style="font-weight:700; border-top:1px solid #000;">
                        SUB TOTAL INVOICE {{ ob_td($sale->SaleID ?? '-') }}
                    </td>
                    <td class="right" style="font-weight:700; border-top:1px solid #000;">
                        {{ $rp($sale->Total ?? $subInvoice) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="tight">
        <tr>
            <td class="right" style="width:85%; font-weight:700;">T O T A L</td>
            <td class="right" style="width:15%; font-weight:700;">
                {{ $rp($grandTotalObapayEdit ?? $grand) }}
            </td>
        </tr>
    </table>

</body>

</html>
