<div class="modal custom--modal fade" id="shareModal" data-bs-keyboard="false" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="shareModalLabel">@lang('Share')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="list list--row social-list social-list--xl mb-3 justify-content-center py-3">
                    <li>
                        <a target="_blank" href="" class="t-link social-list__icon facebook-link">
                            <i class="lab la-facebook-f"></i>
                        </a>
                    </li>
                    <li>
                        <a target="_blank" href="" class="t-link social-list__icon twitter-link">
                            <i class="lab la-twitter"></i>
                        </a>
                    </li>
                    <li>
                        <a target="_blank" href="" class="t-link social-list__icon linkedin-link">
                            <i class="lab la-linkedin-in"></i>
                        </a>
                    </li>
                    <li>
                        <a target="_blank" href="" class="t-link social-list__icon pinterest-link">
                            <i class="lab la-pinterest-p"></i>
                        </a>
                    </li>
                </ul>

                <div class="share-group">
                    <div class="copy-link">
                        <input type="text" class="copyURL" value="" readonly="">
                        <span class="copyBoard" id="copyBtn"><i class="las la-copy"></i> <strong class="copyText">@lang('Copy')</strong></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
        (function($) {
            "use strict";

            // collection
            let modal = $('#shareModal');

            $(document).on('click', '.share-btn', function() {
                let route = $(this).data('route');
                let title = $(this).data('image_title');
                let urlLenCode = $(this).data('url_len_code');

                modal.find('.facebook-link').attr('href', `https://www.facebook.com/sharer/sharer.php?u=${urlLenCode}`);
                modal.find('.twitter-link').attr('href', `https://twitter.com/intent/tweet?text=${title}&amp;url=${urlLenCode}`);
                modal.find('.linkedin-link').attr('href',
                    `http://www.linkedin.com/shareArticle?mini=true&amp;url=${urlLenCode}&amp;title=${title}&amp;summary=${title}`);
                modal.find('.pinterest-link').attr('href', `http://pinterest.com/pin/create/button/?url=${urlLenCode}&description=${title}`);
                modal.find('.copyURL').val(route);
                modal.modal('show');
            });


            modal.on('shown.bs.modal', function() {
                $('.copyBoard').on("click", function() {
                    var copyText = document.getElementsByClassName("copyURL");
                    copyText = copyText[0];
                    copyText.select();
                    copyText.setSelectionRange(0, 99999);

                    /*For mobile devices*/
                    document.execCommand("copy");
                    $('.copyText').text('Copied');
                    setTimeout(() => {
                        $('.copyText').text('Copy');
                    }, 2000);
                });
            })

        })(jQuery);
    </script>
@endpush
