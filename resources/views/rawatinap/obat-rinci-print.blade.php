<!DOCTYPE html>
<html>

<head>
    <title>Obat Rinci</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #000;
        }

        .title {
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .subtitle {
            text-align: center;
            font-size: 10px;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .info td {
            padding: 2px 4px;
        }

        .main th {
            border-top: 2px solid #000;
            border-bottom: 1px solid #000;
            padding: 4px;
            font-size: 10px;
        }

        .main td {
            padding: 3px 4px;
            font-size: 10px;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .line-top {
            border-top: 1px solid #000;
        }

        .line-bottom {
            border-bottom: 1px solid #000;
        }

        @media print {
            body {
                margin: 10mm;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="title">RUMAH SAKIT 'AISYIYAH</div>
    <div class="subtitle">JL. KH. ASYHARI 17 BOJONEGORO</div>

    @if ($pasien)
        <table class="info">
            <tr>
                <td width="15%">REGISTRASI</td>
                <td width="30%">: {{ $pasien->IDReg ?? '-' }}</td>

                <td width="15%">TANGGAL LAHIR</td>
                <td>: {{ !empty($pasien->Tanggal_Lahir) ? date('d/m/Y', strtotime($pasien->Tanggal_Lahir)) : '-' }}</td>
            </tr>

            <tr>
                <td>REKAM MEDIK</td>
                <td>: {{ $pasien->RegNum ?? '-' }}</td>

                <td>ALAMAT</td>
                <td>: {{ $pasien->Addr ?? '-' }}</td>
            </tr>

            <tr>
                <td>PASIEN</td>
                <td>: {{ $pasien->NamaPx ?? '-' }}</td>

                <td>KELURAHAN</td>
                <td>
                    : {{ $pasien->Kelurahan ?? '-' }}
                    &nbsp;&nbsp;&nbsp; Telp &nbsp; {{ $pasien->Telepon ?? '-' }}
                </td>
            </tr>
        </table>
    @endif

    <br>

    <table class="main">
        <thead>
            <tr>
                <th>RUANG</th>
                <th>TANGGAL</th>
                <th>KODE OBAT</th>
                <th>NAMA OBAT</th>
                <th class="right">BANYAKNYA</th>
                <th class="right">DISCOUNT</th>
                <th class="right">HARGA</th>
                <th class="right">JUMLAH</th>
                <th>INVOICE ID</th>
            </tr>
        </thead>

        <tbody>
            @php
                $grandTotal = 0;
                $groupTanggal = collect($obatRinci)->groupBy(function ($item) {
                    return date('Y-m-d', strtotime($item->InvoiceDate));
                });
            @endphp

            @forelse($groupTanggal as $tanggal => $items)

                @php
                    $subTotalTanggal = 0;
                @endphp

                @foreach ($items as $index => $o)
                    @php
                        $subTotalTanggal += $o->TotalLine ?? 0;
                        $grandTotal += $o->TotalLine ?? 0;
                    @endphp

                    <tr>

                        <td>
                            {{ $index == 0 ? $o->RoomName ?? '-' : '' }}
                        </td>

                        <td class="center">
                            {{ $index == 0 ? date('d/m/Y', strtotime($o->InvoiceDate)) : '' }}
                        </td>

                        <td>{{ $o->Kode ?? '-' }}</td>

                        <td>{{ $o->ProductName ?? '-' }}</td>

                        <td class="right">
                            {{ number_format($o->Qty ?? 0, 0, ',', '.') }}
                        </td>

                        <td class="right">
                            {{ number_format($o->Discount ?? 0, 2, ',', '.') }}%
                        </td>

                        <td class="right">
                            {{ number_format($o->Price ?? 0, 0, ',', '.') }}
                        </td>

                        <td class="right">
                            {{ number_format($o->TotalLine ?? 0, 0, ',', '.') }}
                        </td>

                        <td class="center">
                            {{ $o->ID ?? '-' }}
                        </td>
                @endforeach

                <tr class="bold line-top">
                    <td colspan="7" class="right">
                        SUB TOTAL PER TGL {{ date('d/m/Y', strtotime($tanggal)) }}
                    </td>

                    <td class="right">
                        {{ number_format($subTotalTanggal, 0, ',', '.') }}
                    </td>

                    <td></td>
                </tr>

            @empty
                <tr>
                    <td colspan="9" class="center">
                        Data obat rinci tidak tersedia.
                    </td>
                </tr>
            @endforelse
        </tbody>

        <tfoot>
            @if (count($obatRinci))
                <tr class="bold line-top">
                    <td colspan="7" class="right">
                        SUB TOTAL PER RUANG {{ $pasien->RoomName ?? '' }}
                    </td>

                    <td class="right">
                        {{ number_format($grandTotal, 0, ',', '.') }}
                    </td>

                    <td></td>
                </tr>

                <tr class="bold line-top line-bottom">
                    <td colspan="7" class="right">
                        T O T A L
                    </td>

                    <td class="right">
                        {{ number_format($grandTotal, 0, ',', '.') }}
                    </td>

                    <td></td>
                </tr>
            @endif
        </tfoot>
    </table>

</body>

</html>
