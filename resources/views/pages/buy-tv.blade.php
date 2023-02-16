@extends('layout.app')

@section('page_title')
    TV Subscription   
@endsection

@php
@endphp

@section('content')
<div class="mx-auto max-w-xl sm:px-6 lg:px-8">
    <h1 class="text-2xl mb-4">{{ $product->name }}</h1>

    @if($errors->any())
        <x-card class="p-4">
            {{ implode('', $errors->all(':message')) }}
        </x-card>
    @endif


    <form action={{"/confirm-transaction/" . $product->serviceID}} method="POST" class="flex flex-col gap-4">
        @csrf
        <input type="hidden" value="{{$product->name}}" name="product_name" />
        <input type="hidden" value="{{$product->serviceID}}" name="service_id" />

        <div>
            <label for="smartcard_no" class="block text-sm font-medium text-gray-700">Smartcard Number</label>
            <div class="mt-1">
              <input 
                id="smartcard_no" 
                name="smartcard_no" 
                type="text" 
                autocomplete="smartcard_no"
                onchange="validateSmartcard(event)" 
                required 
                class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
                value="{{old('smartcard_no')}}"
                />
            </div>
            <p id="smartcard-info" class="text-red-500 text-xs mt-1">
            </p>
            @error('smartcard_no')
                <p class="text-red-500 text-xs mt-1">{{$message}}</p>
            @enderror
        </div>

        <div class="flex flex-row flex-wrap justify-between">
            <div class="w-1/2">
                <label for="sub_type" class="block text-sm font-medium text-gray-700">Subscription type</label>
                <div class="mt-1">
                    <select id="sub_type" name="sub_type" class="mt-1 block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                        <option value="">Select</option>
                        <option value="renew">Renew</option>
                        <option value="change">Change</option>
                    </select>
                </div>
                @error('sub_type')
                    <p class="text-red-500 text-xs mt-1">{{$message}}</p>
                @enderror
            </div>
    
            <div>
                <label for="sub_plan" class="block text-sm font-medium text-gray-700">Subscription Plan</label>
                <div class="mt-1">
                    <select onchange="populateAmount(event)" id="sub_plan" name="sub_plan" class="mt-1 block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                        <option value="{{json_encode("")}}">Select Subscription Plan</option>
                        @foreach ($variations as $variation)
                            <option value="{{json_encode($variation)}}">{{ $variation->name }}</option>
                        @endforeach
                    </select>
                </div>
                @error('sub_plan')
                    <p class="text-red-500 text-xs mt-1">{{$message}}</p>
                @enderror
            </div>
        </div>


        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <div class="mt-1">
              <input 
                id="email" 
                name="email" 
                type="email" 
                autocomplete="email" 
                required 
                class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
                value="{{old('email')}}"
                />
            </div>
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{$message}}</p>
            @enderror
        </div>

        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700">Mobile Number</label>
            <div class="mt-1">
              <input 
                id="phone" 
                name="phone" 
                type="number" 
                autocomplete="phone" 
                required 
                class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
                value="{{old('phone')}}"
                >
            </div>
            @error('phone')
                <p class="text-red-500 text-xs mt-1">{{$message}}</p>
            @enderror
        </div>

        <div class="inline-flex justify-between">
            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700">Amount</label>
                <div class="mt-1">
                  <input 
                    id="amount" 
                    name="amount" 
                    value="0" 
                    type="number" 
                    required 
                    class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
                    value="{{old('amount')}}"
                    readonly
                    >
                </div>
                @error('amount')
                    <p class="text-red-500 text-xs mt-1">{{$message}}</p>
                @enderror
            </div>

            <div>
                <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
                <div class="mt-1">
                  <input 
                    id="quantity" 
                    name="quantity" 
                    value="1" 
                    type="number" 
                    required 
                    class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
                    >
                </div>
                @error('quantity')
                    <p class="text-red-500 text-xs mt-1">{{$message}}</p>
                @enderror
            </div>
        </div>
 

        <div>
            <button type="submit" class="w-24 inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Continue
            </button>
        </div>
    </form>
</div>
@endsection

@push('javascript')
function populateAmount(event) {
    let amount = document.getElementById("amount");
    let dataBundle = JSON.parse(event.target.value) ?? '';
    amount.value = (dataBundle.variation_amount) ?? 0;
    console.log(amount.value);
}

function validateSmartcard(event) {
    console.log(event.target.value);
    {{-- request to VTPASS endpoint to validate smartcard number --}}
    axios.post('https://sandbox.vtpass.com/api/merchant-verify',
    {
        billersCode: event.target.value,
        serviceID: '{!! request('service_id') !!}'
    },
    {
        'headers': {
            "Authorization": `Basic ${btoa('sandbox@vtpass.com:sandbox')}`,
        }
    }
    ).then((res) => {
        document.getElementById('smartcard-info').innerHTML = res.data.content.error ?? res.data.content.Customer_Name;
    }).catch((err) => {
        console.log(err)
        document.getElementById('smartcard-info').innerHTML = err.response.data;
    })
}
    
@endpush