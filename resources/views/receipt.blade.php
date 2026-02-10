<!DOCTYPE html>
<html>

<head>
    <title>Struk Pembayaran</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
        }

        .container {
            width: 100%;
            max-width: 300px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        .item {
            display: flex;
            justify-content: space-between;
        }

        .total {
            font-weight: bold;
            margin-top: 10px;
            text-align: right;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h3>RESTO POS ENAK</h3>
            <p>Jl. Koding No. 1, Jakarta<br>Telp: 0812-3456-7890</p>
        </div>

        <div class="divider"></div>

        <p>
            Order ID: #{{ $order->id }}<br>
            Meja: {{ $order->table_id }}<br>
            Tanggal: {{ $order->created_at->format('d/m/Y H:i') }}<br>
            Kasir: {{ $order->user->name ?? 'Admin' }}
        </p>

        <div class="divider"></div>

        <table style="width: 100%; border-collapse: collapse;">
            @foreach ($order->items as $item)
                <tr>
                    <td style="text-align: left; padding-bottom: 5px;">
                        {{ $item->food->name }} <br>

                        <span style="font-size: 10px; color: #555;">
                            {{ $item->quantity }} x @ Rp {{ number_format($item->price, 0, ',', '.') }}
                        </span>
                    </td>

                    <td style="text-align: right; vertical-align: top;">
                        Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </table>

        <div class="divider"></div>

        <div class="total">
            TOTAL: Rp {{ number_format($order->total_price, 0, ',', '.') }}
        </div>

        <div class="footer">
            <p>Terima Kasih atas Kunjungan Anda!</p>
            <p>Wifi: RestoEnak / Pass: 123456</p>
        </div>
    </div>
</body>

</html>
