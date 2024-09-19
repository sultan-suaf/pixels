<?php

use App\Constants\Status;
use App\Lib\GoogleAuthenticator;
use App\Models\Extension;
use App\Models\Frontend;
use App\Models\GeneralSetting;
use Carbon\Carbon;
use App\Lib\Captcha;
use App\Lib\ClientInfo;
use App\Lib\CurlRequest;
use App\Lib\FileManager;
use App\Lib\StorageManager;
use App\Models\Ads;
use App\Models\FileType;
use App\Models\Transaction;
use App\Notify\Notify;
use Aws\Credentials\Credentials;
use Aws\S3\S3Client;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Laramin\Utility\VugiChugi;

function systemDetails()
{
    $system['name']          = 'viserstock';
    $system['version']       = '2.0';
    $system['build_version'] = '5.0.5';

    return $system;
}

function slug($string)
{
    return Str::slug($string);
}

function verificationCode($length)
{
    if ($length == 0) return 0;
    $min = pow(10, $length - 1);
    $max = (int) ($min - 1) . '9';
    return random_int($min, $max);
}

function getNumber($length = 8)
{
    $characters = '1234567890';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


function activeTemplate($asset = false)
{
    $template = session('template') ?? gs('active_template');
    if ($asset) return 'assets/templates/' . $template . '/';
    return 'templates.' . $template . '.';
}

function activeTemplateName()
{
    $template = session('template') ?? gs('active_template');
    return $template;
}

function siteLogo($type = null)
{
    $name = $type ? "/logo_$type.png" : '/logo.png';
    return getImage(getFilePath('logo_icon') . $name);
}
function siteFavicon()
{
    return getImage(getFilePath('logo_icon') . '/favicon.png');
}

function loadReCaptcha()
{
    return Captcha::reCaptcha();
}

function loadCustomCaptcha($width = '100%', $height = 46, $bgColor = '#003')
{
    return Captcha::customCaptcha($width, $height, $bgColor);
}

function verifyCaptcha()
{
    return Captcha::verify();
}

function loadExtension($key)
{
    $extension = Extension::where('act', $key)->where('status', Status::ENABLE)->first();
    return $extension ? $extension->generateScript() : '';
}

function getTrx($length = 12)
{
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function getAmount($amount, $length = 2)
{
    $amount = round($amount ?? 0, $length);
    return $amount + 0;
}

function showAmount($amount, $decimal = 2, $separate = true, $exceptZeros = false, $currencyFormat = true)
{
    $separator = '';
    if ($separate) {
        $separator = ',';
    }
    $printAmount = number_format($amount, $decimal, '.', $separator);
    if ($exceptZeros) {
        $exp = explode('.', $printAmount);
        if ($exp[1] * 1 == 0) {
            $printAmount = $exp[0];
        } else {
            $printAmount = rtrim($printAmount, '0');
        }
    }
    if ($currencyFormat) {
        if (gs('currency_format') == Status::CUR_BOTH) {
            return gs('cur_sym') . $printAmount . ' ' . __(gs('cur_text'));
        } elseif (gs('currency_format') == Status::CUR_TEXT) {
            return $printAmount . ' ' . __(gs('cur_text'));
        } else {
            return gs('cur_sym') . $printAmount;
        }
    }
    return $printAmount;
}


function removeElement($array, $value)
{
    return array_diff($array, (is_array($value) ? $value : array($value)));
}

function cryptoQR($wallet)
{
    return "https://api.qrserver.com/v1/create-qr-code/?data=$wallet&size=300x300&ecc=m";
}

function keyToTitle($text)
{
    return ucfirst(preg_replace("/[^A-Za-z0-9 ]/", ' ', $text));
}


function titleToKey($text)
{
    return strtolower(str_replace(' ', '_', $text));
}


function strLimit($title = null, $length = 10)
{
    return Str::limit($title, $length);
}


function getIpInfo()
{
    $ipInfo = ClientInfo::ipInfo();
    return $ipInfo;
}


function osBrowser()
{
    $osBrowser = ClientInfo::osBrowser();
    return $osBrowser;
}


function getTemplates()
{
    $param['purchasecode'] = env("PURCHASECODE");
    $param['website'] = @$_SERVER['HTTP_HOST'] . @$_SERVER['REQUEST_URI'] . ' - ' . env("APP_URL");
    $url = VugiChugi::gttmp() . systemDetails()['name'];
    $response = CurlRequest::curlPostContent($url, $param);
    if ($response) {
        return $response;
    } else {
        return null;
    }
}


function getPageSections($arr = false)
{
    $jsonUrl = resource_path('views/') . str_replace('.', '/', activeTemplate()) . 'sections.json';
    $sections = json_decode(file_get_contents($jsonUrl));
    if ($arr) {
        $sections = json_decode(file_get_contents($jsonUrl), true);
        ksort($sections);
    }
    return $sections;
}

function getImage($image, $size = null, $type = null,$avatar=false)
{
    $clean = '';
    if (file_exists($image) && is_file($image)) {
        return asset($image) . $clean;
    }
    if ($size) {
        return route('placeholder.image', $size);
    }
    if ($avatar) {
        return asset('assets/images/avatar.png');
    }
    if ($type == "user") {
        return asset('assets/images/avatar.png');
    } elseif ($type == 'cover-photo') {
        $defaultImage = getContent('default_images.content', true);
        return asset('assets/images/frontend/default_images/' . @$defaultImage->data_values->cover_photo);
    }

    return asset('assets/images/default.png');
}



function notify($user, $templateName, $shortCodes = null, $sendVia = null, $createLog = true, $pushImage = null)
{
    $globalShortCodes = [
        'site_name' => gs('site_name'),
        'site_currency' => gs('cur_text'),
        'currency_symbol' => gs('cur_sym'),
    ];

    if (gettype($user) == 'array') {
        $user = (object) $user;
    }

    $shortCodes = array_merge($shortCodes ?? [], $globalShortCodes);

    $notify = new Notify($sendVia);
    $notify->templateName = $templateName;
    $notify->shortCodes = $shortCodes;
    $notify->user = $user;
    $notify->createLog = $createLog;
    $notify->pushImage = $pushImage;
    $notify->userColumn = isset($user->id) ? $user->getForeignKey() : 'user_id';
    $notify->send();
}

function getPaginate($paginate = null)
{
    if (!$paginate) {
        $paginate = gs('paginate_number');
    }
    return $paginate;
}

function paginateLinks($data)
{
    return $data->appends(request()->all())->links();
}


function menuActive($routeName, $type = null, $param = null)
{
    if ($type == 3) $class = 'side-menu--open';
    elseif ($type == 2) $class = 'sidebar-submenu__open';
    else $class = 'active';

    if (is_array($routeName)) {
        foreach ($routeName as $key => $value) {
            if (request()->routeIs($value)) return $class;
        }
    } elseif (request()->routeIs($routeName)) {
        if ($param) {
            $routeParam = array_values(@request()->route()->parameters ?? []);
            if (strtolower(@$routeParam[0]) == strtolower($param)) return $class;
            else return;
        }
        return $class;
    }
}


function fileUploader($file, $location, $size = null, $old = null, $thumb = null, $filename = null)
{
    $fileManager = new FileManager($file);
    $fileManager->path = $location;
    $fileManager->size = $size;
    $fileManager->old = $old;
    $fileManager->thumb = $thumb;
    $fileManager->filename = $filename;
    $fileManager->upload();
    return $fileManager->filename;
}

function fileManager()
{
    return new FileManager();
}

function getFilePath($key)
{
    return fileManager()->$key()->path;
}

function getFileSize($key)
{
    return fileManager()->$key()->size;
}


function getFileExt($fileTypeId = 0)
{

    $query = FileType::active();

    if ($fileTypeId && gettype($fileTypeId) ==  'integer') {
        $fileType = $query->where("id", $fileTypeId)->first();
        return @$fileType->supported_file_extension ?? [];
    }

    $fileTypes  = $query->where('supported_file_extension', '!=', null)->pluck('supported_file_extension')->toArray();
    $extensions = [];

    foreach (($fileTypes ?? []) as $fileType) {
        $extensions[] = implode(',', $fileType);
    }

    $extensions = implode(',', $extensions);
    $extensions = explode(',', $extensions);

    return array_unique($extensions);
}


function diffForHumans($date)
{
    $lang = session()->get('lang');
    Carbon::setlocale($lang);
    return Carbon::parse($date)->diffForHumans();
}


function showDateTime($date, $format = 'Y-m-d h:i A')
{
    if (!$date) {
        return '-';
    }
    $lang = session()->get('lang');
    Carbon::setlocale($lang);
    return Carbon::parse($date)->translatedFormat($format);
}


function getContent($dataKeys, $singleQuery = false, $limit = null, $orderById = false)
{

    $templateName = activeTemplateName();
    if ($singleQuery) {
        $content = Frontend::where('tempname', $templateName)->where('data_keys', $dataKeys)->orderBy('id', 'desc')->first();
    } else {
        $article = Frontend::where('tempname', $templateName);
        $article->when($limit != null, function ($q) use ($limit) {
            return $q->limit($limit);
        });
        if ($orderById) {
            $content = $article->where('data_keys', $dataKeys)->orderBy('id')->get();
        } else {
            $content = $article->where('data_keys', $dataKeys)->orderBy('id', 'desc')->get();
        }
    }
    return $content;
}

function verifyG2fa($user, $code, $secret = null)
{
    $authenticator = new GoogleAuthenticator();
    if (!$secret) {
        $secret = $user->tsc;
    }
    $oneCode = $authenticator->getCode($secret);
    $userCode = $code;
    if ($oneCode == $userCode) {
        $user->tv = Status::YES;
        $user->save();
        return true;
    } else {
        return false;
    }
}


function urlPath($routeName, $routeParam = null)
{
    if ($routeParam == null) {
        $url = route($routeName);
    } else {
        $url = route($routeName, $routeParam);
    }
    $basePath = route('home');
    $path = str_replace($basePath, '', $url);
    return $path;
}


function showMobileNumber($number)
{
    $length = strlen($number);
    return substr_replace($number, '***', 2, $length - 4);
}

function showEmailAddress($email)
{
    $endPosition = strpos($email, '@') - 1;
    return substr_replace($email, '***', 1, $endPosition);
}


function getRealIP()
{
    $ip = $_SERVER["REMOTE_ADDR"];
    //Deep detect ip
    if (filter_var(@$_SERVER['HTTP_FORWARDED'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED'];
    }
    if (filter_var(@$_SERVER['HTTP_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED_FOR'];
    }
    if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    if (filter_var(@$_SERVER['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    }
    if (filter_var(@$_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    }
    if ($ip == '::1') {
        $ip = '127.0.0.1';
    }

    return $ip;
}


function appendQuery($key, $value)
{
    return request()->fullUrlWithQuery([$key => $value]);
}

function dateSort($a, $b)
{
    return strtotime($a) - strtotime($b);
}

function dateSorting($arr)
{
    usort($arr, "dateSort");
    return $arr;
}

function gs($key = null)
{
    $general = Cache::get('GeneralSetting');
    if (!$general) {
        $general = GeneralSetting::first();
        Cache::put('GeneralSetting', $general);
    }
    if ($key) return @$general->$key;
    return $general;
}
function isImage($string)
{
    $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
    $fileExtension = pathinfo($string, PATHINFO_EXTENSION);
    if (in_array($fileExtension, $allowedExtensions)) {
        return true;
    } else {
        return false;
    }
}

function isHtml($string)
{
    if (preg_match('/<.*?>/', $string)) {
        return true;
    } else {
        return false;
    }
}


function convertToReadableSize($size)
{
    preg_match('/^(\d+)([KMG])$/', $size, $matches);
    $size = (int)$matches[1];
    $unit = $matches[2];

    if ($unit == 'G') {
        return $size . 'GB';
    }

    if ($unit == 'M') {
        return $size . 'MB';
    }

    if ($unit == 'K') {
        return $size . 'KB';
    }

    return $size . $unit;
}


function frontendImage($sectionName, $image, $size = null, $seo = false)
{
    if ($seo) {
        return getImage('assets/images/frontend/' . $sectionName . '/seo/' . $image, $size);
    }
    return getImage('assets/images/frontend/' . $sectionName . '/' . $image, $size);
}



function adSizes()
{
    return [
        "970x250",
        "728x90",
        "300x250",
    ];
}

function getAds($size, $count = 1)
{

    if (!gs('ads_module'))  return;

    $ads = Ads::where('size', $size)->inRandomOrder()->take($count)->get();
    if (!$ads->count()) return;

    $html = '<div style="text-align:center; padding: 5px 0">';
    while ($count) {
        foreach ($ads as $ad) {
            $ad->increment('impressions');
            if ($ad->type == 1) $html .= $ad->code;
            else {
                $maxWidth =  explode('x', $size)[0];
                $imgUrl = getImage(getFilePath('ads') . '/' . $ad->image, $size);
                $html .= '<a target="_blank" href="' . $ad->target_url . '"><img style="padding:5px;width:100%;max-width:' . $maxWidth . 'px" src="' . $imgUrl . '" alt="' . $ad->title . '"></a>';
            }
            $count--;
        }
    }
    $html .= '</div>';
    return $html;
}


function shortNumber($num)
{
    $units = ['', 'K', 'M', 'B', 'T'];
    for ($i = 0; $num >= 1000; $i++) {
        $num /= 1000;
    }
    return round($num, 2) . $units[$i];
}


function removeFileFromStorageManager($path = null)
{
    $general = gs();
    $servers = [2 => "ftp", 3 => "wasabi", 4 => "do", 5 => "vultr"];
    $server = $servers[$general->storage_type];
    $storageManager = new StorageManager($server);
    $storageManager->removeFile($path);
}

function storageManager($file, $location, $size = null, $old = null, $thumb = null)
{
    $general = gs();
    $servers = [2 => "ftp", 3 => "wasabi", 4 => "do", 5 => "vultr"];
    $server = $servers[$general->storage_type];

    $storageManager = new StorageManager($server, $file);
    $storageManager->path = $location;
    $storageManager->size = $size;
    $storageManager->old = $old;
    $storageManager->thumb = $thumb;
    $storageManager->upload();

    return $storageManager->filename;
}


function removeFile($path = null)
{
    fileManager()->removeFile($path);
}


function referCommission($user, $amount, $trx)
{
    $general = gs();
    if ($user->ref_by) {
        $referrer           = $user->referrer;
        $commission         = $amount * $general->referral_commission / 100;
        $referrer->balance += $commission;
        $referrer->save();

        $transaction               = new Transaction();
        $transaction->user_id      = $referrer->id;
        $transaction->amount       = $commission;
        $transaction->post_balance = $referrer->balance;
        $transaction->trx_type     = "+";
        $transaction->trx          = $trx;
        $transaction->details      = $referrer->fullname . ' get' . $commission . ' ' . $general->cur_text  . 'for referral commission';
        $transaction->remark       = 'referral_commission';
        $transaction->save();

        notify($referrer, 'REFERRAL_COMMISSION', [
            'user'         => $user->fullname,
            'trx'          => $transaction->trx,
            'amount'       => showAmount($transaction->amount, currencyFormat: false),
            'post_balance' => showAmount($transaction->post_balance, currencyFormat: false)
        ]);
    }
}


function imageUrl($directory = null, $image = null, $size = null)
{
    if (!$image) {
        return getImage('/', $size);
    }

    $general = gs();

    if ($general->storage_type == 2) {
        return $general->ftp->host_domain . '/images/' . $image;
    } elseif ($general->storage_type == 3 || $general->storage_type == 4 || $general->storage_type == 5) {
        return getS3FileUri($image);
    } else {
        $image = $directory ? $directory . '/' . $image : $image;
        return getImage($image, $size);
    }
}

function getS3FileUri($fileName, $type = "image")
{
    $general = gs();
    $servers = [3 => "wasabi", 4 => "digital_ocean", 5 => "vultr"];
    $server  = $servers[$general->storage_type];

    $accessKey  = @$general?->{$server}?->key;
    $secretKey  = @$general?->{$server}?->secret;
    $bucketName = @$general?->{$server}?->bucket;

    $objectKey = $type == 'image' ? 'images/' . $fileName : 'files/' . $fileName;
    $endpoint = $general->{$server}->endpoint;

    $credentials = new Credentials($accessKey, $secretKey);
    $s3Client = new S3Client([
        'version'     => 'latest',
        'region'      => @$general?->{$server}?->region ?? '',
        'endpoint'    => $endpoint,
        'credentials' => $credentials
    ]);

    $command = $s3Client->getCommand('GetObject', [
        'Bucket' => $bucketName,
        'Key' => $objectKey,
    ]);

    try {
        return (string) $s3Client->createPresignedRequest($command, '+1 hour')->getUri();
    } catch (Exception $ex) {
    }
}


function fileUrl($fileName)
{
    $general = gs();
    if ($general->storage_type == 2) {
        return @$general->ftp->host_domain . '/files/' . $fileName;
    } elseif ($general->storage_type == 3 || $general->storage_type == 4 || $general->storage_type == 5) {
        return getS3FileUri($fileName, 'file');
    } else {
        return getFilePath('stockFile') . '/' . $fileName;
    }
}

function VideoFileUrl($fileName)
{
    $general = gs();
    if ($general->storage_type == 2) {
        return @$general->ftp->host_domain . '/files/' . $fileName;
    } elseif ($general->storage_type == 3 || $general->storage_type == 4 || $general->storage_type == 5) {
        return getS3FileUri($fileName, 'file');
    } else {
        return getFilePath('stockVideo') . '/' . $fileName;
    }
}

function getSeoContents($keywords = [], $socialTitle = null, $description = null, $imagePath = null,  $type = null)
{
    $seoContents['keywords']           = $keywords;
    $seoContents['social_title']       = $socialTitle;
    $seoContents['description']        = strip_tags($description);
    $seoContents['social_description'] = strip_tags($description);
    $seoContents['image']              = getImage($imagePath, null, $type);
    $seoContents['image_size']         = '1000x700';

    return $seoContents;
}




function getExtension($path)
{
    $ext = pathinfo($path, PATHINFO_EXTENSION);
    return $ext;
}
