@extends($activeTemplate . 'layouts.master')
@section('content')


    <div class="custom--table-container table-responsive--md">
        <table class="table custom--table">
            <thead>
                <tr>
                    <th class="text-center sm-text">@lang('Sl.')</th>
                    <th class="text-center sm-text">@lang('Donor Name')</th>
                    <th class="text-center sm-text">@lang('Amount')</th>
                    <th class="text-center sm-text">@lang('Received at')</th>
                </tr>
            </thead>
            <tbody>
                @forelse($donations as $donation)
                    <tr>

                        <td class="text-center sm-text">
                            {{ $donations->firstItem() + $loop->index }} 
                          </td>
                   
                        <td class="text-center sm-text">
                          {{ @$donation->sender->name }} 
                        </td>
                        <td class="text-center sm-text">
                           {{ showAmount($donation->amount) }}
                        </td>
                        <td class="text-center sm-text">
                            {{ showDateTime($donation->created_at) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center sm-text">{{ __($emptyMessage) }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($donations->hasPages())
        <div class="mt-3">
            {{ paginateLinks($donations) }}
        </div>
    @endif

 
  
@endsection


