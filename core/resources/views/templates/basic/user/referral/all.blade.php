@php
    $maxLevel = 0;
@endphp
@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="card custom--card">
        <div class="card-body">
            <div class="form-group mb-4">
                <label class="d-flex justify-content-between form-label">
                    <span>@lang('Referral Link')</span>
                    @if (auth()->user()->referrer)
                        <span>@lang('You are referred by') <span class="fw-bold">{{ auth()->user()->referrer->fullname }}</span></span>
                    @endif
                </label>
                <div class="input-group">
                    <input type="text" name="text" class="form-control form--control referralURL"
                        value="{{ route('home') }}?reference={{ auth()->user()->username }}" readonly="">
                    <button class="input-group-text copytext copyBoard" id="copyBoard">@lang('Copy')</button>
                </div>
            </div>
            @if ($user->allReferrals->count() > 0)
                <label class="form-label">@lang('My Referrals')</label>
                <div class="treeview-container">
                    <ul class="treeview">
                        <li class="items-expanded"> {{ $user->fullname }} ( {{ $user->username }} )
                            @include($activeTemplate . 'partials.under_tree', ['user' => $user, 'layer' => 0, 'isFirst' => true])
                        </li>
                    </ul>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('style')
    <link href="{{ asset('assets/global/css/jquery.treeView.css') }}" rel="stylesheet" type="text/css">
@endpush

@push('script')
    <script src="{{ asset('assets/global/js/jquery.treeView.js') }}"></script>
    <script>
        "use strict";
        $('.treeview').treeView();
        $('.copyBoard').on("click", function() {
            var copyText = document.getElementsByClassName("referralURL");
            copyText = copyText[0];
            copyText.select();
            copyText.setSelectionRange(0, 99999);

            /*For mobile devices*/
            document.execCommand("copy");
            $(this).text('Copied');

            setTimeout(() => {
                $(this).text('Copy');
            }, 5000);
        });
    </script>
@endpush
