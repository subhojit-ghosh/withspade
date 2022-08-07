@extends('layout.app')

@section('title', 'Blog')

@section('content')
    <main class="flex-auto min-h-screen">
        <div class="relative py-8 px-4 sm:px-6 lg:py-12 lg:px-8">
            <div class="relative max-w-7xl mx-auto">
                <div class="text-center">
                    <h2 class="text-3xl tracking-tight font-extrabold text-gray-900 sm:text-4xl">Spade Blog</h2>
                    <p class="mt-3 max-w-2xl mx-auto text-xl text-gray-500 sm:mt-4">Stay connected with the latest Spade
                        information, events, updates and user deals by following our blog.</p>
                </div>
                <div class="mt-12 max-w-lg mx-auto grid gap-5 lg:grid-cols-3 lg:max-w-none">
                    @foreach ($posts as $post)
                        <div class="flex flex-col rounded-lg shadow-md overflow-hidden">
                            <div class="flex-shrink-0"><img class="h-48 w-full object-cover"
                                    src="{{ $post->thumbnail->attachment->guid }}" alt=""></div>
                            <div class="flex-1 bg-white p-6 flex flex-col justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-loom"></p><a
                                        href="{{ route('blog.detail', $post->post_name) }}" class="block mt-2">
                                        <p class="text-xl font-semibold text-gray-900">{{ $post->post_title }}</p>
                                        <p class="mt-3 text-base text-gray-500">{{ $post->post_excerpt }}</p>
                                    </a>
                                </div>
                                <div class="mt-6 flex items-center">
                                    <div class="flex-shrink-0"><img class="h-10 w-10 rounded-full"
                                            src="{{ $post->author->avatar }}" alt=""></div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $post->author->display_name }}</p>
                                        <div class="flex space-x-1 text-sm text-gray-500">
                                            <time datetime="2020-03-16">
                                                {{ \Carbon\Carbon::parse($post->post_date)->format('M d, Y') }}
                                            </time>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </main>
@endsection
