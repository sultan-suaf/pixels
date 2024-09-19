@extends('admin.layouts.app')

@section('panel')
    <div class="col-md-12">
        <div class="card b-radius--10">
            <div class="card-body p-0">
                <div class="table-responsive--sm table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('Sender Name')</th>
                                <th>@lang('Receiver Name')</th>
                                <th>@lang('Initiated')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($donations as $donation)
                                <tr>
                                    <td>{{ __($donation->sender->name) }} </td>
                                    <td>{{ __(@$donation->user->fullname) }}</br><span class="fw-bold"><a
                                                href="{{ appendQuery('search', @$donation->user->username) }}"><span>@</span>{{ @$donation->user->username }}</a></span>
                                    </td>
                                    <td>
                                        {{ showDateTime($donation->created_at) }}<br>{{ diffForHumans($donation->created_at) }}
                                    </td>
                                    <td>
                                        {{ showAmount($donation->amount) }} {{ gs('cur_text') }}
                                    </td>
                                    <td>
                                        @php echo $donation->statusBadge @endphp
                                    </td>

                                    <td>
                                        <a class="btn btn-sm btn-outline--primary ms-1" href="{{ route('admin.donation.detail', $donation->id) }}">
                                            <i class="la la-desktop"></i>@lang('Details')
                                        </a>

                                    </td>

                                </tr>


                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table><!-- table end -->
                </div>
            </div>
            @if ($donations->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($donations) }}
                </div>
            @endif
        </div><!-- card end -->
    </div>

    @push('breadcrumb-plugins')
        <x-search-form />
    @endpush
@endsection
