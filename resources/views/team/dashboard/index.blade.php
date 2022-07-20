@extends('layout.app')

@section('title', 'Team Dashboard')

@section('content')

    <div class="max-w-xl mx-auto my-10" x-data="dashboard">
        <p class="my-10">Team name: {{ auth()->guard('team')->user()->name }}</p>
        <a href="{{ route('team.dashboard.logout') }}"
            class="py-2 px-4  bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500 focus:ring-offset-indigo-200 text-white transition ease-in duration-200 text-center text-base font-semibold shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 rounded-lg ">
            Logout
        </a>
    </div>

@endsection

@push('script')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('dashboard', () => ({}))
        });
    </script>
@endpush
