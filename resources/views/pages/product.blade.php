@extends('layout.app')

@php
@endphp

@section('page_title')
    {{ $page_title }}
@endsection

@section('content')

<div class="flex flex-row gap-4">
    @foreach ($product_providers as $provider)
        <x-card class="w-1/4">
            <a href={{ url("/buy/" . $provider['service_id']) }}>
                <div>
                    <p>{{ $provider['name'] }}</p>
                </div>
            </a>
        </x-card>
    @endforeach
</div>
    
@endsection