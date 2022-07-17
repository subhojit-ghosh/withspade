@extends('layout.app')

@section('title', 'Dashboard')

@section('content')

    <div class="max-w-xl mx-auto my-10" x-data="dashboard">
        <a href="{{ route('dashboard.logout') }}"
            class="py-2 px-4  bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500 focus:ring-offset-indigo-200 text-white transition ease-in duration-200 text-center text-base font-semibold shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 rounded-lg ">
            Logout
        </a>

        <form action="{{ route('dashboard.update') }}" method="POST" class="mb-10 border border-4 rounded-lg my-10 p-10">
            @csrf
            <label class="flex items-center space-x-3 cursor-pointer">
                <input type="checkbox" name="two_step" class="w-5 h-5" {{ auth()->user()->two_step ? 'checked' : '' }} />
                <span class="text-gray-700 font-normal">
                    Two step verification
                </span>
            </label>

            <p class="mt-10">Choose two step verification method</p>

            <select
                class="block w-52 text-gray-700 py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                name="two_step_method">
                <option value="email" {{ auth()->user()->two_step_method == 'email' ? 'selected' : '' }}>
                    Email
                </option>
                <option value="google2fa" {{ auth()->user()->two_step_method == 'google2fa' ? 'selected' : '' }}
                    {{ auth()->user()->google2fa_verified ? '' : 'disabled' }}>
                    Google Authenticator
                </option>
            </select>

            <button type="submit"
                class="py-2 px-4 mt-10 bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500 focus:ring-offset-indigo-200 text-white transition ease-in duration-200 text-center text-base font-semibold shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 rounded-lg ">
                Save
            </button>
        </form>

        <form @submit.prevent="submit()" autoComplete="off" class="mb-10 border border-4 rounded-lg my-10 p-10">
            @csrf
            <p class="text-xl my-6 font-bold">
                Status:
                @if (auth()->user()->google2fa_verified)
                    <span class="text-green-500">Setup Successful</span>
                @else
                    <span class="text-red-500">Setup Not Done</span>
                @endif
            </p>
            <p class="text-2xl font-bold">Set up Authenticator</p>
            <p class="text-lg">Set up your two factor authentication by scanning the QR code below. Alternatively, you can
                use the code
                <code>{{ $secret }}</code>
            </p>
            {!! $inlineUrl !!}

            <p class="mt-10">Enter the code you see on the Authenticator app</p>
            <input type="text"
                class=" rounded-lg border mt-5 flex-1 appearance-none border border-gray-300 w-full py-2 px-4 bg-white text-gray-700 placeholder-gray-400 shadow-sm text-base focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                placeholder="Enter code to verify" x-model="secret" required />

            <button type="submit"
                class="py-2 px-4  mt-4 flex justify-center items-center  bg-blue-600 hover:bg-blue-700 focus:ring-blue-500 focus:ring-offset-blue-200 text-white w-full transition ease-in duration-200 text-center text-base font-semibold shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2  rounded-lg"
                x-bind:disabled="loading">
                <svg width="20" height="20" fill="currentColor" class="mr-2 animate-spin" x-show="loading" x-cloak
                    viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M526 1394q0 53-37.5 90.5t-90.5 37.5q-52 0-90-38t-38-90q0-53 37.5-90.5t90.5-37.5 90.5 37.5 37.5 90.5zm498 206q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-704-704q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm1202 498q0 52-38 90t-90 38q-53 0-90.5-37.5t-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-964-996q0 66-47 113t-113 47-113-47-47-113 47-113 113-47 113 47 47 113zm1170 498q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-640-704q0 80-56 136t-136 56-136-56-56-136 56-136 136-56 136 56 56 136zm530 206q0 93-66 158.5t-158 65.5q-93 0-158.5-65.5t-65.5-158.5q0-92 65.5-158t158.5-66q92 0 158 66t66 158z">
                    </path>
                </svg>
                Verify
            </button>

        </form>
    </div>

@endsection

@push('script')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('dashboard', () => ({
                loading: false,
                secret: '',

                async submit() {
                    this.loading = true;
                    fetch("{{ route('dashboard.verify-google2fa') }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                secret: this.secret
                            })
                        })
                        .then(async res => {
                            this.loading = false;
                            const data = await res.json();
                            if (!res.ok) {
                                showToast(data.message, 'error');
                            } else {
                                showToast(data.message);
                                location.reload();
                            }
                        })
                        .catch((err) => {
                            console.log(err);
                            this.loading = false;
                            showToast('Something went wrong! Try again.', 'error');
                        });
                }
            }))
        })
    </script>
@endpush
