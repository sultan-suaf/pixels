@extends('admin.layouts.app')

@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 col-md-12 mb-30">
            <div class="card">
                <div class="card-body">

                    <form action="" method="POST">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-12">
                                <div class="alert alert-danger p-3">
                                    @lang('Please Remember, Be very carefull about changing storage or changing FTP host,  Because if you change setting, make sure you copy all image and file directory of uploaded photos to your new FTP or LOCAL storage. Otherwise photos won\'t be shown to the site.   e.g: Change LOCAL To FTP,  then copy all your directory of images ("images" and "files") to your FTP directory and FTP to LOCAL ( assets/images/stock/image and assets/images/stock/file)')
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label class="form-label">@lang('Select Upload Storage')</label>
                                <select class="form-control select2" data-minimum-results-for-search="-1" name="storage_type">
                                    <option value="1" @selected(gs('storage_type') == 1)>@lang('Local Storage')</option>
                                    <option value="2" @selected(gs('storage_type') == 2)>@lang('FTP Storage')</option>
                                    <option value="3" @selected(gs('storage_type') == 3)>@lang('Wasabi Storage')</option>
                                    <option value="4" @selected(gs('storage_type') == 4)>@lang('Digital Ocean')</option>
                                    <option value="5" @selected(gs('storage_type') == 5)>@lang('Vultr')</option>
                                </select>
                            </div>
                        </div>

                        <div class="row config">
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button class="btn btn--primary w-100 h-45" type="submit">@lang('Update')</button>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $(function() {
            "use strict";
            $('select[name=storage_type]').on('change', function() {
                var val = $(this).val();
                if (val == 1) {
                    $('.config').children().remove();
                } else if (val == 2) {
                    var ftp = `<div class="col-md-4">
                                <div class="form-group">
                                    <label class="required">@lang('FTP Hosting Root Access Path')</label>
                                    <input class="form-control" type="text" name="ftp[host_domain]" placeholder="@lang('https://yourdomain.com/foldername')"
                                           value="{{ @gs('ftp->host_domain') }}" required>
                                    <small class="text-danger">@lang('https://yourdomain.com/foldername')</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="required"> @lang('Host')</label>
                                    <input class="form-control" type="text" name="ftp[host]" placeholder="@lang('Host')"
                                           value="{{ @gs('ftp->host ')}}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="required">@lang('Username')</label>
                                    <input class="form-control" type="text" name="ftp[username]" placeholder="@lang('Username')"
                                           value="{{gs('ftp->username') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="required">@lang('Password')</label>
                                    <input class="form-control" type="text" name="ftp[password]" placeholder="@lang('Password')"
                                           value="{{ @gs('ftp')->password }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="required">@lang('Port')</label>
                                    <input class="form-control" type="text" name="ftp[port]" placeholder="@lang('Port')"
                                           value="{{ @gs('ftp')->port }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="required">@lang('Upload Root Folder')</label>
                                    <input class="form-control" type="text" name="ftp[root_path]" placeholder="@lang('/html_public/something')" value="{{ @gs('ftp')->root_path }}" required>
                                </div>
                            </div>`;
                    $('.config').html(ftp);
                } else if (val == 3) {
                    var wasabi = `<div class="col-md-4">
                            <div class="form-group">
                                <label class="required">@lang('Driver')</label>
                                <input class="form-control" type="text" name="wasabi[driver]" value="{{ @gs('wasabi')->driver }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="required"> @lang('Key')</label>
                                <input class="form-control" type="text" name="wasabi[key]" value="{{ @gs('wasabi')->key }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="required">@lang('Secret')</label>
                                <input class="form-control" type="text" name="wasabi[secret]" value="{{ @gs('wasabi')->secret }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="required">@lang('Region')</label>
                                <input class="form-control" type="text" name="wasabi[region]" value="{{ @gs('wasabi')->region }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="required">@lang('Bucket')</label>
                                <input class="form-control" type="text" name="wasabi[bucket]" value="{{ @gs('wasabi')->bucket }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="required">@lang('Endpoint')</label>
                                <input class="form-control" type="text" name="wasabi[endpoint]" value="{{ @gs('wasabi')->endpoint }}" required>
                            </div>
                        </div>`;
                    $('.config').html(wasabi);
                } else if (val == 4) {
                    var digitalOcean = `<div class="col-md-4">
                            <div class="form-group">
                                <label class="required">@lang('Driver')</label>
                                <input class="form-control" type="text" name="digital_ocean[driver]" value="{{ @gs('digital_ocean')->driver }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="required"> @lang('Key')</label>
                                <input class="form-control" type="text" name="digital_ocean[key]" value="{{ @gs('digital_ocean')->key }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="required">@lang('Secret')</label>
                                <input class="form-control" type="text" name="digital_ocean[secret]" value="{{ @gs('digital_ocean')->secret }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="required">@lang('Region')</label>
                                <input class="form-control" type="text" name="digital_ocean[region]" value="{{ @gs('digital_ocean')->region }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="required">@lang('Bucket')</label>
                                <input class="form-control" type="text" name="digital_ocean[bucket]" value="{{ @gs('digital_ocean')->bucket }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="required">@lang('Endpoint')</label>
                                <input class="form-control" type="text" name="digital_ocean[endpoint]" value="{{ @gs('digital_ocean')->endpoint }}" required>
                            </div>
                        </div>`;
                    $('.config').html(digitalOcean);
                } else if (val == 5) {
                    var vultr = `<div class="col-md-4">
                            <div class="form-group">
                                <label class="required">@lang('Driver')</label>
                                <input class="form-control" type="text" name="vultr[driver]" value="{{ @gs('vultr')->driver }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="required"> @lang('Key')</label>
                                <input class="form-control" type="text" name="vultr[key]" value="{{ @gs('vultr')->key }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="required">@lang('Secret')</label>
                                <input class="form-control" type="text" name="vultr[secret]" value="{{ @gs('vultr')->secret }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="required">@lang('Region')</label>
                                <input class="form-control" type="text" name="vultr[region]" value="{{ @gs('vultr')->region }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="required">@lang('Bucket')</label>
                                <input class="form-control" type="text" name="vultr[bucket]" value="{{ @gs('vultr')->bucket }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="required">@lang('Endpoint')</label>
                                <input class="form-control" type="text" name="vultr[endpoint]" value="{{ @gs('vultr')->endpoint }}" required>
                            </div>
                        </div>`;
                    $('.config').html(vultr);
                }
            }).change();
        });
    </script>
@endpush
