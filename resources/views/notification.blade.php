@extends('layout.app')

@section('title', 'Send Notification')

@section('content')

    <div class="max-w-xl mx-auto my-10" x-data="notification">

        <form @submit.prevent="submit()" autoComplete="off" class="mb-10 border border-4 rounded-lg my-10 p-10">
            <p class="mb-10 font-bold text-xl">Send Notification</p>

            <p class="mt-5">User</p>
            <select
                class="block w-full mt-2 text-gray-700 py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                x-model="user">
                <option value="all" selected>All</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">
                        {{ $user->name }} ({{ $user->email }})
                    </option>
                @endforeach
            </select>

            <p class="mt-5">Title</p>
            <input type="text"
                class=" rounded-lg border mt-2 flex-1 appearance-none border border-gray-300 w-full py-2 px-4 bg-white text-gray-700 placeholder-gray-400 shadow-sm text-base focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                x-model="title" required />

            <p class="mt-5">Link</p>
            <input type="text"
                class=" rounded-lg border mt-2 flex-1 appearance-none border border-gray-300 w-full py-2 px-4 bg-white text-gray-700 placeholder-gray-400 shadow-sm text-base focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                x-model="link" required />

            <p class="mt-5">Message</p>
            <input type="text"
                class=" rounded-lg border mt-2 flex-1 appearance-none border border-gray-300 w-full py-2 px-4 bg-white text-gray-700 placeholder-gray-400 shadow-sm text-base focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                x-model="message" required />

            <button type="submit"
                class="py-2 px-4  mt-4 flex justify-center items-center  bg-blue-600 hover:bg-blue-700 focus:ring-blue-500 focus:ring-offset-blue-200 text-white w-full transition ease-in duration-200 text-center text-base font-semibold shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2  rounded-lg"
                x-bind:disabled="loading">
                <svg width="20" height="20" fill="currentColor" class="mr-2 animate-spin" x-show="loading" x-cloak
                    viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M526 1394q0 53-37.5 90.5t-90.5 37.5q-52 0-90-38t-38-90q0-53 37.5-90.5t90.5-37.5 90.5 37.5 37.5 90.5zm498 206q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-704-704q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm1202 498q0 52-38 90t-90 38q-53 0-90.5-37.5t-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-964-996q0 66-47 113t-113 47-113-47-47-113 47-113 113-47 113 47 47 113zm1170 498q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-640-704q0 80-56 136t-136 56-136-56-56-136 56-136 136-56 136 56 56 136zm530 206q0 93-66 158.5t-158 65.5q-93 0-158.5-65.5t-65.5-158.5q0-92 65.5-158t158.5-66q92 0 158 66t66 158z">
                    </path>
                </svg>
                Send
            </button>
        </form>
    </div>

@endsection

@push('script')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('notification', () => ({
                loading: false,
                user: 'all',
                title: '',
                link: '',
                message: '',

                async submit() {
                    this.loading = true;
                    fetch("{{ route('notification.send') }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                user: this.user,
                                title: this.title,
                                link: this.link,
                                message: this.message,
                            })
                        })
                        .then(async res => {
                            this.loading = false;
                            const data = await res.json();
                            if (!res.ok) {
                                showToast(data.message, 'error');
                            } else {
                                showToast(data.message);
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
