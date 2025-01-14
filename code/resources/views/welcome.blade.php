@extends('master.main')

@section('title','Home Page')

@section('content')

    {{--@include('includes.search')--}}

    <div class="row">
        <div class="col-md-3 col-sm-12" style="margin-top:2.3em">
            @include('includes.categories')
        </div>
        <div class="col-md-9 col-sm-12 mt-3 ">

            <div class="row">
                <div class="col">
                    <h1 class="col-10">Welcome to the {{config('app.name')}}</h1>
                    <hr>
                </div>
            </div>

            <div class="row">
    <div class="col">
        Welcome to the premier marketplace for secure and anonymous transactions. Browse with confidence, knowing your privacy is our priority.
    </div>
</div>
<div class="row mt-5">

    <div class="col-md-4">
        <h4><i class="fa fa-money-bill-wave-alt text-info"></i> No Deposit Required</h4>
        <p>
            Start trading immediately without the need for upfront deposits. Enjoy a hassle-free experience with no barriers to entry.
        </p>
    </div>

    <div class="col-md-4">
        <h4><i class="fa fa-shield-alt text-info"></i> Escrow Protection</h4>
        <p>
            All transactions are secured through our escrow system, ensuring both buyers and sellers are protected throughout the process.
        </p>
    </div>

    <div class="col-md-4">
        <h4><i class="fa fa-coins text-info"></i> Multiple Cryptocurrency Support</h4>
        <p>
            We support a wide range of cryptocurrencies, offering flexibility and convenience for every user.
        </p>
    </div>
</div>
            <div class="row">
                <div class="col">
                    <hr>
                </div>
            </div>
            @isModuleEnabled('FeaturedProducts')
                @include('featuredproducts::frontpagedisplay')
            @endisModuleEnabled

            <div class="row mt-4">

                <div class="col-md-4">
                    <h4>
                        Top Vendors
                    </h4>
                    <hr>
                    @foreach(\App\Vendor::topVendors() as $vendor)
                        <table class="table table-borderless table-hover">
                            <tr>
                                <td>
                                    <a href="{{route('vendor.show',$vendor)}}"
                                       style="text-decoration: none; color:#212529">{{$vendor->user->username}}</a>
                                </td>
                                <td class="text-right">
                                    <span class="btn btn-sm @if($vendor->vendor->experience >= 0) btn-primary @else btn-danger @endif active"
                                          style="cursor:default">Level {{$vendor->getLevel()}}</span>

                                </td>
                            </tr>
                        </table>
                    @endforeach
                </div>
                <div class="col-md-4">
                    <h4>
                        Latest orders
                    </h4>
                    <hr>
                    @foreach(\App\Purchase::latestOrders() as $order)
                        <table class="table table-borderless table-hover">
                            <tr>
                                <td>
                                    <img class="img-fluid" height="23px" width="23px"
                                         src="{{ asset('storage/'  . $order->offer->product->frontImage()->image) }}"
                                         alt="{{ $order->offer->product->name }}">
                                </td>
                                <td>
                                    {{str_limit($order->offer->product->name,50,'...')}}
                                </td>
                                <td class="text-right">
                                    {{$order->getSumLocalCurrency()}} {{$order->getLocalSymbol()}}
                                </td>
                            </tr>
                        </table>
                    @endforeach
                </div>

                <div class="col-md-4">
                    <h4>
                        Rising vendors
                    </h4>
                    <hr>
                    @foreach(\App\Vendor::risingVendors() as $vendor)
                        <table class="table table-borderless table-hover">
                            <tr>
                                <td>
                                    <a href="{{route('vendor.show',$vendor)}}"
                                       style="text-decoration: none; color:#212529">{{$vendor->user->username}}</a>
                                </td>
                                <td class="text-right">
                                    <span class="btn btn-sm @if($vendor->vendor->experience >= 0) btn-primary @else btn-danger @endif active"
                                          style="cursor:default">Level {{$vendor->getLevel()}}</span>
                                </td>
                            </tr>
                        </table>
                    @endforeach
                </div>


            </div>


        </div>

    </div>

@stop
