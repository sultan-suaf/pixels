@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @include($activeTemplate . 'partials.breadcrumb')
    <div class="section privacy-policy-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-10">
                    <div class="privacy-policy-section__content">
                        @php echo $policy->data_values->details  @endphp
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Privacy Policy End -->
@endsection
