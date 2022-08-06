@extends('layout.app')

@section('title', 'Dashboard')

@section('content')

    <div class="max-w-xl mx-auto my-10" x-data="dashboard">
        <a href="{{ route('dashboard.logout') }}"
            class="py-2 px-4  bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500 focus:ring-offset-indigo-200 text-white transition ease-in duration-200 text-center text-base font-semibold shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 rounded-lg ">
            Logout
        </a>

        <div class="my-5">
            <div class="float-right relative -top-14">
                <div x-data="{ open: false }">
                    <button @click="open = !open;markAllRead($data.open)"
                        class="p-1 rounded-full focus:outline-none lg:text-loom"><span class="sr-only">View
                            notifications</span><svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                            </path>
                        </svg>
                        @if (auth()->user()->unreadNotifications->count() > 0)
                            <span
                                class="relative -top-8 -right-3 px-2 py-1 text-sm rounded-full bg-indigo-600 text-white">{{ auth()->user()->unreadNotifications->count() }}</span>
                        @endif

                    </button>
                    <div x-show="open"
                        class="hidden lg:block origin-top-right absolute right-0 mt-4 w-96 py-8 px-2 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                        @click.outside="open = false" style="display: none;">
                        <div class="flow-root">
                            <ul role="list" class="-my-5 divide-y divide-gray-200">
                                @foreach (auth()->user()->notifications as $notification)
                                    <li class="py-5 px-4 rounded-md {{ $notification->read_at ? '' : 'bg-blue-100' }}">
                                        <div class="relative focus-within:ring-2 focus-within:ring-indigo-500">
                                            <h3 class="text-sm font-semibold text-gray-800"><a
                                                    href="{{ $notification->data['link'] }}"
                                                    class="hover:underline focus:outline-none">{{ $notification->data['title'] }}</a>
                                            </h3>
                                            <p class="mt-1 text-sm text-gray-600 line-clamp-2">
                                                {{ $notification->data['message'] }}
                                            </p>
                                        </div>
                                    </li>
                                @endforeach
                                @if (count(auth()->user()->notifications) == 0)
                                    <li class="py-5">
                                        <div class="relative focus-within:ring-2 focus-within:ring-indigo-500">
                                            <h3 class="text-sm font-semibold text-gray-800">You don't have any notifications
                                            </h3>
                                        </div>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <p>Last active: {{ auth()->user()->last_active }}</p>

        <form action="{{ route('dashboard.update') }}" method="POST" class="mb-10 border border-4 rounded-lg my-10 p-10">
            @csrf
            <label class="flex items-center space-x-3 cursor-pointer">
                <input type="checkbox" name="two_step" class="w-5 h-5" {{ auth()->user()->two_step ? 'checked' : '' }} />
                <span class="text-gray-700 font-normal">
                    Two step verification
                </span>
            </label>

            <p class="mt-10">Mobile number (with country code)</p>
            <input type="text"
                class=" rounded-lg border mt-5 flex-1 appearance-none border border-gray-300 w-full py-2 px-4 bg-white text-gray-700 placeholder-gray-400 shadow-sm text-base focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                placeholder="Ex: +14842634786" value="{{ auth()->user()->mobile }}" name="mobile" required
                minlength="12" />


            <p class="mt-10">Choose two step verification method</p>

            <select
                class="block w-52 text-gray-700 py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                name="two_step_method">
                <option value="email" {{ auth()->user()->two_step_method == 'email' ? 'selected' : '' }}>
                    Email
                </option>
                <option value="mobile" {{ auth()->user()->two_step_method == 'mobile' ? 'selected' : '' }}>
                    Mobile
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
            <p class="text-lg">Set up your two factor authentication by scanning the QR code below.
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
        });

        function markAllRead(flag) {
            if (flag) {
                fetch("{{ route('notification.mark-all-read') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({})
                    })
                    .then(async res => {})
                    .catch((err) => {});
            }
        }
    </script>
@endpush
