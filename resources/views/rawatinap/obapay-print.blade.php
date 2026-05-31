<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>ObaPay Detail</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #000;
        }

        .title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
        }

        .subtitle {
            text-align: center;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 4px;
            text-align: left;
        }

        td {
            padding: 3px;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .subtotal {
            font-weight: bold;
            border-top: 1px solid #000;
        }

        .grandtotal {
            font-weight: bold;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
        }
    </style>
</head>

<body onload="window.print()">

    <div class="title">
        RUMAH SAKIT 'AISYIYAH
    </div>

    <div class="subtitle">
        JL. KH. HASYIM ASY'ARI 17 BOJONEGORO
    </div>

    <table>
        <thead>
            <tr>
                <th>TANGGAL</th>
                <th>NO TRANSAKSI</th>
                <th>NAMA OBAT</th>
                <th>QTY</th>
                <th>SATUAN</th>
                <th>HARGA</th>
                <th>SUBTOTAL</th>
            </tr>
        </thead>

        <tbody>

            @foreach ($salesFarmasi as $sale)
                @foreach ($sale['items'] ?? [] as $index => $item)
                    <tr>
                        <td>
                            {{ $index == 0 && !empty($sale['date']) ? date('d/m/Y', strtotime($sale['date'])) : '' }}
                        </td>

                        <td>
                            {{ $index == 0 ? $sale['transaction_no'] ?? '-' : '' }}
                        </td>

                        <td>
                            {{ $item['name'] ?? '-' }}
                        </td>

                        <td class="center">
                            {{ $item['qty'] ?? 0 }}
                        </td>

                        <td>
                            {{ $item['unit'] ?? '-' }}
                        </td>

                        <td class="right">
                            {{ number_format($item['unit_price'] ?? 0, 0, ',', '.') }}
                        </td>

                        <td class="right">
                            {{ number_format($item['subtotal'] ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach

                <tr class="subtotal">
                    <td colspan="6" class="right">
                        SUB TOTAL {{ $sale['transaction_no'] ?? '-' }}
                    </td>

                    <td class="right">
                        {{ number_format($sale['summary']['total'] ?? 0, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach

        </tbody>

        <tfoot>
            <tr class="grandtotal">
                <td colspan="6" class="right">
                    TOTAL OBAPAY
                </td>

                <td class="right">
                    {{ number_format($grandTotalFarmasiApi ?? 0, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>

</body>

</html>
