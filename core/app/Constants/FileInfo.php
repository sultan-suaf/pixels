<?php

namespace App\Constants;

class FileInfo
{

    /*
    |--------------------------------------------------------------------------
    | File Information
    |--------------------------------------------------------------------------
    |
    | This class basically contain the path of files and size of images.
    | All information are stored as an array. Developer will be able to access
    | this info as method and property using FileManager class.
    |
    */

    public function fileInfo(){
        $data['withdrawVerify'] = [
            'path'=>'assets/images/verify/withdraw'
        ];
        $data['depositVerify'] = [
            'path'      =>'assets/images/verify/deposit'
        ];
        $data['verify'] = [
            'path'      =>'assets/verify'
        ];
        $data['default'] = [
            'path'      => 'assets/images/default.png',
        ];
        $data['withdrawMethod'] = [
            'path'      => 'assets/images/withdraw/method',
            'size'      => '800x800',
        ];
        $data['ticket'] = [
            'path'      => 'assets/support',
        ];
        $data['logo_icon'] = [
            'path'      => 'assets/images/logo_icon',
        ];
        $data['favicon'] = [
            'size'      => '128x128',
        ];
        $data['extensions'] = [
            'path'      => 'assets/images/extensions',
            'size'      => '36x36',
        ];
        $data['seo'] = [
            'path'      => 'assets/images/seo',
            'size'      => '1180x600',
        ];
        $data['userProfile'] = [
            'path'      =>'assets/images/user/profile',
            'size'      =>'350x300',
        ];
        $data['adminProfile'] = [
            'path'      =>'assets/admin/images/profile',
            'size'      =>'400x400',
        ];
        $data['push'] = [
            'path'      =>'assets/images/push_notification',
        ];
        $data['appPurchase'] = [
            'path'      =>'assets/in_app_purchase_config',
        ];
        $data['maintenance'] = [
            'path'      =>'assets/images/maintenance',
            'size'      =>'660x325',
        ];
        $data['language'] = [
            'path' => 'assets/images/language',
            'size' => '50x50'
        ];
        $data['gateway'] = [
            'path' => 'assets/images/gateway',
            'size' => ''
        ];
        $data['withdrawMethod'] = [
            'path' => 'assets/images/withdraw_method',
            'size' => ''
        ];
        $data['pushConfig'] = [
            'path'      => 'assets/admin',
        ];

        $data['reviewerProfile'] = [
            'path'      => 'assets/reviewer/images/profile',
            'size'      => '400x400',
        ];
        $data['category'] = [
            'path'      => 'assets/images/category',
            'size'      => '1900x740'
        ];
        $data['fileType'] = [
            'path'      => 'assets/images/file_type',
            'size'      => '1900x740',
            'thumb'      => '475x185',
        ];
        $data['fileTypeVideo'] = [
            'path'      => 'assets/videos/file_type',
        ];
        $data['fileTypeCollection'] = [
            'path'      => 'assets/images/file_type_collection',
            'size'      => '250x265'
        ];
        $data['stockImage'] = [
            'path'      => 'assets/images/stock/image',
        ];
        $data['stockVideo'] = [
            'path'      => 'assets/videos/stock',
        ];
        $data['stockFile'] = [
            'path'      => 'assets/images/stock/file',
        ];
        $data['watermark'] = [
            'path'     => 'assets/images',
            'size'     => '400x200'
        ];

        $data['defaultImage'] = [
            'path' => 'assets/images/default'
        ];
        $data['ads'] = [
            'path' => 'assets/images/ads'
        ];
        return $data;
	}

}
