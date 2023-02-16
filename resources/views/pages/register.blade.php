@extends('layout.site')


@section('content')
<div class="flex min-h-full flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <img class="mx-auto h-12 w-auto bg-gray-500" src={{ asset('/img/vtpass-logo.png') }} alt="VT PAY" />
      <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900">Create an account</h2>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      @if(session()->has('message'))
        <div class="bg-gray-300 text-rose-500 rounded-lg p-4 text-center mb-2">
          {{ session()->get('message') }}
        </div>
      @endif
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <form class="space-y-6" action="/users" method="POST">
            @csrf
            <div class="flex gap-4">
                <div class="w-1/2 mr-2">
                    <label for="first_name" class="block text-sm font-medium text-gray-700">First name</label>
                    <div class="mt-1">
                        <input 
                          id="first_name" 
                          name="first_name" 
                          type="text" 
                          value="{{old('first_name')}}"
                          required 
                          class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                    </div>
                    @error('first_name')
                        <p class="text-red-500 text-xs mt-1">{{$message}}</p>
                    @enderror
                </div>
                
                <div class="w-1/2">
                    <label for="last_name" class="block text-sm font-medium text-gray-700">Last name</label>
                    <div class="mt-1">
                        <input 
                          id="last_name" 
                          name="last_name" 
                          type="text" 
                          value="{{old('last_name')}}"
                          required
                          class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                    </div>
                    @error('last_name')
                        <p class="text-red-500 text-xs mt-1">{{$message}}</p>
                    @enderror
                </div>
            </div>

          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
            <div class="mt-1">
              <input 
                id="email" 
                name="email" 
                type="email" 
                required 
                value="{{old('email')}}"
                class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
            </div>

            @error('email')
                <p class="text-red-500 text-xs mt-1">{{$message}}</p>
            @enderror
          </div>

          <div>
            <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
            <div class="mt-1">
              <input 
                id="phone" 
                name="phone" 
                type="phone" 
                required 
                value="{{old('phone')}}"
                class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
            </div>

            @error('phone')
                <p class="text-red-500 text-xs mt-1">{{$message}}</p>
            @enderror
          </div>
  
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <div class="mt-1">
              <input 
                id="password" 
                name="password" 
                type="password" 
                required 
                class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
            </div>
            @error('password')
                <p class="text-red-500 text-xs mt-1">{{$message}}</p>
            @enderror
          </div>
  
          <div>
            <button type="submit" class="flex w-full justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Sign in</button>
          </div>
        </form>

        <div class="py-2">
          <a class="mx-auto text-xs text-indigo-600" href={{ route('login') }}>
            Sign in instead
          </a>
        </div>
      </div>
  </div>
</div>  
@endsection