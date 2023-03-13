<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice</title>
    <style>
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .logo img {
            max-width: 200px;
        }
        .invoice {
            margin-bottom: 50px;
            background-image: url('path/to/paid.png');
            background-repeat: no-repeat;
            background-position: center center;
            background-size: 200px;
            opacity: 0.5;
            padding: 50px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            text-align: left;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
        .total {
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <img src="{{ public_path('storage/image/Logo.png') }}" alt="Logo">
        </div>
        <div class="company-info">
            <h2>Locahub Official</h2>
            <p>Malang, Jawa Timur</p>
            <p>+62-822-3475-87464 | locahubie.com</p>
        </div>
    </div>
    <div class="invoice">
        <h3>Invoice #{{ $transaction->code }}</h3>
        <p>Date: {{ $transaction->created_at->format('d/m/Y') }}</p>
        <p>Buyer Name: {{ $transaction->buyer->name}}</p>
        <p>Phone: {{ $transaction->address->phone_number}}</p>
        <p>Address: {{ $transaction->address->name}}<br>
            {{ $transaction->address->phone_number}}<br>
            {{ $transaction->address->address_label}}<br>
            {{ $transaction->address->complete_address}}<br>
            {{ $transaction->address->address_detail}}</p>
        <p>Payment Status: {{ $transaction->payment_status}}</p>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Shipping Cost</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
           <tbody>
                @foreach ($transaction->store_transactions as $storeTransaction)
                    @foreach ($storeTransaction->items as $item)
                        <tr>
                            <td>{{ $item->product }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ $item->price }}</td>
                            <td>{{ $storeTransaction->shipping_cost }}</td>
                            <td>{{ $item->quantity * $item->price + $storeTransaction->shipping_cost}}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4">Total</td>
                    <td>{{ $transaction->grand_total }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="Grand Total">
        <h3>Grand Total: Rp. {{ $transaction->grand_total }}</h3>
    </div>
</body>
</html>

