"use strict";
let loginModal = $('#loginModal');
$(document).on('click', '.like-btn', function () {
    if (!likeParams.loggedStatus) {
        loginModal.modal('show');
        return false;
    }

    let imageId = $(this).data('image');
    let likeBtn = $(this);
    let data = {
        _token: likeParams.csrfToken,
        image: imageId
    };
    $.post(likeRoutes.updateLike, data, function (response) {
        if (response.error) {
            notify('error', response.error);
        } else {
            let likeContainer = likeBtn.parents('li').find('.gallery__like-num');
            let memberNavLikeContainer = $(document).find('.user-profile__list .total-like');
            likeBtn.addClass('unlike-btn').removeClass('like-btn');
            if (likeBtn.data('has_icon') == 1) {
                likeBtn.attr('data-bs-original-title', 'Unlike').tooltip('show');
                likeBtn.html(`<i class="las la-heart active"></i>`);
            } else {
                likeBtn.addClass('active');
                likeBtn.text("Unlike");
            }

            likeContainer.text(response.total_like);

            if (memberNavLikeContainer.length) {
                memberNavLikeContainer.text(response.user_total_like);
            }
        }
    });
});

$(document).on('click', '.unlike-btn', function () {
    if (!likeParams.loggedStatus) {
        loginModal.modal('show');
        return false;
    }

    let imageId = $(this).data('image');
    let likeBtn = $(this);
    let data = {
        _token: likeParams.csrfToken,
        image: imageId
    };
    $.post(likeRoutes.updateLike, data, function (response) {
        if (response.error) {
            notify('error', response.error);
        } else {
            let likeContainer = likeBtn.parents('li').find('.gallery__like-num');
            let memberNavLikeContainer = $(document).find('.user-profile__list .total-like');
            likeBtn.addClass('like-btn').removeClass('unlike-btn');
            if (likeBtn.data('has_icon') == 1) {
                likeBtn.attr('data-bs-original-title', 'like').tooltip('show');
                likeBtn.html(`<i class="lar la-heart"></i>`);
            } else {
                likeBtn.removeClass('active');
                likeBtn.text("Like");
            }

            likeContainer.text(response.total_like);
            if (memberNavLikeContainer.length) {
                memberNavLikeContainer.text(response.user_total_like);
            }
        }
    });
});