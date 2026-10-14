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


<title>{{ env('APP_NAME') }} - دسته بندی - {{ $category->title }} </title>
@endsection

@section('content')
<div class="custom-responsive-row col-12 row">
    <div class="col-md-8 rounded" style="max-width: 800px; height: auto;">
        {{-- عنوان دسته‌بندی --}}
        {{-- <section class="feedback-section mb-2">
            <div class="feedback-box p-3">
                <h3 class="text-right mb-2">
                    <i class="fas fa-newspaper me-3"></i>
                    خبرهای مرتبط با دسته‌بندی: {{ $category->title }}
                </h3>
            </div>
        </section> --}}

        {{-- لیست اخبار --}}
        <section class="feedback-section py-4">
    <div class="feedback-box">
        @if(count($news_category) > 0)
            @foreach ($news_category as $news)
                <div class="news-card row g-3 align-items-stretch shadow-sm rounded-3 p-3 mb-4 text-light hover-card">
                    <div class="col-md-4">
                        <a href="{{ route('customer.news.show', $news) }}">
                            @if($news->media_type === 'image')
                                <img src="{{ asset('storage/' . $news->media_path) }}" 
                                     class="img-fluid rounded-3 shadow-sm w-100 h-100 object-fit-cover" 
                                     alt="{{ $news->title }}" />
                            @elseif($news->media_type === 'video')
                                <img src="{{ asset('storage/' . $news->thumbnailVideo) }}" 
                                     class="img-fluid rounded-3 shadow-sm w-100 h-100 object-fit-cover" 
                                     alt="{{ $news->title }}" />
                            @endif
                        </a>
                    </div>
                    <div class="col-md-8 d-flex flex-column justify-content-between">
                    <div>
                        <!-- دسته‌بندی به شکل badge -->
                        <span class="badge bg-danger mb-2">{{ $news->category->title }}</span>

                        <!-- عنوان خبر -->
                        <h5 class="news-title fw-bold mb-2">
                            <a href="{{ route('customer.news.show', $news) }}" 
                            class="text-decoration-none hover-link">
                                {{ $news->title }}
                            </a>
                        </h5>

                        <!-- خلاصه خبر -->
                        <p class="text-secondary small mb-3" style="line-height: 1.6;">
                            {{ Str::limit(trim(str_replace('&nbsp;', ' ', strip_tags($news->body))), 150, '...') }}
                        </p>
                    </div>

                    <!-- متای خبر (تاریخ + نویسنده) -->
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

                </div>
            @endforeach

            @if ($news_category->lastPage() > 1)
                <div class="custom-pagination-container">
                    <ul class="custom-pagination">

                        {{-- دکمه قبلی --}}
                        <li class="{{ $news_category->onFirstPage() ? 'disabled' : '' }}">
                            <a href="{{ $news_category->previousPageUrl() ?? '#' }}" rel="prev">&laquo;</a>
                        </li>

                        @php
                        $current = $news_category->currentPage();
                        $last = $news_category->lastPage();
                        @endphp

                        {{-- صفحه اول --}}
                        @if ($current > 3)
                        <li>
                            <a href="{{ $news_category->url(1) }}">1</a>
                        </li>
                        @endif

                        {{-- سه نقطه قبل --}}
                        @if ($current > 4)
                        <li class="disabled"><span>...</span></li>
                        @endif

                        {{-- صفحات اطراف صفحه فعلی --}}
                        @for ($i = max(1, $current - 1); $i <= min($last, $current + 1); $i++) <li
                            class="{{ $i == $current ? 'active' : '' }}">
                            <a href="{{ $news_category->url($i) }}">{{ $i }}</a>
                            </li>
                            @endfor

                            {{-- سه نقطه بعد --}}
                            @if ($current < $last - 3) <li class="disabled"><span>...</span></li>
                                @endif

                                {{-- صفحه آخر --}}
                                @if ($current < $last - 2) <li>
                                    <a href="{{ $news_category->url($last) }}">{{ $last }}</a>
                                    </li>
                                    @endif

                                    {{-- دکمه بعدی --}}
                                    <li class="{{ !$news_category->hasMorePages() ? 'disabled' : '' }}">
                                        <a href="{{ $news_category->nextPageUrl() ?? '#' }}" rel="next">&raquo;</a>
                                    </li>

                    </ul>
                </div>
                @endif

                
        <style>
            .custom-pagination-container {
                display: flex;
                justify-content: center;
                /* margin-top: 30px; */
                direction: rtl;
                font-family: 'Vazir', Tahoma, Arial, sans-serif;
            }

            .custom-pagination {
                list-style: none;
                padding: 0;
                display: flex;
                gap: 8px;
            }

            .custom-pagination li {
                display: inline-block;
            }

            .custom-pagination li a {
                display: block;
                padding: 8px 14px;
                color: #444;
                border: 1.5px solid #ddd;
                border-radius: 8px;
                text-decoration: none;
                font-weight: 600;
                transition: all 0.3s ease;
                user-select: none;
            }

            .custom-pagination li a:hover:not(.disabled) {
                background-color: #dc3545;
                color: white;
                border-color: #dc3545;
            }

            .custom-pagination li.active a {
                background-color: #dc3545;
                color: white;
                border-color: #dc3545;
                cursor: default;
            }

            .custom-pagination li.disabled a {
                color: #aaa;
                cursor: not-allowed;
                pointer-events: none;
            }

        </style>

        @else
            <p>خبری برای دسته‌بندی <span class="text-danger">{{ $category->title }}</span> یافت نشد!</p>
        @endif
    </div>
</section>

    </div>



<!-- Sidebar -->
@include('customer.news.sidebar')
</div>

@endsection


@section('scripts')

@endsection
