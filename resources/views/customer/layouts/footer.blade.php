<div class="row">
    <!-- ستون درباره ما -->
    <div class="col-md-4 mb-3">
        <h5 class="font-vazir-bold">درباره ما</h5>
        <p class="small">
           {{ $setting->footer_about ?? null }}
        </p>
    </div>

    <!-- ستون لینک های سریع -->
    <div class="col-md-3 mb-3">
        <h5 class="font-vazir-bold">لینک‌های سریع</h5>
        <ul class="list-unstyled">
            <li><a href="#" class="footer-link">خانه</a></li>
            <li><a href="#" class="footer-link">آرشیو</a></li>
            <li><a href="#" class="footer-link">درباره ما</a></li>
            <li><a href="#" class="footer-link">تماس با ما</a></li>
        </ul>
    </div>

    <!-- ستون شبکه های اجتماعی -->
    <div class="col-md-3 mb-3">
        <h5 class="font-vazir-bold">ما را دنبال کنید</h5>
        <div>
            <ul class="list-unstyled">
                @foreach($socials as $social)
                    <li>
                        <a href="{{ $social['url'] }}" target="_blank" class="footer-link" rel="noopener noreferrer">
                            {{ $social['platform'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
            {{-- <a href="#" class="footer-link me-3 fs-4"><i class="bi bi-instagram"></i></a>
            <a href="#" class="footer-link me-3 fs-4"><i class="bi bi-telegram">باشگاه خبرنگاران
                    جوان</i></a>
            <a href="#" class="footer-link me-3 fs-4"><i class="bi bi-twitter"></i></a>
            <a href="#" class="footer-link fs-4"><i class="bi bi-github"></i></a> --}}
        </div>
    </div>

        <div class="col-md-2 mb-3">
        <h5 class="font-vazir-bold">مجوزها</h5>
        <div>
            
        </div>
    </div>
</div>

<hr class="footer-divider">

<div class="text-center small footer-text">
    © 2025 تمامی حقوق محفوظ است.
</div>


<style>

    .footer {
        background-color: #212529;
        /* dark background */
        color: #f8f9fa;
        /* light text */
    }

    .footer-link {
        color: #f8f9fa;
        text-decoration: none;
    }

    .footer-link:hover {
        text-decoration: underline;
    }

    .footer-divider {
        border-color: #f8f9fa;
    }

    .footer-text {
        color: #f8f9fa;
    }

    /* در حالت تم روشن */
    body.light-theme .footer {
        background-color: #f8f9fa;
        /* light background */
        color: #212529;
        /* dark text */
    }

    body.light-theme .footer-link {
        color: #212529;
    }

    body.light-theme .footer-divider {
        border-color: #212529;
    }

    body.light-theme .footer-text {
        color: #212529;
    }

</style>
