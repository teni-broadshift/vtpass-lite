@extends('layout.app')

@section('page_title')
    Airtime Recharge   
@endsection

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
            <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
            <div class="mt-1">
              <input 
                id="email" 
                name="email" 
                type="email" 
                autocomplete="email" 
                required 
                class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
                value="{{old('email')}}"
                >
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

        <div>
            <label for="amount" class="block text-sm font-medium text-gray-700">Amount</label>
            <div class="mt-1">
              <input 
                id="amount" 
                name="amount" 
                type="number" 
                required 
                class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
                value="{{old('amount')}}"
                >
            </div>
            @error('amount')
                <p class="text-red-500 text-xs mt-1">{{$message}}</p>
            @enderror
        </div>

        <div>
            <button type="submit" class="w-24 inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Continue</button>
        </div>
    </form>
</div>
@endsection