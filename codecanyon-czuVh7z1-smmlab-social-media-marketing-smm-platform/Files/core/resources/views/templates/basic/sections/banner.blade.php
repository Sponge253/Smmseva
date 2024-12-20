@php
    $bannerContent = getContent('banner.content', true);
@endphp

<section class="banner-section">
    <div class="banner-element">
        <img src="{{ frontendImage('banner',@$bannerContent->data_values->image, '630x540') }}" alt="@lang('banner')">
    </div>
    <div class="banner-shape-one">
        <img src="{{ asset($activeTemplateTrue . 'images/banner/icon-1.png') }}" alt="@lang('shape')">
    </div>
    <div class="banner-shape-two">
        <img src="{{ asset($activeTemplateTrue . 'images/banner/icon-2.png') }}" alt="@lang('shape')">
    </div>
    <div class="banner-shape-three">
        <img src="{{ asset($activeTemplateTrue . 'images/banner/icon-3.png') }}" alt="@lang('shape')">
    </div>
    <div class="container">
        <figure class="figure highlight-background highlight-background--lean-right">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="1291px" height="480px">
                <defs>
                    <linearGradient id="PSgrad_0" x1="0%" x2="0%" y1="100%" y2="0%">
                        <stop offset="28%" stop-color="rgb(244,245,250)" stop-opacity="1" />
                        <stop offset="100%" stop-color="rgb(252,253,255)" stop-opacity="1" />
                    </linearGradient>

                </defs>
                <path fill-rule="evenodd" opacity="0.1" fill="rgb(0, 0, 0)" d="M480.084,0.001 L1290.991,0.001 L810.906,831.000 L-0.000,831.000 L480.084,0.001 Z" />
                <path fill="url(#PSgrad_0)" d="M480.084,0.001 L1290.991,0.001 L810.906,831.000 L-0.000,831.000 L480.084,0.001 Z" />
            </svg>
        </figure>
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="banner-content">
                    <h1 class="title">{{ __(@$bannerContent->data_values->heading) }}</h1>
                    <p>{{ __(@$bannerContent->data_values->sub_heading) }}</p>
                    <div class="banner-btn">
                        <a class="btn--base" href="{{ @$bannerContent->data_values->button_link }}">{{ __(@$bannerContent->data_values->button) }} <i class="las la-angle-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
