<div class="modal custom--modal fade login-modal" id="profileModal" data-bs-keyboard="false" tabindex="-1" aria-labelledby="profileModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="profileModalLabel">@lang('Update Profile')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('user.profile.update') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row gy-4">
                        <div class="col-md-6">
                            <label class="form-label">@lang('First Name')</label>
                            <input type="text" class="form-control form--control" name="firstname" value="{{ $user->firstname }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">@lang('Last Name')</label>
                            <input type="text" class="form-control form--control" name="lastname" value="{{ $user->lastname }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">@lang('E-mail Address')</label>
                            <input class="form-control form--control" value="{{ $user->email }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">@lang('Mobile Number')</label>
                            <input class="form-control form--control" value="{{ $user->mobile }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">@lang('Address')</label>
                            <input type="text" class="form-control form--control" name="address" value="{{ @$user->address }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">@lang('State')</label>
                            <input type="text" class="form-control form--control" name="state" value="{{ @$user->state }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">@lang('Zip Code')</label>
                            <input type="text" class="form-control form--control" name="zip" value="{{ @$user->zip }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">@lang('City')</label>
                            <input type="text" class="form-control form--control" name="city" value="{{ @$user->city }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">@lang('Country')</label>
                            <input class="form-control form--control" value="{{ @$user->country_name }}" disabled>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--base btn--lg w-100">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
</div>
