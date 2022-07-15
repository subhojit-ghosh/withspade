@extends('layout.app')

@section('title', 'Two Step Verification')

@section('content')

    <div class="max-w-xl mx-auto my-10" x-data="twoStep">

        <div class="flex flex-col w-full px-4 py-8 bg-white rounded-lg shadow sm:px-6 md:px-8 lg:px-10">
            <div class="self-center mb-6 text-xl font-light text-gray-600 sm:text-2xl">
                Two Step Verification
            </div>
            <p class="text-center">Please confirm your account by entering the verification code sent to your email address
            </p>
            <div class="mt-8">
                <form @submit.prevent="submit()" autoComplete="off">
                    <div class="flex flex-col mb-2">
                        <div class="flex relative ">
                            <input type="text"
                                class=" rounded-lg flex-1 appearance-none border border-gray-300 w-full py-2 px-4 bg-white text-gray-700 placeholder-gray-400 shadow-sm text-base focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                                placeholder="Verification code" x-model="otp" required />
                        </div>
                    </div>
                    <div class="flex w-full">

                        <button type="submit"
                            class="py-2 px-4 flex justify-center items-center  bg-blue-600 hover:bg-blue-700 focus:ring-blue-500 focus:ring-offset-blue-200 text-white w-full transition ease-in duration-200 text-center text-base font-semibold shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2  rounded-lg"
                            x-bind:disabled="loading">
                            <svg width="20" height="20" fill="currentColor" class="mr-2 animate-spin"
                                x-show="loading" x-cloak viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M526 1394q0 53-37.5 90.5t-90.5 37.5q-52 0-90-38t-38-90q0-53 37.5-90.5t90.5-37.5 90.5 37.5 37.5 90.5zm498 206q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-704-704q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm1202 498q0 52-38 90t-90 38q-53 0-90.5-37.5t-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-964-996q0 66-47 113t-113 47-113-47-47-113 47-113 113-47 113 47 47 113zm1170 498q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-640-704q0 80-56 136t-136 56-136-56-56-136 56-136 136-56 136 56 56 136zm530 206q0 93-66 158.5t-158 65.5q-93 0-158.5-65.5t-65.5-158.5q0-92 65.5-158t158.5-66q92 0 158 66t66 158z">
                                </path>
                            </svg>
                            Verify
                        </button>

                    </div>
                </form>
            </div>
        </div>

    </div>

@endsection

@push('script')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('twoStep', () => ({
                loading: false,
                otp: '',

                async submit() {
                    this.loading = true;
                    fetch("{{ route('login.verify') }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                otp: this.otp
                            })
                        })
                        .then(async res => {
                            this.loading = false;
                            const data = await res.json();
                            if (!res.ok) {
                                showToast(data.message, 'error');
                            } else {
                                showToast(data.message);
                                location.href = "{{route('dashboard.index')}}";
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
