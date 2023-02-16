@extends('layout.app')

@php
@endphp

@section('page_title')
    {{-- {{ $page_title }} --}}
@endsection

@php
@endphp

@section('content')

<div class="mx-auto">
    <h1 class="font-bold flex flex-1 items-center gap-3 mb-4">
        <button type="button" onclick="window.history.back()" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">Back</button>
        <span class="text-2xl">Transaction Details</span>
    </h1>

    @if(session()->has('message'))
        <div class="bg-gray-300 text-rose-500 rounded-lg p-4 text-center mb-2">
        {{ session()->get('message') }}
        </div>
    @endif

    <x-card class="w-1/2 mx-auto">
        <div>
            {{ $purchase_data->product_name }}
        </div>
        @if (($purchase_data->bundle != null))
            <p class="flex justify-between"><span>Bundle</span> <span>{{ $purchase_data->bundle }}</span></p>            
        @endif
        @if (($purchase_data->smartcard_no != null))
            <p class="flex justify-between"><span>Smartcard number</span> <span>{{ $purchase_data->smartcard_no }}</span></p>            
        @endif
        @if (($purchase_data->meter_no != null))
            <p class="flex justify-between"><span>Meter number</span> <span>{{ $purchase_data->meter_no }}</span></p>            
        @endif
        <p class="flex justify-between"><span>Email Address</span> <span>{{ $purchase_data->email }}</span></p>
        <p class="flex justify-between"><span>Mobile Number</span> <span>{{ $purchase_data->phone }}</span></p>
        <p class="flex justify-between"><span>Amount</span> <span>{{ $purchase_data->amount }} + ({{$purchase_data->convinience_fee ?? 0}} Convenience fee)</span></p>
        <p class="flex justify-between"><span>Total Amount</span> <span> {{$purchase_data->amount + ($purchase_data->convinience_fee ?? 0)}}</span></p>
        <p class="flex justify-between"><span>Status</span> <span> {{$purchase_data->status}}</span></p>
        
        <div class="mt-4 flex justify-around">
            <form action="/pay-with-paystack" method="POST">
                @csrf
                <input type="hidden" value="{{json_encode($purchase_data)}}" name="purchase_data" />

                <button type="submit" class="w-[10rem] inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Pay with Paystack
                </button>
            </form>

            <form action="/pay" method="POST">
                @csrf
                <input type="hidden" value="{{json_encode($purchase_data)}}" name="purchase_data" />

                <button type="submit" class="w-[8rem] inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Pay with wallet
                </button>
            </form>
        </div>
    </x-card>
</div>
    
@endsection