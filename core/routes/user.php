<?php

use Illuminate\Support\Facades\Route;

Route::namespace('User\Auth')->name('user.')->group(function () {

    Route::middleware('guest')->group(function () {
        Route::controller('LoginController')->group(function () {
            Route::get('/login', 'showLoginForm')->name('login');
            Route::post('/login', 'login');
            Route::get('logout', 'logout')->middleware('auth')->withoutMiddleware('guest')->name('logout');
        });

        Route::controller('RegisterController')->middleware(['guest'])->group(function () {
            Route::get('register', 'showRegistrationForm')->name('register');
            Route::post('register', 'register');
            Route::post('check-user', 'checkUser')->name('checkUser')->withoutMiddleware('guest');
        });

        Route::controller('ForgotPasswordController')->prefix('password')->name('password.')->group(function () {
            Route::get('reset', 'showLinkRequestForm')->name('request');
            Route::post('email', 'sendResetCodeEmail')->name('email');
            Route::get('code-verify', 'codeVerify')->name('code.verify');
            Route::post('verify-code', 'verifyCode')->name('verify.code');
        });

        Route::controller('ResetPasswordController')->group(function () {
            Route::post('password/reset', 'reset')->name('password.update');
            Route::get('password/reset/{token}', 'showResetForm')->name('password.reset');
        });

        Route::controller('SocialiteController')->group(function () {
            Route::get('social-login/{provider}', 'socialLogin')->name('social.login');
            Route::get('social-login/callback/{provider}', 'callback')->name('social.login.callback');
        });
    });
});

Route::middleware('auth')->name('user.')->group(function () {

    Route::get('user-data', 'User\UserController@userData')->name('data');
    Route::post('user-data-submit', 'User\UserController@userDataSubmit')->name('data.submit');

    //authorization
    Route::middleware('registration.complete')->namespace('User')->controller('AuthorizationController')->group(function () {
        Route::get('authorization', 'authorizeForm')->name('authorization');
        Route::get('resend-verify/{type}', 'sendVerifyCode')->name('send.verify.code');
        Route::post('verify-email', 'emailVerification')->name('verify.email');
        Route::post('verify-mobile', 'mobileVerification')->name('verify.mobile');
        Route::post('verify-g2fa', 'g2faVerification')->name('2fa.verify');
    });

    Route::middleware(['check.status', 'registration.complete'])->group(function () {

        Route::namespace('User')->group(function () {

            Route::controller('UserController')->group(function () {
                Route::get('dashboard', 'home')->name('home');
                Route::get('download-attachments/{file_hash}', 'downloadAttachment')->name('download.attachment');

                //2FA
                Route::get('twofactor', 'show2faForm')->name('twofactor');
                Route::post('twofactor/enable', 'create2fa')->name('twofactor.enable');
                Route::post('twofactor/disable', 'disable2fa')->name('twofactor.disable');

                //KYC
                Route::get('kyc-form', 'kycForm')->name('kyc.form');
                Route::get('kyc-data', 'kycData')->name('kyc.data');
                Route::post('kyc-submit', 'kycSubmit')->name('kyc.submit');

                //Report
                Route::any('deposit/history', 'depositHistory')->name('deposit.history');
                Route::any('donation/history', 'donationHistory')->name('donation.history');
                Route::get('transactions', 'transactions')->name('transactions');

                Route::post('add-device-token', 'addDeviceToken')->name('add.device.token');


                //follow
                Route::post('follow/update', 'updateFollow')->name('follow.update');

                //referrals
                Route::get('referrals', 'referrals')->name('referral.all');
                Route::get('referral/commission', 'referralCommissionLog')->name('referral.log');

                //Earning log
                Route::get('earning/log', 'earningLog')->name('earning.log');

                //Download history
                Route::get('download/history', 'downloadHistory')->name('download.history');
            });

            //Profile setting
            Route::controller('ProfileController')->group(function () {
                Route::post('profile/update', 'submitProfile')->name('profile.update');
                Route::get('change-password', 'changePassword')->name('change.password');
                Route::post('change-password', 'submitPassword');
                Route::post('cover-photo', 'updateCoverPhoto')->name('cover.update');
                Route::post('profile-picture', 'updateProfilePicture')->name('profile.picture.update');
            });

            //image
            Route::controller('ImageController')->name('image.')->prefix('image')->group(function () {
                Route::get('all', 'all')->name('all');
                Route::get('pending', 'pending')->name('pending');
                Route::get('approved', 'approved')->name('approved');
                Route::get('rejected', 'rejected')->name('rejected');

                //image store
                Route::get('upload', 'add')->name('add');
                Route::post('store', 'store')->name('store');
                Route::post('like/update', 'updateLike')->name('like.update');

                //image edit
                Route::get('{id}/edit', 'edit')->name('edit');
                Route::post('update/{id}', 'updateImage')->name('update');
                Route::post('file/status/{id}', 'changeImageFileActiveStatus')->name('file.status');

                //Download own image and already download image
                Route::get('download/{id}', 'download')->name('download.file');
                Route::get('download/video/{id?}', 'downloadVideo')->name('download.video');
            });


            //collection
            Route::controller('CollectionController')->name('collection.')->prefix('collection')->group(function () {
                Route::get('all', 'all')->name('all');
                Route::post('update/{id}', 'update')->name('update');
                Route::post('delete/{id}', 'delete')->name('delete');
                Route::get('image/data', 'imageData')->name('image.data');
                Route::post('add', 'addCollection')->name('add');
                Route::post('image/add', 'addImage')->name('image.add');
            });

            //plan
            Route::controller('PlanController')->name('plan.')->prefix('plan')->group(function () {
                Route::post('purchase', 'purchasePlan')->name('purchase');
            });


            // Withdraw
            Route::controller('WithdrawController')->prefix('withdraw')->name('withdraw')->group(function () {
                Route::middleware('kyc')->group(function () {
                    Route::get('/', 'withdrawMoney');
                    Route::post('/', 'withdrawStore')->name('.money');
                    Route::get('preview', 'withdrawPreview')->name('.preview');
                    Route::post('preview', 'withdrawSubmit')->name('.submit');
                });
                Route::get('history', 'withdrawLog')->name('.history');
            });
        });

        // Payment
        Route::controller('Gateway\PaymentController')->group(function () {
            Route::any('/payment', 'payment')->name('payment');
            Route::prefix('deposit')->name('deposit.')->group(function () {
                Route::any('/', 'deposit')->name('index');
                Route::post('insert', 'depositInsert')->name('insert');
                Route::get('confirm', 'depositConfirm')->name('confirm');
                Route::get('manual', 'manualDepositConfirm')->name('manual.confirm');
                Route::post('manual', 'manualDepositUpdate')->name('manual.update');
            });
        });
    });
});
