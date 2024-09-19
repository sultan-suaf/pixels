@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="d-flex justify-content-end mb-3">
        <button class="btn btn--base btn-sm addBtn" data-modal_title="@lang('Add Collection')">
            <i class="las la-plus"></i>
            @lang('Add Collection')
        </button>
    </div>
    <div class="custom--table-container table-responsive--md">
        <table class="custom--table table">
            <thead>
                <tr>
                    <th class="sm-text">@lang('Title')</th>
                    <th class="sm-text">@lang('Images')</th>
                    <th class="sm-text">@lang('Public / Private')</th>
                    <th class="sm-text text-center">@lang('Action')</th>
                </tr>
            </thead>
            <tbody>
                @forelse($collections as $collection)
                    <tr>
                        <td class="sm-text">
                            @if ($collection->images_count)
                                <a href="{{ route('collection.detail', [slug($collection->title), $collection->id]) }}"> {{ __($collection->title) }}</a>
                            @else
                                <span class="text--warning">
                                    {{ __($collection->title) }}
                                </span>
                            @endif
                        </td>

                        <td class="sm-text text-center">
                            {{ $collection->images_count }}
                        </td>
                        <td class="sm-text text-center">
                            @if ($collection->is_public)
                                @lang('Public')
                            @else
                                @lang('Private')
                            @endif
                        </td>

                        <td>
                            <div class="d-flex justify-content-center flex-wrap gap-1">
                                <button class="btn btn--base btn-sm editBtn" data-collection="{{ $collection }}"
                                    data-modal_title="@lang('Edit Collection') - {{ __($collection->title) }}">
                                    <i class="las la-pen"></i>
                                </button>
                                <button class="btn btn--danger btn-sm confirmationBtn"
                                    data-action="{{ route('user.collection.delete', $collection->id) }}" data-question="@lang('Are you sure to delete this collection ?')">
                                    <i class="las la-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="sm-text text-center" colspan="4">{{ __($emptyMessage) }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($collections->hasPages())
        <div class="mt-5">
            {{ paginateLinks($collections) }}
        </div>
    @endif


    <!-- edit Modal -->
    <div class="modal fade custom--modal" id="collectionModal" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
                </div>
                <form action="" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="row gy-3">
                            <div class="col-12">
                                <label class="form-label">@lang('Title')</label>
                                <input class="form-control form--control" name="title" type="text" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">@lang('Description')</label>
                                <textarea class="form-control form--control" name="description" rows="4"></textarea>
                            </div>
                            <div class="col-12 select2-parent">
                                <label class="form-label">@lang('Visibility')</label>
                                <div class="form--select">
                                    <select class="form-select select2-basic" data-minimum-results-for-search="-1" name="is_public" required>
                                        <option value="0">@lang('Private')</option>
                                        <option value="1">@lang('Public')</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn--base w-100 h-45" type="submit">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('script')
    <script>
        "use strict";
        let modal = $('#collectionModal');

        $('.editBtn').on('click', function() {
            let collection = $(this).data('collection');
            modal.find('form').attr('action', ` {{ route('user.collection.update', '') }}/${collection.id}`);
            modal.find('.modal-title').text($(this).data('modal_title'));
            modal.find('[name=title]').val(collection.title);
            modal.find('[name=description]').val(collection.description);
            modal.find('[name=is_public]').val(collection.is_public);
            modal.modal('show');
        });

        $('.addBtn').on('click', function() {
            modal.find('.modal-title').text($(this).data('modal_title'));
            modal.find('form').attr('action', "{{ route('user.collection.add') }}");
            modal.modal('show');
        });

        $('#collectionModal').on('hidden.bs.modal', function() {
            $(this).find('form').trigger("reset");
        })
    </script>
@endpush
