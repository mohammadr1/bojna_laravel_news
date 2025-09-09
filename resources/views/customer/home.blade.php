@extends('customer.layouts.master-one-col')

@section('head-tag')
@endsection

@section('content')
<div class="row g-3 align-items-stretch">
    <!-- اسلایدر -->
    <div class="col-md-8">
        <div class="position-relative shadow rounded overflow-hidden" style="height: 350px;">
            <div id="myCarousel" class="carousel slide h-100" data-bs-ride="carousel">
                <div class="carousel-inner h-100">
                    @foreach($sliders as $index => $slider)
                    <a href="{{ route('customer.news.show', $slider) }}" aria-label="{{ $slider->title }}">
                        <div class="carousel-item h-100 @if($index === 0) active @endif">
                            {{-- <img src="{{ asset('storage/' . $slider->image) }}" class="d-block w-100 h-100 object-fit-cover"
                                alt="{{ $slider->title }}"> --}}
                            @if($slider->media_type === 'image')
                                <img src="{{ asset('storage/' . $slider->media_path) }}" class="d-block w-100 h-100" alt="{{ $slider->title }}" alt="{{ $slider->title }}" />
                            @elseif($slider->media_type === 'video')
                                <img src="{{ asset('storage/' . $slider->thumbnailVideo) }}" class="d-block w-100 h-100" alt="{{ $slider->title }}" alt="{{ $slider->title }}" />
                            @endif
                                    
                            <div class="position-absolute bottom-0 w-100 p-3 slider-caption">
                                <h5 class="mb-2">{{ $slider->title }}</h5>
                                @if($slider->publish_date)
                                <p class="mb-1">{{ jdate($slider->publish_date)->format('j F Y') }}</p>
                                @endif
                                @if($slider->subtitle)
                                <p class="mb-0">{{ $slider->subtitle }}</p>
                                @endif
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#myCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                    <span class="visually-hidden">قبلی</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#myCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                    <span class="visually-hidden">بعدی</span>
                </button>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="d-flex flex-column" style="height: 350px;">
            @foreach ($leftSliderNews as $news)
            <div class="news-card d-flex shadow-sm rounded mb-2 p-2">
                <div class="w-50 pe-2">
                    <a href="{{ route('customer.news.show', $news) }}">
                        {{-- <img src="{{ asset('storage/' . $news->image) }}" class="img-fluid rounded w-100 h-100 object-fit-cover" alt="{{ $news->title }}"> --}}

                        @if($news->media_type === 'image')
                            <img src="{{ asset('storage/' . $news->media_path) }}" class="img-fluid rounded-3 shadow-sm" alt="{{ $news->title }}" alt="{{ $news->title }}" />
                        @elseif($news->media_type === 'video')
                            <img src="{{ asset('storage/' . $news->thumbnailVideo) }}" class="img-fluid rounded-3 shadow-sm" alt="{{ $news->title }}" alt="{{ $news->title }}" />
                        @endif

                    </a>
                </div>
                <div class="w-50 d-flex flex-column justify-content-between overflow-auto">
                    <div>
                        <span class="badge bg-danger mb-1">{{ $news->category->title }}</span>
                        <h6 class="news-title mb-1 small">
                            <a href="{{ route('customer.news.show', $news) }}" class="text-body">{{ $news->title }}</a>
                        </h6>
                        <div class="news-meta small text-muted">
                            <span><i class="far fa-clock me-1"></i>{{ jdate($news->published_at)->format('Y/m/d') }}</span><br>
                            <span><i class="far fa-user me-1"></i>{{ $news->author->name ?? 'نامشخص' }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            @if ($leftSliderNews->count() == 1)
            <div class="news-card d-flex shadow-sm rounded bg-light-subtle mb-2" style="height: 50%; opacity: 0.3;"></div>
            @endif
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="latest-news col-md-8 rounded">
        <h3 class="mb-4 pt-4 pe-4"><i class="fas fa-newspaper me-2"></i>پیشنهاد سردبیر</h3>
        {{-- {{ dd($bottomSliderNews->links()) }} --}}
        @foreach ($bottomSliderNews as $news)
        <div class="news-card row align-items-center shadow-sm rounded-3 p-3 mb-4">
            <div class="col-md-4">
                {{-- <img src="{{ asset('storage/' . $news->image) }}" class="img-fluid rounded-3 shadow-sm" alt="{{ $news->title }}"> --}}
                @if($news->media_type === 'image')
                    <img src="{{ asset('storage/' . $news->media_path) }}" class="img-fluid rounded-3 shadow-sm" alt="{{ $news->title }}" alt="{{ $news->title }}" />
                @elseif($news->media_type === 'video')
                    <img src="{{ asset('storage/' . $news->thumbnailVideo) }}" class="img-fluid rounded-3 shadow-sm" alt="{{ $news->title }}" alt="{{ $news->title }}" />
                    {{-- <iframe src="https://www.aparat.com/video/video/embed/videohash/{{ $news->media_path }}/vt/frame"
                            class="img-fluid rounded-3 shadow-sm" width="100" frameborder="0" allowfullscreen></iframe> --}}
                             {{-- <a href="{{ route('customer.news.show', $news) }}">
        <img src="https://www.aparat.com/video/video/show/thumbnail/{{ $news->media_path }}.jpg" alt="ویدیو"> --}}
    {{-- </a> --}}

    
                @endif
            </div>
            <div class="col-md-8">
                <a href="{{ route('customer.news.show', $news) }}">
                    <div class="news-content">
                        <span class="badge bg-danger mb-2">{{ $news->category->title }}</span>
                        <h5 class="news-title text-body">{{ $news->title }}</h5>
                        <p class="text-body" style="text-align: justify">
                            {{ Str::limit(trim(str_replace('&nbsp;', ' ', strip_tags($news->subtitle))), 250, '[...]') }}
                        </p>
                        <div class="news-meta mt-3">
                            <span class="news-date text-muted">
                                <i class="far fa-clock me-1"></i> {{ jdate($news->published_at)->format('Y/m/d') }}
                            </span>
                            <span class="news-author text-muted ms-3">
                                <i class="far fa-user me-1"></i> {{ $news->author->display_name ?? 'نامشخص' }}
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        @endforeach
        <div class="custom-pagination-container">
    @if ($bottomSliderNews->lastPage() > 1)
    <ul class="custom-pagination">
        {{-- دکمه قبلی --}}
        <li class="{{ ($bottomSliderNews->currentPage() == 1) ? 'disabled' : '' }}">
            <a href="{{ $bottomSliderNews->url($bottomSliderNews->currentPage() - 1) }}" aria-label="قبلی" rel="prev">&laquo;</a>
        </li>

        {{-- شماره صفحات --}}
        @for ($page = 1; $page <= $bottomSliderNews->lastPage(); $page++)
            <li class="{{ ($bottomSliderNews->currentPage() == $page) ? 'active' : '' }}">
                <a href="{{ $bottomSliderNews->url($page) }}">{{ $page }}</a>
            </li>
        @endfor

        {{-- دکمه بعدی --}}
        <li class="{{ ($bottomSliderNews->currentPage() == $bottomSliderNews->lastPage()) ? 'disabled' : '' }}">
            <a href="{{ $bottomSliderNews->url($bottomSliderNews->currentPage() + 1) }}" aria-label="بعدی" rel="next">&raquo;</a>
        </li>
    </ul>
    @endif
</div>

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

    </div>

    <div class="col-md-4">
        <section class="feedback-section sticky-top pb-4" style="top: 120px">
            @foreach($messages as $message)
            <div class="feedback-box p-3 shadow-sm rounded">
                <div class="user-message">
                    <h4 class="fw-bold">حرف مردم</h4>
                    <p>{{ $message->content }}</p>
                </div>
                <div class="admin-response mt-2">
                    <h5>پاسخ مسئول</h5>
                    <p>{{ $message->response }}</p>
                </div>
            </div>
            @endforeach
        </section>
    </div>
</div>

<section class="media-section mt-4">

    {{-- تصویر شاخص --}}
    <div class="media-box">
        <div class="media-header">تصویر</div>
        <div class="media-content">
            @if($media_image && $media_image->media_type === 'image')
                <img src="{{ asset('storage/' . $media_image->media_path) }}" alt="{{ $media_image->title }}">
            @else
                <p>تصویر شاخصی برای امروز یافت نشد</p>
            @endif
        </div>
    </div>

    {{-- ویدیو شاخص --}}
    <div class="media-box mt-4">
        <div class="media-header">فیلم</div>
        <div class="media-content">
            @if($media_video && $media_video->media_type === 'video' && $media_video->media_path)
                <iframe
                    src="https://www.aparat.com/video/video/embed/videohash/{{ $media_video->media_path }}/vt/frame"
                    frameborder="0"
                    allowfullscreen
                    style="width: 100%; aspect-ratio: 16/9;">
                </iframe>
            @else
                <p>فیلم شاخصی برای امروز یافت نشد</p>
            @endif
        </div>
    </div>

</section>


<style>
    [data-bs-theme="dark"] {
        color-scheme: dark;
    }

    [data-bs-theme="dark"] .news-card,
    [data-bs-theme="dark"] .media-box,
    [data-bs-theme="dark"] .media-header,
    [data-bs-theme="dark"] .feedback-box {
        background-color: #2c2f34 !important;
        color: #f1f1f1;
    }

    [data-bs-theme="dark"] .admin-response{
        background-color: #2c2f34 !important;
        color: #f1f1f1;
    }

    .slider-caption {
        background-color: rgba(0, 0, 0, 0.6);
        color: white;
        max-height: 45%;
        overflow: auto;
        font-size: 0.9rem;
        direction: rtl;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }

    .media-section {
        display: flex;
        flex-wrap: wrap;
        gap: 2rem;
        justify-content: center;
        align-items: stretch;
    }

    .media-box {
        flex: 1 1 45%;
        background-color: white;
        border-radius: 16px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .media-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    .media-header {
        font-size: 1.2rem;
        font-weight: bold;
        background-color: #eef2f7;
        border-bottom: 1px solid #ddd;
        text-align: center;
        padding: 0.5rem;
    }

    .media-content {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 250px;
        padding: 1rem;
    }

    .media-content img {
        max-width: 100%;
        max-height: 200px;
        border-radius: 12px;
    }

    @media (max-width: 768px) {
        .media-box {
            flex: 1 1 100%;
        }
    }

    .news-title {
        font-size: 1rem;
        font-weight: bold;
    }

    .news-meta span {
        font-size: 0.8rem;
    }
</style>
@endsection
