@extends('layout.app')

@section('title', $post->post_title)

@section('content')
    <main class="flex-auto min-h-screen">
        <div class="relative py-8 px-4 sm:px-6 lg:py-12 lg:px-8">
            <div class="relative max-w-7xl mx-auto">
                <div>
                    <h2 class=" text-3xl tracking-tight font-extrabold text-gray-900 sm:text-4xl">
                        {{ $post->post_title }}</h2>
                    <div class="mt-6 flex items-center">
                        <div class="flex-shrink-0"><img class="h-10 w-10 rounded-full" src="{{ $post->author->avatar }}"
                                alt=""></div>
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
                <div class="mt-12 mx-auto">
                    <img class="w-full h-[400px] object-cover" src="{{ $post->thumbnail->attachment->guid }}" alt="">
                    <div class="my-16">{!! $post->post_content !!}</div>
                </div>
            </div>
        </div>
    </main>
@endsection
