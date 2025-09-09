@extends('customer.layouts.master-one-col')

@section('head-tag')
<link rel="stylesheet" href="{{ asset('assets/css/style-show.css') }}">

<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
{{-- <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
<style type="text/tailwindcss">
    @theme {
        --color-clifford: #da373d;
      }
</style> --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

@endsection

@section('content')

<div class="custom-responsive-row col-12 row">

    <div class="col-md-8 rounded" style="max-width: 800px; height: auto;">

        <section class="feedback-section">
            <div class="feedback-box p-0">
                <div class="feedback-image-wrapper">
                    {{-- <img src="{{ asset('storage/' . $news->image) }}" class="feedback-image" alt="{{ $news->title }}"> --}}
                    {{-- @if($news->content_type === 'video' && $news->video_code)
    <iframe src="https://www.aparat.com/video/video/embed/videohash/{{ $news->video_code }}/vt/frame"
        width="100%" height="400" frameborder="0" allowfullscreen></iframe>
@elseif($news->content_type === 'news' && $news->image)
    <img src="{{ asset('storage/' . $news->image) }}" alt="{{ $news->title }}">
@endif --}}

@if($news->media_type === 'image')
    <img src="{{ asset('storage/' . $news->media_path) }}" 
         class="w-100" 
         alt="{{ $news->title }}" />
@elseif($news->media_type === 'video')
    <iframe
        src="https://www.aparat.com/video/video/embed/videohash/{{ $news->media_path }}/vt/frame"
        frameborder="0"
        allowfullscreen
        style="width: 100%; aspect-ratio: 16/9;">
    </iframe>
@endif

{{-- @endif --}}
                </div>
                <div class="container d-flex flex-wrap align-items-center text-muted mt-3 pb-3 small">
                    <div class="me-4 mb-2">
                        <i class="far fa-calendar me-1"></i>
                        <span>{{ jdate($news->published_at)->format('%d %B %Y') }}</span>
                        {{-- خروجی مثال: ۱۵ مرداد ۱۴۰۲ --}}
                    <span class="news-author text-muted ms-3">
                                <i class="far fa-user me-1"></i> {{ $news->author->display_name ?? 'نامشخص' }}
                            </span>
                    </div>
                    {{-- <div class="mb-2">
                        <i class="far fa-eye me-1"></i>
                        <span> بازدید</span>
                    </div> --}}
                </div>


                <div class="feedback-content p-2">
                    <span class="news-pretitle">{{ $news->on_titr }}</span>
                    <h1 class="news-title">{{ $news->title }}</h1>
                    <p class="news-subtitle">{{ $news->subtitle }}</p>

                    <div class="news-body justify-text">
                        {!! preg_replace('/<figcaption[^>]*>.*?<\/figcaption>/is', '' , $news->body) !!}
                    </div>
                </div>
                {{-- <section class="feedback-section">
                    <div class="feedback-box">
                        <div class="feedback-image-wrapper">

                        </div>
                    </div>
                </section> --}}
            </div>
        </section>



        <section class="feedback-section rounded bg-white mt-3">
            <div class="feedback-box">

                @php
                $shortUrl = route('short.redirect', ['code' => $news->short_link]);
                @endphp

                <div>
                    <h5>لینک کوتاه خبر</h3>
                    <section class="">
                        <small id="copyMessage" style="margin-left: 10px; color: green; display: none;">کپی شد
                            ✅</small>
                        <span id="shortLinkText"
                            style="color: #007bff; cursor: pointer; direction: ltr; float: left;"
                            onclick="copyShortLink()">
                            {{ $shortUrl }}
                        </span>
                    </section>
                </div>
            </div>
        </section>


        <script>
            function copyShortLink() {
                const text = document.getElementById("shortLinkText").innerText;

                navigator.clipboard.writeText(text).then(function () {
                    const msg = document.getElementById("copyMessage");
                    msg.style.display = "inline";
                    setTimeout(() => msg.style.display = "none", 2000);
                }).catch(function (err) {
                    alert("خطا در کپی کردن لینک!");
                });
            }

        </script>


        <section class="feedback-section rounded bg-white mt-3">
            <div class="container feedback-box">
                <h5 class="font-bold text-gray-800">برچسب‌ها</h5>
                @if($news->tags && $news->tags->count())
                <div class="flex flex-wrap gap-2">
                    @foreach ($news->tags as $tag)
                    <a href="{{ route('customer.news.tags', $tag->name) }}" style="cursor: pointer;"
                        class="tag-item bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm hover:bg-blue-200">
                        {{ $tag->name }}
                    </a>
                    @endforeach
                </div>
                @else
                <p class="p-2 text-gray-500">بدون برچسب</p>
                @endif
            </div>
        </section>



    </div>

    <!-- Sidebar -->
    @include('customer.news.sidebar')


    @endsection


    @section('scripts')

    @endsection
