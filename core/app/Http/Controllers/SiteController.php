<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Models\AdminNotification;
use App\Models\Category;
use App\Models\Collection;
use App\Models\FileType;
use App\Models\Follow;
use App\Models\Frontend;
use App\Models\GatewayCurrency;
use App\Models\Image;
use App\Models\ImageFile;
use App\Models\Language;
use App\Models\Page;
use App\Models\Plan;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;


class SiteController extends Controller
{

    private $memberRelation = ['images', 'downloads', 'publicCollections', 'privateCollections', 'followers', 'followings'];

    public function index()
    {
        $reference = @$_GET['reference'];
        if ($reference) {
            session()->put('reference', $reference);
        }

        $pageTitle = 'Home';
        $images    = Image::approved()->hasActiveFiles()->where(function ($q) {
            $q->where('user_id', auth()->id())->orWhereHas('category', function ($category) {
                $category->active();
            });
        })->orderBy('id', 'DESC')->withCount(['files as premium' => function ($file) {
            $file->active()->premium();
        }])->with('user', 'likes')->limit(28)->get();

        $sections    = Page::where('tempname', activeTemplate())->where('slug', '/')->first();
        $seoContents = $sections->seo_content;
        $seoImage    = @$seoContents->image ? getImage(getFilePath('seo') . '/' . @$seoContents->image, getFileSize('seo')) : null;
        return view('Template::home', compact('pageTitle', 'sections', 'seoContents', 'seoImage', 'images'));
    }

    public function pages($slug)
    {
        $page        = Page::where('tempname', activeTemplate())->where('slug', $slug)->firstOrFail();
        $pageTitle   = $page->name;
        $sections    = $page->secs;
        $seoContents = $page->seo_content;
        $seoImage    = @$seoContents->image ? getImage(getFilePath('seo') . '/' . @$seoContents->image, getFileSize('seo')) : null;
        return view('Template::pages', compact('pageTitle', 'sections', 'seoContents', 'seoImage'));
    }


    public function contact()
    {
        if (!gs('contact_system')) {
            abort(404);
        }

        $pageTitle   = "Contact Us";
        $user        = auth()->user();
        $sections    = Page::where('tempname', activeTemplate())->where('slug', 'contact')->first();
        $seoContents = $sections->seo_content;
        $seoImage    = @$seoContents->image ? getImage(getFilePath('seo') . '/' . @$seoContents->image, getFileSize('seo')) : null;
        return view('Template::contact', compact('pageTitle', 'user', 'sections', 'seoContents', 'seoImage'));
    }


    public function contactSubmit(Request $request)
    {
        if (!gs('contact_system')) {
            abort(404);
        }

        $request->validate([
            'name'    => 'required',
            'email'   => 'required',
            'subject' => 'required|string|max:255',
            'message' => 'required',
        ]);

        $request->session()->regenerateToken();

        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }

        $random = getNumber();

        $ticket           = new SupportTicket();
        $ticket->user_id  = auth()->id() ?? 0;
        $ticket->name     = $request->name;
        $ticket->email    = $request->email;
        $ticket->priority = Status::PRIORITY_MEDIUM;


        $ticket->ticket     = $random;
        $ticket->subject    = $request->subject;
        $ticket->last_reply = Carbon::now();
        $ticket->status     = Status::TICKET_OPEN;
        $ticket->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = auth()->user() ? auth()->user()->id : 0;
        $adminNotification->title     = 'A new contact message has been submitted';
        $adminNotification->click_url = urlPath('admin.ticket.view', $ticket->id);
        $adminNotification->save();

        $message                    = new SupportMessage();
        $message->support_ticket_id = $ticket->id;
        $message->message           = $request->message;
        $message->save();

        $notify[] = ['success', 'Ticket created successfully!'];

        return to_route('ticket.view', [$ticket->ticket])->withNotify($notify);
    }

    public function policyPages($slug)
    {
        $policy      = Frontend::where('slug', $slug)->where('data_keys', 'policy_pages.element')->firstOrFail();
        $pageTitle   = $policy->data_values->title;
        $seoContents = $policy->seo_content;
        $seoImage    = @$seoContents->image ? frontendImage('policy_pages', $seoContents->image, getFileSize('seo'), true) : null;
        return view('Template::policy', compact('policy', 'pageTitle', 'seoContents', 'seoImage'));
    }

    public function changeLanguage($lang = null)
    {
        $language          = Language::where('code', $lang)->first();
        if (!$language) $lang = 'en';
        session()->put('lang', $lang);
        return back();
    }

    public function blogDetails($slug)
    {
        $blog        = Frontend::where('slug', $slug)->where('data_keys', 'blog.element')->firstOrFail();
        $pageTitle   = $blog->data_values->title;
        $seoContents = $blog->seo_content;
        $seoImage    = @$seoContents->image ? frontendImage('blog', $seoContents->image, getFileSize('seo'), true) : null;
        return view('Template::blog_details', compact('blog', 'pageTitle', 'seoContents', 'seoImage'));
    }


    public function cookieAccept()
    {
        Cookie::queue('gdpr_cookie', gs('site_name'), 43200);
    }

    public function cookiePolicy()
    {
        $cookieContent = Frontend::where('data_keys', 'cookie.data')->first();
        abort_if($cookieContent->data_values->status != Status::ENABLE, 404);
        $pageTitle = 'Cookie Policy';
        $cookie    = Frontend::where('data_keys', 'cookie.data')->first();
        return view('Template::cookie', compact('pageTitle', 'cookie'));
    }

    public function placeholderImage($size = null)
    {
        $imgWidth  = explode('x', $size)[0];
        $imgHeight = explode('x', $size)[1];
        $text      = $imgWidth . '×' . $imgHeight;
        $fontFile  = realpath('assets/font/solaimanLipi_bold.ttf');
        $fontSize  = round(($imgWidth - 50) / 8);
        if ($fontSize <= 9) {
            $fontSize = 9;
        }
        if ($imgHeight < 100 && $fontSize > 30) {
            $fontSize = 30;
        }

        $image     = imagecreatetruecolor($imgWidth, $imgHeight);
        $colorFill = imagecolorallocate($image, 100, 100, 100);
        $bgFill    = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $bgFill);
        $textBox    = imagettfbbox($fontSize, 0, $fontFile, $text);
        $textWidth  = abs($textBox[4] - $textBox[0]);
        $textHeight = abs($textBox[5] - $textBox[1]);
        $textX      = ($imgWidth - $textWidth) / 2;
        $textY      = ($imgHeight + $textHeight) / 2;
        header('Content-Type: image/jpeg');
        imagettftext($image, $fontSize, 0, $textX, $textY, $colorFill, $fontFile, $text);
        imagejpeg($image);
        imagedestroy($image);
    }

    public function maintenance()
    {
        $pageTitle = 'Maintenance Mode';
        if (gs('maintenance_mode') == Status::DISABLE) {
            return to_route('home');
        }
        $maintenance = Frontend::where('data_keys', 'maintenance.data')->first();
        return view('Template::maintenance', compact('pageTitle', 'maintenance'));
    }

    public function members()
    {
        $pageTitle = "All Members";
        $user      = auth()->user();
        $members   = User::active()->withCount('images')->orderBy('images_count', 'DESC')->paginate(getPaginate());
        $heading   = 'Members';
        return view('Template::member.all', compact('pageTitle', 'members', 'heading'));
    }

    public function collections()
    {
        $pageTitle   = "Collections";
        $collections = Collection::public()->active()->with('images', 'user')->whereHas('images')->orderBy('id', 'DESC')->paginate(getPaginate());
        return view('Template::collections', compact('pageTitle', 'collections'));
    }



    public function images($scope = null)
    {
        $pageTitle = 'Images';
        $images    = Image::approved()->where(function ($q) {
            $q->where('user_id', auth()->id())->orWhereHas('category', function ($category) {
                $category->active();
            });
        })->hasActiveFiles()->orderBy('id', 'DESC')->withCount(['files as premium' => function ($file) {
            $file->active()->premium();
        }]);

        if ($scope) {
            try {
                $pageTitle = str_replace('-', ' ', ucwords($scope, '-')) . ' Images';
                $scope     = lcfirst(str_replace('-', '', ucwords($scope, '-')));
                $images    = $images->$scope();
            } catch (\Throwable $th) {
                abort(404);
            }
        }

        if (!in_array($scope, ['popular', 'mostDownload']))  $images = $images->orderBy('id', 'DESC');

        $images = $images->with('user', 'likes', 'files')->orderBy('id', 'DESC')->paginate(getPaginate());

        return view('Template::premium_images', compact('pageTitle', 'images'));
    }

    public function plans()
    {
        $pageTitle   = "Plans";
        $plans       = Plan::active()->get();
        $activePlans = Plan::active()->get();

        return view('Template::plans', compact('pageTitle', 'plans', 'activePlans'));
    }



    public function txtDownload()
    {
        $general  = gs();
        $filepath = 'assets/license/license.txt';
        $fileName = $general->site_name . '_' . 'license.txt';
        $headers  = [
            'Cache-Control' => 'no-store, no-cache'
        ];
        return response()->download($filepath, $fileName, $headers);
    }

    public function collectionDetail($slug, $id)
    {
        $collection       = Collection::findOrFail($id);
        $pageTitle        = 'Collection - ' . $collection->title;
        $collectionImages = Image::hasActiveFiles()->whereHas('collections', function ($query) use ($id) {
            $query->where('collection_id', $id);
        })->with('user', 'likes')->paginate(getPaginate());
        return view('Template::collection_details', compact('collectionImages', 'pageTitle', 'collection'));
    }



    public function memberImages($username)
    {

        $memberRelation           = $this->memberRelation;
        $memberRelation['images'] = function ($i) {
            $i->where('user_id', auth()->id())->orWhere(function ($q) {
                $q->hasActiveFiles();
            });
        }; //change image count condition
        $member = User::withCount($memberRelation)->where('username', $username)->firstOrFail();


        $pageTitle   = "Member Images";
        $seoContents = $this->memberSeoContent($member);

        $images      = Image::where(function ($i) {
            $i->where('user_id', auth()->id())->orWhere(function ($q) {
                $q->hasActiveFiles();
            });
        })->with('user', 'likes')->where('user_id', $member->id)->orderBy('id', 'DESC')->paginate(getPaginate());

        return view('Template::member.images', compact('pageTitle', 'member', 'images', 'seoContents'));
    }

    public function memberCollections($username)
    {
        $user      = auth()->user();
        $member    = User::withCount($this->memberRelation)->where('username', $username)->firstOrFail();
        $pageTitle = "Member Collections";


        $collections = Collection::where('user_id', $member->id)->public()->with(['images' => function ($query) {
            $query->where('status', 1);
        }, 'user'])->paginate(getPaginate(16));
        if ($user && $user->id == $member->id) {
            $collections = Collection::where('user_id', $member->id)->with(['images' => function ($query) {
                $query->where('status', 1);
            }, 'user'])->paginate(getPaginate(16));
        }
        return view('Template::member.collections', compact('pageTitle', 'member', 'collections'));
    }

    public function memberFollowerFollowings($username)
    {
        $member = User::with([
            'followers' => function ($followers) {
                $followers->orderBy('id', 'desc')->limit(21);
            }, 'followings' => function ($followings) {
                $followings->orderBy('id', 'desc')->limit(21);
            },
            'followers.followerProfile',
            'followings.followingProfile'
        ])
            ->withCount($this->memberRelation)->where('username', $username)->firstOrFail();

        $pageTitle = "About " . $member->fullname;

        return view('Template::member.about', compact('pageTitle', 'member'));
    }


    protected function memberSeoContent($member)
    {
        $imagePath   = getFilePath('userProfile') . '/' . @$member->image;
        $keywords    = [$member->username, $member->firstname, $member->lastname, $member->fullname];
        $seoContents = getSeoContents($keywords, $member->fullname, $member->fullname, $imagePath, 'user');
        return $seoContents;
    }

    public function imageDetail($slug, $id)
    {
        $image = Image::with(['user'])->where(function ($q) {
            $q->where('user_id', auth()->id())->orWhereHas('category', function ($category) {
                $category->active();
            });
        })->withSum('files as totalDownloads', 'total_downloads')->findOrFail($id);


        abort_if(!$image->files()->active()->count() && @auth()->user()->id != @$image->user_id, 404);


        abort_if($image->status != Status::ENABLE && @auth()->user()->id != @$image->user_id, 404);

        // dd(212);
        $imageFiles = ImageFile::where('image_id', $id);
        if (@auth()->user()->id != @$image->user_id) {
            $imageFiles = $imageFiles->where('status', Status::ENABLE);
        }
        $imageFiles = $imageFiles->get();


        $this->incrementTotalView($image);

        $pageTitle   = $image->title;
        $imagePath   = getFilePath('stockImage') . '/' . @$image->image_name;
        $seoContents = getSeoContents($image->tags, $image->title, $image->description, $imagePath);

        $user              = auth()->user();
        $todayDownload     = 0;
        $monthlyDownload   = 0;
        $alreadyDownloaded = false;

        $relatedImages   = Image::where('id', '!=', $image->id)->approved()->where('category_id', $image->category_id)->with('user', 'likes', 'files')->orderBy('id', 'desc')->limit(8)->get();
        $gatewayCurrency = [];
        if (gs('donation_module')) {
            $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
                $gate->where('status', Status::ENABLE);
            })->with('method')->orderby('method_code')->get();
        }

        return view('Template::image_details', compact('pageTitle', 'image', 'relatedImages', 'seoContents', 'todayDownload', 'monthlyDownload', 'alreadyDownloaded', 'imageFiles', 'gatewayCurrency'));
    }


    private function incrementTotalView($image)
    {
        $counter = session()->get('viewCounter');
        if (!isset($counter)) {
            $imageData = [$image->id => Carbon::now()->addMinutes(5)];
            session()->put('viewCounter', $imageData);

            $image->total_view += 1;
            $image->save();
        } elseif (!array_key_exists($image->id, $counter)) {
            $imageData = $counter + [$image->id => Carbon::now()->addMinutes(5)];
            session()->put('viewCounter', $imageData);
            $image->total_view += 1;
            $image->save();
        } else {
            if ($counter[$image->id] < Carbon::now()) {
                $image->total_view += 1;
                $image->save();

                $counter[$image->id] = Carbon::now()->addMinutes(5);
                session()->put('viewCounter', $counter);
            }
        }
    }



    public function search(Request $request)
    {
        $pageTitle   = "Search";
        $images      = collect([]);
        $collections = collect([]);

        if ($request->type == 'image') {
            $getImages       = $this->getImages($request);
            $images          = $getImages['images'];
            $imageCount      = $getImages['imageCount'];
            $getCollections  = $this->getCollections($request, true);
            $collectionCount = $getCollections['collectionCount'];
        } else {
            $getImages = $this->getImages($request, false);

            $imageCount      = $getImages['imageCount'];
            $getCollections  = $this->getCollections($request);
            $collections     = $getCollections['collections'];
            $collectionCount = $getCollections['collectionCount'];
        }

        $categories = Category::active()->whereHas('images', function ($query) {
            $query->approved()->hasActiveFiles();
        })->get();


        return view('Template::image_search', compact('pageTitle', 'images', 'collections', 'imageCount', 'collectionCount', 'categories'));
    }


    private function getImages($request, $onlyCount = false)
    {
        $images = $this->searchImages($request);

        $data['imageCount'] = (clone $images)->count();

        if (!$onlyCount) {
            $data['images'] = $images->paginate(getPaginate(25));
        }
        return $data;
    }


    private function getCollections($request, $onlyCount = false)
    {
        $collections             = $this->searchCollections($request);
        $data['collectionCount'] = (clone $collections)->count();
        if (!$onlyCount) {
            $data['collections'] = $collections->paginate(getPaginate(25));
        }
        return $data;
    }


    private function searchCollections($request)
    {
        $fileType = FileType::where('slug', $request->file_type)->first();

        if ($fileType) {
            $collections = Collection::public()->active()->with(['images', 'user'])
                ->whereHas('images', function ($image) use ($fileType) {
                    $image->where('file_type_id', $fileType->id);
                })->whereDoesntHave('images', function ($image) use ($fileType) {
                    $image->where('file_type_id', '!=', $fileType->id);
                });
        } else {
            $collections = Collection::public()->active()->with(['images', 'user'])->whereHas('images');
        }

        if ($request->category) {
            $category = $request->category;
            if ($category) {
                $collections = $collections->whereHas('images', function ($images) use ($category) {
                    $images->whereHas('category', function ($query) use ($category) {
                        $query->where('slug', $category);
                    });
                });
            }
        }
        if ($request->has('is_free')) {
            $isFree = $request->is_free;
            if ($isFree) {
                $collections = $collections->whereHas('images.files', function ($query) use ($isFree) {
                    $query->where('is_free', $isFree);
                });
            }
        }

        if ($request->has('color')) {
            $colors = $request->color;
            if ($colors) {
                $collections = $collections->whereHas('images', function ($query) use ($colors) {
                    $query->whereJsonContains('colors', $colors);
                });
            }
        }

        if ($request->has('tag')) {
            $tags = $request->tags;
            if ($tags) {
                $collections = $collections->whereHas('images', function ($query) use ($tags) {
                    $query->whereJsonContains('tags', $tags);
                });
            }
        }

        if ($request->has('filter')) {
            $filter      = $request->filter;
            $collections = $collections->whereHas('images', function ($images) use ($filter) {
                $images->where(function ($query) use ($filter) {
                    $query->where('title', 'like', "%$filter%")->orWhere(function ($query) use ($filter) {
                        $query->whereJsonContains('tags', $filter);
                    })->orWhere(function ($query) use ($filter) {
                        $query->whereHas('category', function ($category) use ($filter) {
                            $category->where('name', 'like', "%$filter%");
                        })->orWhereHas('user', function ($user) use ($filter) {
                            $user->where('username', 'like', "%$filter%")
                                ->orWhere('firstname', 'like', "%$filter%")
                                ->orWhere('lastname', 'like', "%$filter%");
                        })->orWhereHas('collections', function ($collections) use ($filter) {
                            $collections->where('title', 'like', "%$filter%");
                        });
                    });
                });
            });
        }

        //last filter
        if ($request->has('period')) {
            $period      = $request->period;
            $collections = $collections->whereHas('images', function ($query) use ($period) {
                $query->where('upload_date', '>=', Carbon::now()->subMonth($period));
            });
        }

        if ($request->has('popular')) {
            $collections = $collections->withSum('images as total_downloads', 'total_downloads')->orderBy('total_downloads', 'DESC');
        }

        if (!$request->has('sort_by')) {
            $collections = $collections->orderBy('id', 'desc');
        } else {
            $collections = $collections->orderBy('id', 'asc');
        }

        return $collections;
    }



    private function searchImages($request)
    {
        $images = Image::approved()->where(function ($q) {
            $q->where('user_id', auth()->id())->orWhereHas('category', function ($category) {
                $category->active();
            });
        })->with('likes', 'user')->withCount(['files as premium' => function ($file) {
            $file->active()->premium();
        }]);


        if ($request->category) {
            $category = $request->category;
            $images   = $images->whereHas('category', function ($query) use ($category) {
                $query->where('slug', $category)->where('status', Status::ENABLE);
            });
        }

        if ($request->file_type) {

            $fileType = FileType::where('slug', $request->file_type)->first();
            if ($fileType) {
                $images = $images->where('file_type_id', $fileType->id);
            }
        }

        if ($request->has('tag') && $request->tag != 'all') {
            $images->whereRaw("JSON_CONTAINS(tags, ?)", [json_encode($request->tag)]);
        }
        // filter by extensions
        if ($request->has('extension') && $request->extension != 'all') {
            $images->whereJsonContains('extensions', $request->extension);
        }

        if ($request->has('is_free')) {
            if ($request->is_free) {
                $isFree = $request->is_free;
                $images = $images->whereHas('files', function ($q) use ($isFree) {
                    $q->active()->where('is_free', $isFree);
                });
            }
        }

        if ($request->has('color')) {
            $images = $images->whereJsonContains('colors', $request->color);
        }

        if ($request->has('tag')) {
            $images = $images->whereJsonContains('tags', $request->tag);
        }



        if ($request->has('filter') && $request->filter) {
            $filter = $request->filter;
            $images = $images->where(function ($query) use ($filter) {
                $query->where('title', 'like', "%$filter%")->orWhere(function ($query) use ($filter) {
                    $query->whereJsonContains('tags', $filter);
                })->orWhere(function ($query) use ($filter) {
                    $query->whereHas('category', function ($category) use ($filter) {
                        $category->where('name', 'like', "%$filter%");
                    })->orWhereHas('user', function ($user) use ($filter) {
                        $user->where('username', 'like', "%$filter%")
                            ->orWhere('firstname', 'like', "%$filter%")
                            ->orWhere('lastname', 'like', "%$filter%");
                    })->orWhereHas('collections', function ($collections) use ($filter) {
                        $collections->where('title', 'like', "%$filter%");
                    });
                });
            });
        }

        //last filter
        if ($request->has('period')) {
            $images = $images->where('upload_date', '>=', Carbon::now()->subMonth($request->period));
        }

        if ($request->has('popular')) {
            $images = $images->popular();
        }

        if (!$request->has('sort_by')) {
            $images = $images->orderBy('id', 'desc');
        } else {
            $images = $images->orderBy('id', 'asc');
        }

        return $images;
    }


    public function getInvoice($type, $trx, $id)
    {

        abort_if(!in_array($type, ['image', 'plan']), 404);


        $transaction = Transaction::with('user')->where('trx', $trx)->first();


        if (!$transaction) {

            abort(404);
        }

        $image = null;
        $plan  = null;
        if ($type == 'image') {
            $image = Image::findOrFail($id);
        } else {
            $plan = Plan::findOrFail($id);
        }
        $pageTitle = 'Invoice#' . $transaction->trx;
        return view('Template::invoice', compact('transaction', 'pageTitle', 'image', 'plan'));
    }


    public function memberFollowers($username)
    {
        $member      = User::where('username', $username)->firstOrFail();
        $followerIds = Follow::where('following_id', $member->id)->pluck('user_id');
        $members     = User::whereIn('id', $followerIds)->withCount('images')->orderBy('images_count', 'DESC')->paginate(getPaginate());
        $heading     = $member->fullname . '\'s followers';
        $pageTitle   = 'Followers';

        return view('Template::member.all', compact('pageTitle', 'members', 'heading'));
    }

    public function memberFollowings($username)
    {
        $member       = User::where('username', $username)->firstOrFail();
        $followingIds = Follow::where('user_id', $member->id)->pluck('user_id');
        $members      = User::whereIn('id', $followingIds)->withCount('images')->orderBy('images_count', 'DESC')->paginate(getPaginate());
        $heading      = $member->fullname . '\'s followings';
        $pageTitle    = 'Followings';

        return view('Template::member.all', compact('pageTitle', 'members', 'heading'));
    }
}
