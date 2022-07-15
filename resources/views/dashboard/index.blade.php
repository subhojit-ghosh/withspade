@extends('layout.app')

@section('title', 'Dashboard')

@section('content')

    <div class="max-w-xl mx-auto my-10" x-data="dashboard">
        <a href="{{ route('dashboard.logout') }}"
            class="py-2 px-4  bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500 focus:ring-offset-indigo-200 text-white transition ease-in duration-200 text-center text-base font-semibold shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 rounded-lg ">
            Logout
        </a>

        <form action="{{ route('dashboard.update') }}" method="POST">
            @csrf
            <label class="flex items-center space-x-3 mt-32 cursor-pointer">
                <input type="checkbox" name="two_step" class="w-5 h-5" {{ auth()->user()->two_step ? 'checked' : '' }} />
                <span class="text-gray-700 font-normal">
                    Two step verification
                </span>
            </label>

            <button type="submit"
                class="py-2 px-4 mt-10 bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500 focus:ring-offset-indigo-200 text-white transition ease-in duration-200 text-center text-base font-semibold shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 rounded-lg ">
                Save
            </button>
        </form>


    </div>

@endsection
