@extends('layouts.email.app')

@section('title', 'Pembayaran Berhasil')

@php
    date_default_timezone_set('Asia/Jakarta');
@endphp

@section('content')
    @php
        $produk = '';
        foreach ($data->items as $key => $item) {
            $produk .= $item->product;
            $produk .= $key == count($data->items) - 1 ? '' : ', ';
        }
        
        $total = $data->items->sum('total');
        $shippingCost = $data->items->sum('shipping_cost');
        $totalFormat = number_format($total + $shippingCost, 0, 0, 2);
    @endphp
    <p>
        Hai <b>{{ $data->store->user->name }}</b>, id pesanan <b>{{ $code }}</b> telah selesai melakukan pembayaran
        pada
        {{ date('l, d F Y') }}, terkait produk <b>{{ $produk }}</b>. Mohon untuk mengemas produk
        <b>{{ $produk }}</b> dan melakukan
        konfirmasi pesanan sehingga
        dapat melanjutkan status order ke pengiriman.
    </p>
    <p>&nbsp;</p>
@endsection
