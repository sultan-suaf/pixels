"use strict";
let followLoginModal = $("#loginModal");
$(document).on('click', '.follow', function () {
    let followersRoute = $(this).data('followers_route');
    if (!followParams.loggedStatus) {
        followLoginModal.modal('show');
        return false;
    }
    let followingId = $(this).data('following_id');
    let followBtn = $(this);

    let data = {
        _token: followParams.csrfToken,
        following_id: followingId,
        status: 1,
        append_status: followParams.appendStatus
    }
    $.post(followRoutes.updateFollow, data, function (response) {
        if (response.error) {
            notify('error', response.error);
        } else {
            followBtn.addClass('unfollow active').removeClass('follow');
            followBtn.text('Unfollow');
            let memberNavFollowContainer = $(document).find('.user-profile__list .total-follower');
            let memberNavTotalFollower = parseInt(memberNavFollowContainer.text());

            if (memberNavFollowContainer.length) {
                memberNavTotalFollower += 1;
                memberNavFollowContainer.text(memberNavTotalFollower);
            }

            if (followParams.appendStatus) {
                let ul = $(document).find('.followers-ul');
                if (response.html) {
                    ul.html(response.html);
                }

                if (response.total_followers > 21) {
                    let followersDiv = $(document).find('.followers-div');
                    followersDiv.append(`<a href="${followersRoute}">see all</a>`);
                }
            }
        }
    });
});

$(document).on('click', '.unfollow', function () {
    if (!followParams.loggedStatus) {
        followLoginModal.modal('show');
        return false;
    }
    let followingId = $(this).data('following_id');
    let unfollowBtn = $(this);

    let data = {
        _token: followParams.csrfToken,
        following_id: followingId,
        status: 0,
        append_status: followParams.appendStatus
    }

    $.post(followRoutes.updateFollow, data, function (response) {
        if (response.error) {
            notify('error', response.error);
        } else {
            unfollowBtn.addClass('follow').removeClass('unfollow active');
            unfollowBtn.text('Follow');
            let memberNavFollowContainer = $(document).find('.user-profile__list .total-follower');
            let memberNavTotalFollower = parseInt(memberNavFollowContainer.text());

            if (memberNavFollowContainer.length) {
                memberNavTotalFollower -= 1;
                memberNavFollowContainer.text(memberNavTotalFollower);
            }

            if (followParams.appendStatus) {
                let ul = $(document).find('.followers-ul');
                if (response.html) {
                    ul.html(response.html);
                }
            }
            if (response.total_followers < 21) {
                let followersDiv = $(document).find('.followers-div');
                followersDiv.find('.follower-route').remove();
            }
        }
    });
});