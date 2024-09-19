@php
    $cookie = App\Models\Frontend::where('data_keys', 'cookie.data')->first();
@endphp

@if ($cookie->data_values->status == 1 && !\Cookie::get('gdpr_cookie'))
    <div class="cookies-card hide text-center">
        <div class="cookies-card__icon bg--base">
            <i class="las la-cookie-bite"></i>
        </div>
        <p class="cookies-card__content mt-4">{{ $cookie->data_values->short_desc }} <a href="{{ route('cookie.policy') }}"
                target="_blank">@lang('learn more')</a></p>
        <div class="cookies-card__btn mt-4">
            <button class="btn btn--base w-100 policy" type="button">@lang('Allow')</button>
        </div>
    </div>
@endif

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.policy').on('click', function() {
                $.get('{{ route('cookie.accept') }}', function(response) {
                    $('.cookies-card').addClass('d-none');
                });
            });

            setTimeout(function() {
                $('.cookies-card').removeClass('hide')
            }, 2000);

        })(jQuery);
    </script>
@endpush
