@extends('master.profile')

@section('title', 'Purchases')

@section('profile-content')
    @include('includes.flash.success')
    @include('includes.flash.error')

    <h1 class="mb-3">Purchases</h1>
    
    <ul class="nav nav-tabs nav-fill mb-3">
        <li class="nav-item">
            <a class="nav-link @if(!array_key_exists($state, \App\Purchase::$states)) active @endif" href="{{ route('profile.purchases') }}">
                All ({{ auth() -> user() -> purchasesCount() }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link @if($state == 'purchased') active @endif" href="{{ route('profile.purchases', 'purchased') }}">
                Purchased ({{ auth() -> user() -> purchasesCount('purchased') }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link @if($state == 'sent') active @endif" href="{{ route('profile.purchases', 'sent') }}">
                Sent ({{ auth() -> user() -> purchasesCount('sent') }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link @if($state == 'delivered') active @endif" href="{{ route('profile.purchases', 'delivered') }}">
                Delivered ({{ auth() -> user() -> purchasesCount('delivered') }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link @if($state == 'disputed') active @endif" href="{{ route('profile.purchases', 'disputed') }}">
                Disputed ({{ auth() -> user() -> purchasesCount('disputed') }})
            </a>
        </li>
    </ul>
    
    <table class="table table-hover table-striped">
        <thead>
        <tr>
            <th>Product</th>
            <th>#</th>
            <th>Price</th>
            <th>Shipping</th>
            <th>Total</th>
            <th>Vendor Payment Address</th>
            {{-- <th>ID</th> --}}

        </tr>
        </thead>
        <tbody>
        @foreach($purchases as $purchase)
            <tr>
                <td>
                    <a href="{{ route('product.show', $purchase -> offer -> product) }}">{{ $purchase -> offer -> product -> name }}</a>
                    <br>by
                    <a href="{{ route('vendor.show', $purchase -> vendor) }}">{{ $purchase -> vendor -> user -> username }}</a>
                    @if($purchase -> isDisputed() && $purchase -> dispute -> isResolved())
                        <span class="badge badge-success">resolved</span>
                    @endif
                </td>
                <td class="">
                    {{ $purchase -> quantity }}
                </td>
                <td class="">
                    <span class="badge badge-mblue">@include('includes.currency', ['usdValue' => $purchase -> offer -> price ])</span>
                </td>
                <td class="">
                    @if($purchase -> shipping)
                        <p class="text-muted text-sm-center">{{ $purchase -> shipping -> name }} - {{ $purchase -> shipping -> price }} $</p>
                    @else
                        <span class="badge badge-info">Digital delivery</span>
                    @endif
                </td>
                <td class="">
                    <span class="badge badge-mblue">@include('includes.currency', ['usdValue' => $purchase -> value_sum ])</span>
                </td>

                <td>
                    <input type="text" readonly="readonly" class="form-control form-control-sm" value="{{ $purchase -> address }}">
                </td>
                {{-- <td class="text-right">
                    <a href="{{ route('profile.purchases.single', $purchase) }}" class="btn btn-sm {{ $purchase -> isCanceled() ? 'btn-danger' : 'btn-mblue' }} mt-1" --}}
                         {{-- >@if($purchase->isCanceled()) <em>Canceled</em> @else {{ $purchase -> short_id }} @endif</a> --}}


                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="alert alert-danger">
        <h4 class="alert-heading">Payment Instructions</h4>
        <p>To confirm your purchase, please send the exact payment amount to the vendor's wallet address shown under "Vendor Payment Address". The transaction will be processed once payment is received.</p>
        <hr>
        <p class="mb-0">Important: Make sure to send the exact amount displayed and use the correct wallet address to avoid any issues with your order processing.</p>
    </div>

    {{ $purchases -> links('includes.paginate') }}
@stop