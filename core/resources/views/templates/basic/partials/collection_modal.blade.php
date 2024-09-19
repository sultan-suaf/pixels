<div class="modal custom--modal fade" id="collectionModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog @if (Auth::check()) modal-lg @endif modal-dialog-centered">
        <div class="modal-content @if (Auth::check()) border-0 @endif">
            @if (!Auth::check())
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Add to collection')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-center">@lang('Please login first')</p>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('user.login') }}" class="btn btn--dark">@lang('Login')</a>
                </div>
            @else
                <div class="modal-body p-0">

                </div>
            @endif
        </div>
    </div>
</div>

@push('script')
    <script>
        (function($) {
            "use strict";

            // collection
            let modal = $('#collectionModal');
            let isChanged = false;

            $(document).on('click', '.collect-btn', function() {
                let checkLogged = @json(Auth::check());
                if (!checkLogged) {
                    modal.modal('show');
                    return false;
                }

                let data = {
                    image: $(this).data('image_id')
                };

                $.ajax({
                    type: "get",
                    url: "{{ route('user.collection.image.data') }}",
                    data: data,
                    success: function(response) {
                        if (response.html) {
                            modal.find('.modal-body').html(response.html);
                        }
                    }
                });
                setTimeout(() => {
                    modal.modal('show');
                }, 500);

            })

            $(document).on('click', '.add-collection', function() {
                let title = modal.find('[name=title]').val();
                if (!title) {
                    modal.find('[name=title]').addClass('border--danger');
                    notify('error', 'This field is required');
                    return false;
                }

                let data = {
                    _token: "{{ csrf_token() }}",
                    title: title
                };

                $.post("{{ route('user.collection.add') }}", data, function(response) {
                    if (response.error) {
                        notify('error', response.error);
                    } else {
                        modal.find('[name=title]').val('');
                        let collectionUl = modal.find('.collections');
                        collectionUl.prepend(`
                        <li class="list-group-item d-flex justify-content-between flex-wrap active select-collection " data-collection="${response.collection.id}">
                            <span class="left-side">${response.collection.title}</span>
                            <span class="right-side">
                                    <input type="hidden" name="collection[]" value="${response.collection.id}">
                                    <span class="hover-effect">remove</span>
                                    <span class="show"><i class="las la-check"></i></span>
                            </span>
                        </li>
                       `);
                    }
                });
            });

            modal.on('shown.bs.modal', function() {
                modal.find('form').on('submit', function(e) {
                    e.preventDefault();
                    let formData = new FormData(this);
                    const BUTTON = modal.find('button[type="submit"]');
                    $.ajax({
                        url: "{{ route('user.collection.image.add') }}",
                        data: formData,
                        processData: false,
                        contentType: false,
                        type: 'POST',
                        headers: {
                            'X-CSRF-Token': "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                $(document).find(`.collect-btn[data-image_id="${response.image}"]`).attr(
                                    'data-bs-original-title', `${response.tooltip}`);
                                BUTTON.html(`<i class="fas fa-check"></i>&nbsp;Submited`);

                                setTimeout(() => {
                                    modal.modal('hide');
                                }, 500);
                            } else {
                                BUTTON.html(`Submit`);
                                notify('error', response.error || `@lang('Something went wrong')`);
                            }
                        },
                        beforeSend: function() {
                            BUTTON.html(`<i class="fas fa-spinner"></i>&nbsp;Submitting`);
                        },
                        error: function() {
                            notify('error', `@lang('Something went wrong')`);
                            BUTTON.html(`@lang('Submit')`);
                        }

                    });
                });

                // press enter add colleciton
                let titleField = modal.find('[name=title]');
                $(titleField).keypress(function(event) {
                    var keycode = (event.keyCode ? event.keyCode : event.which);
                    if (keycode == '13') {
                        event.preventDefault();
                        modal.find('.add-collection').click();
                    }
                });
            })

            //select and unselect collection
            $(document).on('click', '.remove-collection', function() {
                $(this).find('[name="collection[]"]').remove();
                $(this).find('.hover-effect').html(`select`);
                $(this).addClass('select-collection').removeClass('active remove-collection');
                $(this).find('.show').html('');
            });

            $(document).on('click', '.select-collection', function() {
                let collectionId = $(this).data('collection');
                $(this).find('.right-side').prepend(`<input type="hidden" name="collection[]" value="${collectionId}">`);
                $(this).find('.hover-effect').html(`remove`);
                $(this).find('.show').html(`<i class="las la-check"></i>`);

                $(this).addClass('active remove-collection').removeClass('select-collection');
            });

        })(jQuery);
    </script>
@endpush
