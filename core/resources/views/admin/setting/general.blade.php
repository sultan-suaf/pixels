@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 col-md-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <form method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-xl-4 col-sm-6">
                                <div class="form-group ">
                                    <label> @lang('Site Title')</label>
                                    <input class="form-control" type="text" name="site_name" required value="{{ gs('site_name') }}">
                                </div>
                            </div>
                            <div class="col-xl-4 col-sm-6">
                                <div class="form-group ">
                                    <label>@lang('Currency')</label>
                                    <input class="form-control" type="text" name="cur_text" required value="{{ gs('cur_text') }}">
                                </div>
                            </div>
                            <div class="col-xl-4 col-sm-6">
                                <div class="form-group ">
                                    <label>@lang('Currency Symbol')</label>
                                    <input class="form-control" type="text" name="cur_sym" required value="{{ gs('cur_sym') }}">
                                </div>
                            </div>
                            <div class="form-group col-xl-4 col-sm-6">
                                <label class="required"> @lang('Timezone')</label>
                                <select class="select2 form-control" name="timezone">
                                    @foreach ($timezones as $key => $timezone)
                                        <option value="{{ @$key }}" @selected(@$key == $currentTimezone)>{{ __($timezone) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-xl-4 col-sm-6">
                                <label class="required"> @lang('Site Base Color')</label>
                                <div class="input-group">
                                    <span class="input-group-text p-0 border-0">
                                        <input type='text' class="form-control colorPicker" value="{{ gs('base_color') }}">
                                    </span>
                                    <input type="text" class="form-control colorCode" name="base_color" value="{{ gs('base_color') }}">
                                </div>
                            </div>

                            <div class="form-group col-xl-4 col-sm-6">
                                <label> @lang('Record to Display Per page')</label>
                                <select class="select2 form-control" name="paginate_number" data-minimum-results-for-search="-1">
                                    <option value="20" @selected(gs('paginate_number') == 20)>@lang('20 items per page')</option>
                                    <option value="50" @selected(gs('paginate_number') == 50)>@lang('50 items per page')</option>
                                    <option value="100" @selected(gs('paginate_number') == 100)>@lang('100 items per page')</option>
                                </select>
                            </div>

                            <div class="form-group col-xl-4 col-sm-6 ">
                                <label class="required"> @lang('Currency Showing Format')</label>
                                <select class="select2 form-control" name="currency_format" data-minimum-results-for-search="-1">
                                    <option value="1" @selected(gs('currency_format') == Status::CUR_BOTH)>@lang('Show Currency Text and Symbol Both')</option>
                                    <option value="2" @selected(gs('currency_format') == Status::CUR_TEXT)>@lang('Show Currency Text Only')</option>
                                    <option value="3" @selected(gs('currency_format') == Status::CUR_SYM)>@lang('Show Currency Symbol Only')</option>
                                </select>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label data-bs-toggle="tooltip" title="@lang('Maximum price for image upload, while contributor uploading any resources, they can\'t cross this maximum price')">@lang('Image Maximum Price') <i
                                            class="las la-info-circle text--info"></i></label>
                                    <div class="input-group">
                                        <input class="form-control" name="image_maximum_price" type="number"
                                            value="{{ getAmount(gs('image_maximum_price')) }}" min="0" required step="any">
                                        <span class="input-group-text">{{ __(gs('cur_text')) }}</span>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label data-bs-toggle="tooltip" title="@lang('-1 for unlimited resources upload')"> @lang('Per day Resource Upload limit') <i
                                            class="las la-info-circle text--info"></i></label>
                                    <input class="form-control" name="upload_limit" type="number" value="{{ gs('upload_limit') }}" required
                                        step="any">
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label data-bs-toggle="tooltip" title="@lang('Contributor\'s commission in each premium resource download')"> @lang('Contributor\'s Commission') <i
                                            class="las la-info-circle text--info"></i></label>
                                    <div class="input-group">
                                        <input class="form-control" name="per_download" type="number" value="{{ getAmount(gs('per_download')) }}"
                                            required step="any">
                                        <span class="input-group-text">
                                            %
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label> @lang('Select Banner')</label>
                                    <select name="banner" class="form-control select2" data-minimum-results-for-search="-1">
                                        <option value="{{ Status::BANNER }}" @selected(gs('banner') == Status::BANNER)>@lang('Banner')</option>
                                        <option value="{{ Status::BANNER_TWO }}" @selected(gs('banner') == Status::BANNER_TWO)>@lang('Banner Two')</option>
                                    </select>
                                </div>
                            </div>
                            @if (gs('referral_system'))
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label data-bs-toggle="tooltip" title="@lang('Referral Commission in each plan purchased')"> @lang('Referral Commission') <i
                                                class="las la-info-circle text--info"></i></label>
                                        <div class="input-group">
                                            <input class="form-control" name="referral_commission" type="number"
                                                value="{{ gs('referral_commission') }}" required step="any">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="col-12">
                                <div class="form-group">
                                    <label> @lang('Global Ad Script')</label>
                                    <textarea name="ads_script" class="form-control" name="ads_script">{{ gs('ads_script') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-none-30 justify-content-center mt-5">
        <div class="col-lg-6 col-md-12 mb-30">
            <div class="card">
                <div class="card-header">
                    <h5>@lang('Upload Instructions')</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.instruction') }}" enctype="multipart/form-data" method="POST">
                        @csrf
                        <div class="form-group">
                            <label> @lang('Heading') </label>
                            <input class="form-control" name="heading" type="text" value="{{ @gs('instruction')->heading }}" required>
                        </div>
                        <div class="form-group">
                            <label> @lang('Instruction') </label>
                            <textarea class="form-control" required name="instruction" rows="5">{{ @gs('instruction')->instruction }}</textarea>
                        </div>

                        <div class="form-group">
                            <label> @lang('Instruction file') (@lang('Please insert any  .txt file')) </label>
                            <input class="form-control" name="txt" type="file" type="text" accept="text/plain">
                        </div>

                        <div class="form-group">
                            <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-12 mb-30">
            <div class="card h-100">
                <div class="card-header">
                    <h5>@lang('Upload watermark Image')</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.watermark') }}" enctype="multipart/form-data" method="POST">
                        @csrf

                        <div class="form-group">
                            <x-image-uploader class="w-100 exclude" name="watermark" type="watermark"
                                imagePath="{{ getImage('assets/images/watermark.png') }}" />
                        </div>

                        <div class="form-group">
                            <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('script-lib')
    <script src="{{ asset('assets/admin/js/spectrum.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/spectrum.css') }}">
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";


            $('.colorPicker').spectrum({
                color: $(this).data('color'),
                change: function(color) {
                    $(this).parent().siblings('.colorCode').val(color.toHexString().replace(/^#?/, ''));
                }
            });

            $('.colorCode').on('input', function() {
                var clr = $(this).val();
                $(this).parents('.input-group').find('.colorPicker').spectrum({
                    color: clr,
                });
            });
        })(jQuery);
    </script>
@endpush
