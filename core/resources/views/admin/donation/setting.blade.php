@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 col-md-12 mb-30">
            <div class="card">
                <div class="card-body" id="generalCard">
                    <form action="" method="POST">
                        @csrf
                        <div class="row">

                            <div class="col-md-3 col-sm-6">
                                <div class="form-group">
                                    <label> @lang('Donation Item')</label>
                                    <input class="form-control" name="item" type="text" value="{{ @gs('donation_setting')?->item }}" required>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="form-group">
                                    <label> @lang('Subtitle') </label>
                                    <input class="form-control" name="subtitle" type="text" value="{{ @gs('donation_setting')?->subtitle }}" required>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="form-group">
                                    <label>@lang('Icon')</label>
                                    <div class="input-group">
                                        <input class="form-control iconPicker icon" name="icon" type="text"
                                            value="{{ @gs('donation_setting')?->icon }}" autocomplete="off" required>
                                        <span class="input-group-text input-group-addon" data-icon="las la-home" role="iconpicker"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-sm-6">
                                <div class="form-group">
                                    <label> @lang('Amount') </label>
                                    <div class="input-group">
                                        <input class="form-control" name="amount" type="number" value="{{ @gs('donation_setting')?->amount }}" required
                                            step="any">
                                        <span class="input-group-text">{{ gs('cur_text') }}</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="form-group">
                            <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                        </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    </div>
@endsection

@push('style-lib')
    <link href="{{ asset('assets/admin/css/fontawesome-iconpicker.min.css') }}" rel="stylesheet">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/fontawesome-iconpicker.js') }}"></script>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.iconPicker').iconpicker().on('iconpickerSelected', function(e) {
                $(this).closest('.form-group').find('.iconpicker-input').val(`<i class="${e.iconpickerValue}"></i>`);
            });
            $('.iconPicker').closest('.form-group').find('.input-group-text.input-group-addon').html(function() {
                return $(this).closest('.form-group').find('.iconPicker').val();
            });
        })(jQuery);
    </script>
@endpush
