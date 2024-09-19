<?php

use Illuminate\Support\Facades\Route;

Route::get('/clear', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
});

// User Support Ticket
Route::controller('TicketController')->prefix('ticket')->name('ticket.')->group(function () {
    Route::get('/', 'supportTicket')->name('index');
    Route::get('new', 'openSupportTicket')->name('open');
    Route::post('create', 'storeSupportTicket')->name('store');
    Route::get('view/{ticket}', 'viewTicket')->name('view');
    Route::post('reply/{id}', 'replyTicket')->name('reply');
    Route::post('close/{id}', 'closeTicket')->name('close');
    Route::get('download/{attachment_id}', 'ticketDownload')->name('download');
});

//image
Route::controller('ImageController')->name('image.')->prefix('image')->group(function () {
    Route::post('download/{id}', 'download')->name('download');
});

Route::controller('SiteController')->group(function () {
    Route::get('/contact', 'contact')->name('contact');
    Route::post('/contact', 'contactSubmit');
    Route::get('/change/{lang?}', 'changeLanguage')->name('lang');

    //get invoice
    Route::get('invoice/{type}/{trx}/{id}', 'getInvoice')->name('invoice');

    //plans
    Route::get('/plans', 'plans')->name('plans');

    //collections
    Route::get('/collections', 'collections')->name('collections');
    Route::get('/collection/{slug}/{id}/detail', 'collectionDetail')->name('collection.detail');

    //members
    Route::get('/members', 'members')->name('members');
    Route::get('/{username}/images', 'memberImages')->name('member.images');
    Route::get('/{username}/collections', 'memberCollections')->name('member.collections');
    Route::get('/{username}/about', 'memberFollowerFollowings')->name('member.followers.followings');
    Route::get('/{username}/followers', 'memberFollowers')->name('member.followers');
    Route::get('/{username}/followings', 'memberFollowings')->name('member.followings');

    //images
    Route::get('images/{scope}', 'images')->name('images');
    Route::get('image/{slug}/{id}', 'imageDetail')->name('image.detail');

    //search
    Route::get('search', 'search')->name('search');

    Route::get('/txt/download', 'txtDownload')->name('txt.download');

    Route::get('cookie-policy', 'cookiePolicy')->name('cookie.policy');

    Route::get('/cookie/accept', 'cookieAccept')->name('cookie.accept');

    Route::get('blog/{slug}', 'blogDetails')->name('blog.details');

    Route::get('policy/{slug}', 'policyPages')->name('policy.pages');

    Route::get('placeholder-image/{size}', 'placeholderImage')->withoutMiddleware('maintenance')->name('placeholder.image');
    Route::get('maintenance-mode', 'maintenance')->withoutMiddleware('maintenance')->name('maintenance');

    Route::get('/{slug}', 'pages')->name('pages');
    Route::get('/', 'index')->name('home');
});

Route::prefix('donation')->name('donation.')->group(function () {

    Route::controller('DonationController')->group(function () {
        Route::post('insert/{image_id}', 'donationInsert')->name('insert');
    });
    
    Route::controller('Gateway\PaymentController')->group(function () {
        Route::get('confirm', 'depositConfirm')->name('confirm');
        Route::get('manual', 'manualDepositConfirm')->name('manual.confirm');
        Route::post('manual', 'manualDepositUpdate')->name('manual.update');
    });
});
