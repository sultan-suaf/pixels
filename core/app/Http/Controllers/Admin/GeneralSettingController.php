<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Frontend;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class GeneralSettingController extends Controller
{
    public function systemSetting()
    {
        $pageTitle = 'System Settings';
        $settings  = json_decode(file_get_contents(resource_path('views/admin/setting/settings.json')));
        return view('admin.setting.system', compact('pageTitle', 'settings'));
    }
    public function general()
    {
        $pageTitle       = 'General Setting';
        $timezones       = timezone_identifiers_list();
        $currentTimezone = array_search(config('app.timezone'), $timezones);
        return view('admin.setting.general', compact('pageTitle', 'timezones', 'currentTimezone'));
    }

    public function generalUpdate(Request $request)
    {
        $referValidation = 'nullable';
        if (gs('referral_system')) {
            $referValidation = 'required';
        }

        $request->validate([
            'site_name'           => 'required|string|max:40',
            'cur_text'            => 'required|string|max:40',
            'cur_sym'             => 'required|string|max:40',
            'base_color'          => 'nullable|regex:/^[a-f0-9]{6}$/i',
            'timezone'            => 'required|integer',
            'currency_format'     => 'required|in:1,2,3',
            'paginate_number'     => 'required|integer',
            'referral_commission' => $referValidation . '|numeric|gt:0',
            'upload_limit'        => 'required|numeric|gte: -1',
            'per_download'        => 'required|numeric|gte: 0',
            'image_maximum_price' => 'required|numeric|gt:0',
            'ads_script'          => 'nullable|string',
            'banner'              => 'required'
        ]);

        $timezones = timezone_identifiers_list();
        $timezone  = @$timezones[$request->timezone] ?? 'UTC';

        $general                      = gs();
        $general->site_name           = $request->site_name;
        $general->cur_text            = $request->cur_text;
        $general->cur_sym             = $request->cur_sym;
        $general->paginate_number     = $request->paginate_number;
        $general->base_color          = str_replace('#', '', $request->base_color);
        $general->currency_format     = $request->currency_format;
        $general->upload_limit        = $request->upload_limit;
        $general->referral_commission = $general->referral_system ? $request->referral_commission : $general->referral_commission;
        $general->per_download        = $request->per_download;
        $general->image_maximum_price = $request->image_maximum_price;
        $general->image_maximum_price = $request->image_maximum_price;
        $general->ads_script          = $request->ads_script;
        $general->banner              = $request->banner;
        $general->save();

        $timezoneFile = config_path('timezone.php');
        $content      = '<?php $timezone = "' . $timezone . '" ?>';
        file_put_contents($timezoneFile, $content);
        $notify[] = ['success', 'General setting updated successfully'];
        return back()->withNotify($notify);
    }

    public function systemConfiguration()
    {
        $pageTitle = 'System Configuration';
        return view('admin.setting.configuration', compact('pageTitle'));
    }


    public function systemConfigurationSubmit(Request $request)
    {
        $general                    = gs();
        $general->kv                = $request->kv ? Status::ENABLE : Status::DISABLE;
        $general->ev                = $request->ev ? Status::ENABLE : Status::DISABLE;
        $general->en                = $request->en ? Status::ENABLE : Status::DISABLE;
        $general->sv                = $request->sv ? Status::ENABLE : Status::DISABLE;
        $general->sn                = $request->sn ? Status::ENABLE : Status::DISABLE;
        $general->pn                = $request->pn ? Status::ENABLE : Status::DISABLE;
        $general->force_ssl         = $request->force_ssl ? Status::ENABLE : Status::DISABLE;
        $general->secure_password   = $request->secure_password ? Status::ENABLE : Status::DISABLE;
        $general->registration      = $request->registration ? Status::ENABLE : Status::DISABLE;
        $general->agree             = $request->agree ? Status::ENABLE : Status::DISABLE;
        $general->multi_language    = $request->multi_language ? Status::ENABLE : Status::DISABLE;
        $general->in_app_payment    = $request->in_app_payment ? Status::ENABLE : Status::DISABLE;
        $general->is_invoice_active = $request->is_invoice_active ? Status::ENABLE : Status::DISABLE;
        $general->watermark         = $request->watermark ? Status::ENABLE : Status::DISABLE;
        $general->ads_module        = $request->ads_module ? Status::ENABLE : Status::DISABLE;
        $general->contact_system    = $request->contact_system ? Status::ENABLE : Status::DISABLE;
        $general->donation_module   = $request->donation_module ? Status::ENABLE : Status::DISABLE;
        $general->auto_approval     = $request->auto_approval ? Status::ENABLE : Status::DISABLE;
        $general->referral_system   = $request->referral_system ? Status::ENABLE : Status::DISABLE;
        $general->save();
        $notify[] = ['success', 'System configuration updated successfully'];
        return back()->withNotify($notify);
    }


    public function logoIcon()
    {
        $pageTitle = 'Logo & Favicon';
        return view('admin.setting.logo_icon', compact('pageTitle'));
    }

    public function logoIconUpdate(Request $request)
    {
        $request->validate([
            'logo'      => ['image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
            'logo_dark' => ['image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
            'favicon'   => ['image', new FileTypeValidate(['png'])],
        ]);

        $path = getFilePath('logo_icon');
        if ($request->hasFile('logo')) {
            try {
                fileUploader($request->logo, $path, filename: 'logo.png');
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload the logo'];
                return back()->withNotify($notify);
            }
        }

        if ($request->hasFile('favicon')) {
            try {
                fileUploader($request->favicon, $path, filename: 'favicon.png');
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload the favicon'];
                return back()->withNotify($notify);
            }
        }
        if ($request->hasFile('logo_dark')) {
            try {
                fileUploader($request->logo_dark, $path, filename: 'logo_dark.png');
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload the dark logo'];
                return back()->withNotify($notify);
            }
        }

        $notify[] = ['success', 'Logo & favicon updated successfully'];
        return back()->withNotify($notify);
    }

    public function customCss()
    {
        $pageTitle   = 'Custom CSS';
        $file        = activeTemplate(true) . 'css/custom.css';
        $fileContent = @file_get_contents($file);
        return view('admin.setting.custom_css', compact('pageTitle', 'fileContent'));
    }

    public function sitemap()
    {
        $pageTitle   = 'Sitemap XML';
        $file        = 'sitemap.xml';
        $fileContent = @file_get_contents($file);
        return view('admin.setting.sitemap', compact('pageTitle', 'fileContent'));
    }

    public function sitemapSubmit(Request $request)
    {
        $file = 'sitemap.xml';
        if (!file_exists($file)) {
            fopen($file, "w");
        }
        file_put_contents($file, $request->sitemap);
        $notify[] = ['success', 'Sitemap updated successfully'];
        return back()->withNotify($notify);
    }



    public function robot()
    {
        $pageTitle   = 'Robots TXT';
        $file        = 'robots.xml';
        $fileContent = @file_get_contents($file);
        return view('admin.setting.robots', compact('pageTitle', 'fileContent'));
    }

    public function robotSubmit(Request $request)
    {
        $file = 'robots.xml';
        if (!file_exists($file)) {
            fopen($file, "w");
        }
        file_put_contents($file, $request->robots);
        $notify[] = ['success', 'Robots txt updated successfully'];
        return back()->withNotify($notify);
    }


    public function customCssSubmit(Request $request)
    {
        $file = activeTemplate(true) . 'css/custom.css';
        if (!file_exists($file)) {
            fopen($file, "w");
        }
        file_put_contents($file, $request->css);
        $notify[] = ['success', 'CSS updated successfully'];
        return back()->withNotify($notify);
    }

    public function maintenanceMode()
    {
        $pageTitle   = 'Maintenance Mode';
        $maintenance = Frontend::where('data_keys', 'maintenance.data')->firstOrFail();
        return view('admin.setting.maintenance', compact('pageTitle', 'maintenance'));
    }

    public function maintenanceModeSubmit(Request $request)
    {
        $request->validate([
            'description' => 'required',
            'image'       => ['nullable', new FileTypeValidate(['jpg', 'jpeg', 'png'])]
        ]);
        $general                   = gs();
        $general->maintenance_mode = $request->status ? Status::ENABLE : Status::DISABLE;
        $general->save();

        $maintenance = Frontend::where('data_keys', 'maintenance.data')->firstOrFail();
        $image       = @$maintenance->data_values->image;
        if ($request->hasFile('image')) {
            try {
                $old   = $image;
                $image = fileUploader($request->image, getFilePath('maintenance'), getFileSize('maintenance'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $maintenance->data_values = [
            'description' => $request->description,
            'image'       => $image
        ];
        $maintenance->save();

        $notify[] = ['success', 'Maintenance mode updated successfully'];
        return back()->withNotify($notify);
    }

    public function cookie()
    {
        $pageTitle = 'GDPR Cookie';
        $cookie    = Frontend::where('data_keys', 'cookie.data')->firstOrFail();
        return view('admin.setting.cookie', compact('pageTitle', 'cookie'));
    }

    public function cookieSubmit(Request $request)
    {
        $request->validate([
            'short_desc'  => 'required|string|max:255',
            'description' => 'required',
        ]);
        $cookie              = Frontend::where('data_keys', 'cookie.data')->firstOrFail();
        $cookie->data_values = [
            'short_desc'  => $request->short_desc,
            'description' => $request->description,
            'status'      => $request->status ? Status::ENABLE : Status::DISABLE,
        ];
        $cookie->save();
        $notify[] = ['success', 'Cookie policy updated successfully'];
        return back()->withNotify($notify);
    }


    public function socialiteCredentials()
    {
        $pageTitle = 'Social Login Credentials';
        return view('admin.setting.social_credential', compact('pageTitle'));
    }

    public function updateSocialiteCredentialStatus($key)
    {
        $general     = gs();
        $credentials = $general->socialite_credentials;
        try {
            $credentials->$key->status = $credentials->$key->status == Status::ENABLE ? Status::DISABLE : Status::ENABLE;
        } catch (\Throwable $th) {
            abort(404);
        }

        $general->socialite_credentials = $credentials;
        $general->save();

        $notify[] = ['success', 'Status changed successfully'];
        return back()->withNotify($notify);
    }

    public function updateSocialiteCredential(Request $request, $key)
    {
        $general     = gs();
        $credentials = $general->socialite_credentials;
        try {
            @$credentials->$key->client_id     = $request->client_id;
            @$credentials->$key->client_secret = $request->client_secret;
        } catch (\Throwable $th) {
            abort(404);
        }
        $general->socialite_credentials = $credentials;
        $general->save();

        $notify[] = ['success', ucfirst($key) . ' credential updated successfully'];
        return back()->withNotify($notify);
    }


    public function ftpSettings(Request $request)
    {
        $pageTitle = "Storage Settings";
        return view('admin.setting.storage', compact('pageTitle'));
    }


    public function ftpSettingsUpdate(Request $request)
    {
        $request->validate(
            [
                'storage_type'    => 'in:1,2,3,4,5',
                'ftp.host_domain' => 'required_if:storage_type,2|url',
                'ftp.host'        => 'required_if:storage_type,2',
                'ftp.username'    => 'required_if:storage_type,2',
                'ftp.password'    => 'required_if:storage_type,2',
                'ftp.port'        => 'required_if:storage_type,2|integer',
                'ftp.root_path'   => 'required_if:storage_type,2',

                'wasabi.driver'   => 'required_if:storage_type,3',
                'wasabi.key'      => 'required_if:storage_type,3',
                'wasabi.secret'   => 'required_if:storage_type,3',
                'wasabi.region'   => 'required_if:storage_type,3',
                'wasabi.bucket'   => 'required_if:storage_type,3',
                'wasabi.endpoint' => 'required_if:storage_type,3',

                'digital_ocean.driver'   => 'required_if:storage_type,4',
                'digital_ocean.key'      => 'required_if:storage_type,4',
                'digital_ocean.secret'   => 'required_if:storage_type,4',
                'digital_ocean.region'   => 'required_if:storage_type,4',
                'digital_ocean.bucket'   => 'required_if:storage_type,4',
                'digital_ocean.endpoint' => 'required_if:storage_type,4',

                'vultr.driver'   => 'required_if:storage_type,5',
                'vultr.key'      => 'required_if:storage_type,5',
                'vultr.secret'   => 'required_if:storage_type,5',
                'vultr.bucket'   => 'required_if:storage_type,5',
                'vultr.endpoint' => 'required_if:storage_type,5',
            ],
            [
                'ftp.host_domain.required_if' => ':host_domain is required when ftp storage is selected',
                'ftp.host.required_if'        => ':host is required when ftp storage is selected',
                'ftp.username.required_if'    => ':username is required when ftp storage is selected',
                'ftp.password.required_if'    => ':password is required when ftp storage is selected',
                'ftp.port.required_if'        => ':port is required when ftp storage is selected',
                'ftp.root_path.required_if'   => ':root_path is required when ftp storage is selected',

                'wasabi.driver.required_if' => 'Wasabi driver field is required',
                'wasabi.key.required_if'    => 'Wasabi key field is required',
                'wasabi.secret.required_if' => 'Wasabi secret field is required',
                'wasabi.region.required_if' => 'Wasabi region field is required',
                'wasabi.bucket'             => 'Wasabi bucket field is required',
                'wasabi.endpoint'           => 'Wasabi endpoint field is required',

                'digital_ocean.driver.required_if' => 'Digital Ocean driver field is required',
                'digital_ocean.key.required_if'    => 'Digital Ocean key field is required',
                'digital_ocean.secret.required_if' => 'Digital Ocean secret field is required',
                'digital_ocean.region.required_if' => 'Digital Ocean region field is required',
                'digital_ocean.bucket'             => 'Digital Ocean bucket field is required',
                'digital_ocean.endpoint'           => 'Digital Ocean endpoint field is required',

                'vultr.driver.required_if' => 'Vultr driver field is required',
                'vultr.key.required_if'    => 'Vultr key field is required',
                'vultr.secret.required_if' => 'Vultr secret field is required',
                'vultr.bucket'             => 'Vultr bucket field is required',
                'vultr.endpoint'           => 'Vultr endpoint field is required',

            ]
        );

        $general               = gs();
        $general->storage_type = $request->storage_type;
        if ($request->storage_type == 2) {
            $general->ftp = $request->ftp;
        }
        if ($request->storage_type == 3) {
            $general->wasabi = $request->wasabi;
        }

        if ($request->storage_type == 4) {
            $general->digital_ocean = $request->digital_ocean;
        }
        if ($request->storage_type == 5) {
            $general->vultr = $request->vultr;
        }

        $general->save();
        $notify[] = ['success', 'Storage setting updated successfully'];
        return back()->withNotify($notify);
    }


    public function updateInstruction(Request $request)
    {
        $request->validate([
            'heading'     => 'required|string',
            'instruction' => 'required',

        ]);

        $general              = gs();
        $general->instruction = [
            'heading'     => $request->heading,
            'instruction' => $request->instruction,

        ];

        if ($request->hasFile('txt')) {
            if ($request->txt->getClientOriginalExtension() != 'txt') {
                $notify[] = ['error', 'Only txt file accepted'];
                return back()->withNotify($notify);
            }
            $filename = 'license.txt';
            $path     = 'assets/license/';

            if ($general->file != null) {
                unlink($path . $general->file);
            }

            $request->txt->move($path, $filename);
            $general->ins_file = $filename;
        }

        $general->save();

        $notify[] = ['success', 'Instruction updated successfully'];
        return back()->withNotify($notify);
    }

    // public function updateWaterMark(Request $request)
    // {
    //     $request->validate([
    //         'watermark' => [new FileTypeValidate(['png'])]
    //     ]);


    //     $path = getFilePath('watermark');
    //     if ($request->hasFile('watermark')) {
    //         try {
    //             fileUploader($request->watermark,$path,getFileSize('watermark'),filename:'watermark.png');
    //         } catch (\Exception $exp) {
    //             $notify[] = ['error', 'Couldn\'t upload the watermark'];
    //             return back()->withNotify($notify);
    //         }
    //     }

    //     $notify[] = ['success', 'watermark has been updated.'];
    //     return back()->withNotify($notify);
    // }

    public function updateWaterMark(Request $request)
    {
        $request->validate([
            'watermark' => [new FileTypeValidate(['png'])]
        ]);

        if ($request->hasFile('watermark')) {
            try {
                $path = getFilePath('watermark');
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                $size    = explode('x', getFileSize('watermark'));
                $manager = new ImageManager(new Driver());
                $manager->read($request->watermark)->resize($size[0], $size[1])->save($path . '/watermark.png');
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload the watermark'];
                return back()->withNotify($notify);
            }
        }

        $notify[] = ['success', 'watermark has been updated.'];
        return back()->withNotify($notify);
    }
}
