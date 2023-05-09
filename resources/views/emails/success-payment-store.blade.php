@extends('layouts.email.app')

@section('title', 'Pembayaran Berhasil')

@php
    date_default_timezone_set('Asia/Jakarta');
@endphp

@section('content')
    <p>
        Halo {{ $data->store->user->name }}
    </p>
    <p>&nbsp;</p>
    @php
        $total = $data->items->sum('total');
        $shippingCost = $data->items->sum('shipping_cost');
        $totalFormat = number_format($total + $shippingCost, 0, 0, 2);
    @endphp
    <p>
        Order dengan total pembayaran {{ $totalFormat }} sudah lunas. Segera lakukan pengiriman.
    </p>
    <p>&nbsp;</p>
@endsection
