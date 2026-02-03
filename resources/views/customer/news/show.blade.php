@extends('customer.layouts.master-one-col')

@section('head-tag')
<link rel="stylesheet" href="{{ asset('assets/css/style-show.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/comments-style.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
@endsection

@section('content')

<div class="container my-4">

    {{-- خبر --}}
    <div class="row justify-content-center">
        <div class="col-12 col-md-8">
            <section class="feedback-section mb-3">
                <div class="card">
                    <div class="card-body p-0">
                        {{-- تصویر یا ویدئو --}}
                        <div class="mb-3">
                            @if($news->media_type === 'image')
                                <img src="{{ asset('storage/' . $news->media_path) }}" class="w-100 rounded" alt="{{ $news->title }}">
                            @elseif($news->media_type === 'video')
                                <div class="ratio ratio-16x9">
                                    <iframe src="https://www.aparat.com/video/video/embed/videohash/{{ $news->media_path }}/vt/frame" frameborder="0" allowfullscreen></iframe>
                                </div>
                            @endif
                        </div>

                        {{-- اطلاعات خبر --}}
                        <div class="px-3 mb-3 d-flex flex-wrap align-items-center text-muted small">
                            <div class="me-3 mb-2 d-flex align-items-center">
                                <i class="far fa-calendar me-1"></i> {{ jdate($news->published_at)->format('%d %B %Y') }}
                                <span class="ms-3"><i class="far fa-user me-1"></i> {{ $news->author->display_name ?? 'نامشخص' }}</span>
                            </div>
                        </div>

                        {{-- محتوا --}}
                        <div class="px-3 pb-3">
                            <span class="news-pretitle text-muted">{{ $news->on_titr }}</span>
                            <h1 class="news-title">{{ $news->title }}</h1>
                            <p class="news-subtitle">{{ $news->subtitle }}</p>
                            <div class="news-body">
                                {!! preg_replace('/<figcaption[^>]*>.*?<\/figcaption>/is', '', $news->body) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- لینک کوتاه --}}
            <section class="feedback-section mb-3">
                <div class="card p-3">
                    @php
                        $shortUrl = route('short.redirect', ['code' => $news->short_link]);
                    @endphp
                    <h5>لینک کوتاه خبر</h5>
                    <small id="copyMessage" class="text-success ms-2" style="display:none;">کپی شد ✅</small>
                    <span id="shortLinkText" class="text-primary" style="cursor:pointer; direction:ltr;" onclick="copyShortLink()">
                        {{ $shortUrl }}
                    </span>
                </div>
            </section>

            {{-- برچسب‌ها --}}
            <section class="feedback-section mb-3">
                <div class="card p-3">
                    <h5>برچسب‌ها</h5>
                    @if($news->tags && $news->tags->count())
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($news->tags as $tag)
                                <a href="{{ route('customer.news.tags', $tag->name) }}" class="badge bg-primary text-white text-decoration-none px-3 py-1">
                                    {{ $tag->name }}
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">بدون برچسب</p>
                    @endif
                </div>
            </section>

            {{-- فرم و نظرات --}}
            <section class="feedback-section mb-3">
                {{-- فرم ارسال نظر --}}
                <div class="card mb-4" id="comment-form">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-edit me-2"></i><span id="form-title">نظر جدید</span></h5>
                        <div id="reply-info" class="d-none">
                            <span>در حال پاسخ به نظر <strong id="reply-to-name"></strong></span>
                            <button type="button" id="cancel-reply" class="btn btn-sm btn-outline-secondary ms-2">
                                <i class="fas fa-times"></i> لغو
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('customer.comments') }}" id="commentForm" class="needs-validation" novalidate>
                            @csrf
                            <input type="hidden" name="news_id" value="{{ $news->id }}">
                            <input type="hidden" name="parent_id" id="parent_id">

                            <div class="row mb-3">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label for="author" class="form-label">نام شما *</label>
                                    <input type="text" id="author" name="author" class="form-control" placeholder="نام و نام خانوادگی" required>
                                    <div class="invalid-feedback">لطفاً نام خود را وارد کنید.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">ایمیل (اختیاری)</label>
                                    <input type="email" id="email" name="email" class="form-control" placeholder="your@email.com">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="comment" class="form-label">متن نظر *</label>
                                <textarea id="comment" name="content" class="form-control" placeholder="نظر خود را بنویسید..." rows="4" maxlength="500" required></textarea>
                                <div class="form-text">
                                    <span id="char-count">0</span> / 500 کاراکتر
                                </div>
                                <div class="invalid-feedback">نظر نمی‌تواند خالی باشد.</div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted"><i class="fas fa-info-circle me-1"></i> نظرات پس از تأیید مدیر نمایش داده می‌شوند</small>
                                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-1"></i> ارسال نظر</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- لیست نظرات --}}
                <div id="comments-list">
                    @if ($news->comments->isEmpty())
                        <p class="text-center text-muted">اولین نفری باشید که نظر خود را ثبت می‌کند.</p>
                    @endif

                    @foreach ($news->comments as $index => $comment)
                        <div class="card mb-3" data-id="{{ $comment->id }}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-secondary text-white d-flex justify-content-center align-items-center me-3" style="width:40px; height:40px;">
                                            {{ mb_substr($comment->author, 0, 1) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $comment->author }}</h6>
                                            <small class="text-muted">{{ $comment->created_at->format('Y/m/d - H:i') }}</small>
                                        </div>
                                    </div>
                                    <span class="text-muted">#{{ $index + 1 }}</span>
                                </div>
                                <p class="mt-2">{{ $comment->content }}</p>

                                @if ($comment->admin_content)
                                    <div class="card mt-3 bg-light">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center me-2" style="width:30px; height:30px;">M</div>
                                                <div>
                                                    <h6 class="mb-0">مدیر سایت</h6>
                                                    <small class="badge bg-primary">{{ $comment->is_admin ? 'پاسخ مسئول' : 'پاسخ' }}</small>
                                                </div>
                                            </div>
                                            <p class="mb-0">{{ $comment->admin_content }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

        </div>

        {{-- Sidebar --}}
        @include('customer.news.sidebar')

    </div>
</div>

@endsection

@section('scripts')
<script>
    // شمارنده کاراکتر
    const commentInput = document.getElementById('comment');
    const charCount = document.getElementById('char-count');
    commentInput.addEventListener('input', () => {
        charCount.textContent = commentInput.value.length;
    });

    // پاسخ به کامنت
    document.querySelectorAll('.btn-reply').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('parent_id').value = btn.dataset.id;
            document.getElementById('form-title').innerText = 'پاسخ به ' + btn.dataset.author;
            document.getElementById('reply-info').classList.remove('d-none');
        });
    });

    // لغو پاسخ
    document.getElementById('cancel-reply')?.addEventListener('click', () => {
        document.getElementById('parent_id').value = '';
        document.getElementById('form-title').innerText = 'نظر جدید';
        document.getElementById('reply-info').classList.add('d-none');
    });

    // کپی لینک کوتاه
    function copyShortLink() {
        const text = document.getElementById("shortLinkText").innerText;
        navigator.clipboard.writeText(text).then(() => {
            const msg = document.getElementById("copyMessage");
            msg.style.display = "inline";
            setTimeout(() => msg.style.display = "none", 2000);
        }).catch(() => alert("خطا در کپی کردن لینک!"));
    }
</script>
@endsection
