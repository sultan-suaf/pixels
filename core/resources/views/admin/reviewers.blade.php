@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-md-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('S.N')</th>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Username')</th>
                                    <th>@lang('Email')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reviewers as $reviewer)
                                    <tr>
                                        <td>{{ $reviewers->firstItem() + $loop->index }}</td>
                                        <td>{{ __($reviewer->name) }}</td>
                                        <td>
                                            {{ $reviewer->username }}
                                        </td>
                                        <td>
                                            {{ $reviewer->email }}
                                        </td>
                                        <td>
                                            @php echo $reviewer->statusBadge @endphp
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline--dark" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="las la-ellipsis-v"></i>@lang('Action')</button>

                                            <div class="dropdown-menu">
                                                <a href="javascript:void(0)" class="dropdown-item cuModalBtn editBtn" data-modal_title="@lang('Update Reason')"
                                                    data-resource="{{ $reviewer }}"><i class="las la-pencil-alt"></i> @lang('Edit')</a>

                                                @if ($reviewer->status == 1)
                                                    <a href="javascript:void(0)" class="dropdown-item confirmationBtn" data-question="@lang('Are you sure, you want to banned this reviewer?')"
                                                        data-action="{{ route('admin.reviewers.status', $reviewer->id) }}"><i class="las la-ban"></i>
                                                        @lang('Ban')</a>

                                                    <a href="{{ route('admin.reviewers.login', $reviewer->id) }}" class="dropdown-item" target="_blank"><i
                                                            class="las la-sign-in-alt"></i> @lang('Login')</a>
                                                @else
                                                    <a href="javascript:void(0)" class="dropdown-item confirmationBtn" data-question="@lang('Are you sure, you want to banned this reviewer?')"
                                                        data-action="{{ route('admin.reviewers.status', $reviewer->id) }}"><i
                                                            class="las la-check-circle"></i> @lang('Unban')</a>
                                                @endif

                                            </div>

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($reviewers->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($reviewers) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="cuModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Report & Request')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form method="post" action="{{ route('admin.reviewers.save') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Name')</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('Username')</label>
                            <input type="text" name="username" class="form-control" value="{{ old('username') }}" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('Email')</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        </div>
                        <div class="form-group">
                            <div class="d-flex justify-content-between">
                                <label>@lang('Password')</label>
                                <span class="generatePassword text--primary">@lang('Generate Password')</span>
                            </div>
                            <input type="text" id="password" name="password" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <button class="btn btn-outline--primary btn-sm cuModalBtn addBtn" data-modal_title="@lang('Add Reason')"><i
            class="las la-plus"></i>@lang('Add New')</button>
@endpush

@push('style')
    <style>
        .generatePassword {
            cursor: pointer;
        }

        .body-wrapper,
        .table-responsive {
            overflow: unset !important;
        }

        .dropdown-menu {
            padding: 0 0;
        }

        .dropdown-menu .show {
            position: absolute;
            inset: 0px 0px auto auto;
            margin: 0px;
            transform: translate(-24.9688px, 54px);
        }
    </style>
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {
            $('.editBtn').on('click', function() {
                $('#password').val('');
                $('#password').attr('required', false);
                $('#password').parents('.form-group').find('label').removeClass('required');

                $('#confirmPassword').attr('required', false);
                $('#confirmPassword').parents('.form-group').find('label').removeClass('required');
            });

            $('.addBtn').on('click', function() {
                $('#password').attr('required', true);
                $('#password').parents('.form-group').find('label').addClass('required');

                $('#confirmPassword').attr('required', true);
                $('#confirmPassword').parents('.form-group').find('label').addClass('required');
            });

            $('.generatePassword').on('click', function() {
                var password = generatePassword(8);
                $('#password').val(password);
            });

            function generatePassword(passwordLength) {
                var numberChars = "0123456789";
                var upperChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
                var lowerChars = "abcdefghijklmnopqrstuvwxyz";
                var specialChars = "!#$%&()*+,-./:;<=>?@[\]^_`{|}~";
                var allChars = numberChars + upperChars + lowerChars + specialChars;
                var randPasswordArray = Array(passwordLength);

                randPasswordArray[0] = numberChars;
                randPasswordArray[1] = upperChars;
                randPasswordArray[2] = lowerChars;
                randPasswordArray[3] = specialChars;
                randPasswordArray = randPasswordArray.fill(allChars, 4);

                return shuffleArray(randPasswordArray.map(function(x) {
                    return x[Math.floor(Math.random() * x.length)]
                })).join('');
            }

            function shuffleArray(array) {
                for (var i = array.length - 1; i > 0; i--) {
                    var j = Math.floor(Math.random() * (i + 1));
                    var temp = array[i];
                    array[i] = array[j];
                    array[j] = temp;
                }
                return array;
            }
        })(jQuery);
    </script>
@endpush
