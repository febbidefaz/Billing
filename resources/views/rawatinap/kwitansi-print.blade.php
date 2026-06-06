<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Kwitansi</title>

    <style>
        body {
            font-family: Tahoma, Arial, sans-serif;
            font-size: 10px;
            color: #000;
        }

        .kwitansi {
            width: 96%;
            margin: 80px auto 0 auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 1px 4px;
            vertical-align: top;
            line-height: 12px;
        }

        .box {
            border: 1px solid #000;
        }

        .box td {
            padding: 2px 4px;
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

        .u {
            text-decoration: underline;
        }

        .italic {
            font-style: italic;
        }

        .nominal {
            text-align: right;
            font-weight: bold;
            white-space: nowrap;
        }

        .ttd-kasir {
            padding-top: 25px;
            text-align: center;
        }

        .small {
            font-size: 7px;
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
            .print-btn {
                display: none !important;
            }
        }
    </style>
</head>

<body>

    <button onclick="window.print()" class="print-btn">Print</button>

    @php
        $R = fn($v) => number_format($v ?? 0, 0, ',', '.');
    @endphp

    <br><br><br><br><br>

    <div class="kwitansi">

        <table class="box">
            <tr>
                <td width="15%" class="bold">
                    KWITANSI NO.
                </td>

                <td width="30%">
                    : <span class="bold u">{{ $pasien->ID }}</span>
                </td>

                <td width="12%">
                    NAMA PX
                </td>

                <td>
                    : {{ $pasien->Nama }}
                </td>
            </tr>

            <tr>
                <td>TGL. PULANG</td>
                <td>: {{ date('d/m/Y', strtotime($pasien->TglByr ?? now())) }}</td>

                <td>ALAMAT</td>
                <td>: {{ $pasien->Addr }}</td>
            </tr>

            <tr>
                <td>RUANG</td>
                <td>: {{ $pasien->RoomName }}</td>

                <td>NO. RM</td>
                <td>: {{ $pasien->RegNum }}</td>
            </tr>
        </table>

        <table class="box" style="margin-top:4px;">
            <tr>
                <td colspan="4">
                    <span class="italic" style="font-size:10px;">
                        Sudah terima dari
                    </span>

                    &nbsp;&nbsp;

                    @if (!empty(optional($kasir)->payBy))
                        <span class="bold" style="font-size:10px;">
                            Bapak/Ibu {{ $kasir->payBy }}
                        </span>
                    @endif
                </td>
            </tr>

            <tr>
                <td style="width:15%;">Jumlah Uang</td>
                <td colspan="3" class="italic u center">
                    # {{ $terbilangSisa }} RUPIAH #
                </td>
            </tr>

            <tr>
                <td width="15%">
                    Buat Pembayaran
                </td>

                <td width="42%">

                    <table width="100%">
                        <tr>
                            <td>- KARCIS & JASA PERIKSA</td>
                            <td class="nominal">{{ $R($karcisJasa) }}</td>
                        </tr>

                        <tr>
                            <td>- VISITE & RUANGAN</td>
                            <td class="nominal">{{ $R($totalVisitRuang) }}</td>
                        </tr>

                        <tr>
                            <td>- UTILITAS</td>
                            <td class="nominal">{{ $R($totalUtilitas) }}</td>
                        </tr>

                        <tr>
                            <td>- BIAYA OBAT</td>
                            <td class="nominal">{{ $R($totalObat) }}</td>
                        </tr>
                    </table>

                </td>

                <td width="3%"></td>

                <td width="40%">

                    <table width="100%">
                        <tr>
                            <td>- LABORAT</td>
                            <td class="nominal">{{ $R($totalLab) }}</td>
                        </tr>

                        <tr>
                            <td>- RADIOLOGI</td>
                            <td class="nominal">{{ $R($totalRadiologi) }}</td>
                        </tr>

                        <tr>
                            <td>- TINDAKAN</td>
                            <td class="nominal">{{ $R($totalOperasi) }}</td>
                        </tr>

                        <tr>
                            <td>- LAIN-LAIN</td>
                            <td class="nominal">{{ $R($totalLain) }}</td>
                        </tr>
                    </table>

                </td>
            </tr>
        </table>

        <table class="box" style="margin-top:3px;">
            <tr>
                <td width="20%">GRAND TOTAL</td>
                <td width="20%" class="right bold">
                    {{ $R($grandTotal) }}
                </td>

                <td width="25%"></td>

                <td width="35%" class="center">
                    KASIR,
                </td>
            </tr>

            <tr>
                <td>DIJAMIN / DIBAYAR</td>

                <td class="right" style="border-bottom:1px solid #000;">
                    {{ $R($dijamin) }}
                </td>

                <td></td>

                <td class="center">
                    {{ $tanggalCetak }}
                </td>
            </tr>

            <tr>
                <td>TERBILANG</td>
                <td class="right bold">
                    {{ $R($sisa) }}
                </td>

                <td></td>
                <td></td>
            </tr>

            <tr>
                <td colspan="3"></td>

                <td class="ttd-kasir">
                    <span class="u">{{ $pasien->Kasir }}</span>
                </td>
            </tr>

            <tr>
                <td>SHIFT</td>

                <td class="center bold">
                    {{ $pasien->Shift }}
                </td>

                <td colspan="2"></td>
            </tr>
        </table>

    </div>

</body>

</html>
