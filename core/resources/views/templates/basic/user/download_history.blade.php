@extends($activeTemplate . 'layouts.master')
@section('content')


    <div class="custom--table-container table-responsive--md">
        <table class="table custom--table">
            <thead>
                <tr>
                    <th class="sm-text">@lang('Image')</th>
                    <th class="sm-text">@lang('Category')</th>
                    <th class="sm-text">@lang('Last Download')</th>
                    <th class="sm-text">@lang('Action')</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($downloads as $key=>$download)
                <tr>
                        <td class="sm-text">
                            {{ __($download->imageFile->image->title) }} |
                            {{ __($download->imageFile->resolution) }} 
                           <div>
                            <span>- @lang('By')</span> <a href="{{ route('member.images', $download->contributor->username) }}">
                                 {{ $download->contributor->fullname }}
                            </a> 
                           </div>
                            
                        </td>
                        <td class="sm-text">
                            {{ __($download->imageFile->image->category->name) }}
                        </td>
                        <td class="sm-text">
                            {{ showDateTime($download->updated_at) }}
                        </td>

                        <td>
                            <a href="{{ route('user.image.download.file', $download->imageFile->id) }}" class="btn btn--base btn-sm">
                                <i class="las la-download"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center sm-text">{{ __($emptyMessage) }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($downloads->hasPages())
            <div class="mt-3 d-flex justify-content-end">
                {{ paginateLinks($downloads) }}
            </div>
        @endif


    </div>
@endsection
