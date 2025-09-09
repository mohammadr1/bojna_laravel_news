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

        {{-- <section class="feedback-section mb-2">
            <div class="feedback-box">
                <div class="feedback-image-wrapper p-3 mb-2">
                    <h3 class="text-right mb-2"><i class="fas fa-newspaper me-3"></i>خبرهای مرتبط با برچسب:
                        {{ $tag->name }}</h3>
                </div>
            </div>
        </section> --}}

        <section class="feedback-section">
            <div class="feedback-box">

                @foreach ($newsList as $news)
                <div class="col-md-12 rounded " style="height: auto;">

                    <div class="news-card row align-items-center shadow-sm rounded-3 p-3 mb-4 bg-white">
                        <div class="col-md-4">
                            {{-- <img src="{{ asset('storage/' . $news->image) }}" class="img-fluid rounded-3 shadow-sm"
                                alt="تصویر خبر"> --}}
                            @if($news->media_type === 'image')
                                <img src="{{ asset('storage/' . $news->media_path) }}" 
                                     class="img-fluid rounded-3 shadow-sm w-100 h-100 object-fit-cover" 
                                     alt="{{ $news->title }}" />
                            @elseif($news->media_type === 'video')
                                <img src="{{ asset('storage/' . $news->thumbnailVideo) }}" 
                                     class="img-fluid rounded-3 shadow-sm w-100 h-100 object-fit-cover" 
                                     alt="{{ $news->title }}" />
                            @endif
                        </div>
                        <div class="col-md-8 d-flex flex-column justify-content-between">
                            <div>
                                <!-- دسته‌بندی -->
                                <span class="badge bg-danger mb-2">{{ $news->category->title }}</span>

                                <!-- عنوان خبر -->
                                <h5 class="news-title fw-bold mb-2">
                                    <a href="{{ route('customer.news.show', $news) }}" 
                                    class="text-decoration-none text-white hover-link">
                                        {{ $news->title }}
                                    </a>
                                </h5>

                                <!-- خلاصه خبر -->
                                <p class="text-secondary small mb-3" style="line-height: 1.7;">
                                    {{ Str::limit(trim(str_replace('&nbsp;', ' ', strip_tags($news->body))), 150, '...') }}
                                </p>
                            </div>

                            <!-- متا -->
                            <div class="news-meta mt-3 d-flex align-items-center text-muted small">
                                <span class="news-date me-3">
                                    <i class="far fa-calendar me-1"></i>
                                    {{ jdate($news->published_at)->format('%d %B %Y') }}
                                </span>
                                <span class="news-author">
                                    <i class="far fa-user me-1"></i>
                                    {{ $news->author->display_name ?? 'نامشخص' }}
                                </span>
                            </div>
                        </div>

                        </a>
                    </div>
                </div>
                @endforeach

            </div>
        </section>

    </div>

    <!-- Sidebar -->
    @include('customer.news.sidebar')


    @endsection
