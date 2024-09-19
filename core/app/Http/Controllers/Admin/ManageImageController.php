<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Models\Image;
use App\Models\Category;
use App\Models\Download;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Lib\DownloadFile;
use App\Models\Color;
use App\Models\FileType;
use App\Models\ImageFile;
use App\Models\Reason;

class ManageImageController extends Controller
{
    public function all()
    {
        $pageTitle = 'All Images';
        $images    = $this->imageData();
        return view('admin.images.list', compact('pageTitle', 'images'));
    }

    public function pending()
    {
        $pageTitle = 'Pending Images';
        $images    = $this->imageData('pending');
        return view('admin.images.list', compact('pageTitle', 'images'));
    }

    public function rejected()
    {
        $pageTitle = 'Rejected Images';
        $images    = $this->imageData('rejected');
        return view('admin.images.list', compact('pageTitle', 'images'));
    }

    public function approved()
    {
        $pageTitle = 'Approved Images';
        $images    = $this->imageData('approved');
        return view('admin.images.list', compact('pageTitle', 'images'));
    }

    public function updateFeature($id)
    {
        $image = Image::findOrFail($id);

        if ($image->status != Status::IMAGE_APPROVED) {
            $notify[] = ['error', 'Image should be approved first'];
            return back()->withNotify($notify);
        }

        $notification       = 'Image un-featured successfully';
        $image->is_featured = $image->is_featured ? Status::DISABLE : Status::ENABLE;
        $image->save();

        if ($image->is_featured) {
            $notification = 'Image featured successfully';
        }

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    public function details($id)
    {
        $image      = Image::with('category', 'user', 'files')->withSum('files as totalDownloads', 'total_downloads')->findOrFail($id);
        $pageTitle  = 'Image Details - ' . $image->title;
        $categories = Category::active()->orderBy('name', 'asc')->get();
        $colors     = Color::orderBy('name', 'desc')->get();

        $extensions = getFileExt($image->file_type_id);
        $reasons    = Reason::all();
        $fileTypes  = FileType::active()->get();

        return view('admin.images.detail', compact('pageTitle', 'image', 'categories', 'colors', 'extensions', 'reasons', 'fileTypes'));
    }

    public function downloadLog($id)
    {
        $image     = Image::findOrFail($id);
        $logs      = Download::where('image_file_id', $image->id)->with('user', 'contributor', 'image')->paginate(getPaginate());
        $pageTitle = 'Download logs - ' . $image->title;
        return view('admin.images.download_log', compact('pageTitle', 'logs'));
    }

    public function update(Request $request, $id)
    {

        $fileType = FileType::active()->find($request->file_type);


        if (!$fileType) {
            $notify[] = ['error', 'File type not found'];
            return back()->withNotify($notify);
        }


        $colors = Color::select('color_code')->pluck('color_code')->toArray() ?? [];

        $request->validate([
            'category'     => 'required|integer|gt:0',
            'title'        => 'required|string|max:40',
            'description'  => 'required|string',
            'resolution'   => 'required|array',
            'resolution.*' => 'required|string|max:40',
            'tags'         => 'required|array',
            'tags.*'       => 'required|string',
            'extensions'   => 'required|array',
            'extensions.*' => 'required|string',
            'colors'       => 'required|array',
            'colors.*'     => 'required|in:' . implode(',', $colors),
            'status'       => 'nullable|in:0,1,3',
            'statusFile'   => 'required|array',
            'statusFile.*' => 'required|in:0,1',
            'is_free'      => 'required|array',
            'is_free.*'    => 'required|in:0,1',                        //0 = Premium, 1=Free
            'price'        => 'array',
            'price.*'      => 'nullable|numeric',
            'reason'       => 'required_if:status,3',
            'file_id'      => 'required|array',
            'file_id.*'    => 'required|integer',
        ], [
            'extensions.*.in' => 'Extensions are invalid',
            'colors.*.in'     => 'Colors are invalid'
        ]);

        $category = Category::active()->find($request->category);

        if (!$category) {
            $notify[] = ['error', 'Category not found'];
            return back()->withNotify($notify);
        }

        $image = Image::with('category')->findOrFail($id);

        $image->category_id  = $request->category;
        $image->file_type_id = $request->file_type;
        $image->title        = $request->title;
        $image->description  = $request->description;
        $image->tags         = $request->tags;
        $image->extensions   = $request->extensions;
        $image->colors       = $request->colors;
        $image->attribution  = $request->attribution ? Status::ENABLE : Status::DISABLE;
        $image->status       = $request->status;
        $image->admin_id     = auth('admin')->id();
        $image->reviewer_id  = 0;

        if ($image->status == 3) {
            $image->reason = $request->reason;
        }

        $image->save();

        foreach ($request->resolution ?? [] as $key => $value) {
               $imageFile                                       = ImageFile::findOrFail($request->file_id[$key]);
               $imageFile->resolution                           = $value;
               $imageFile->is_free                              = $request->is_free[$key];
               $imageFile->status                               = $request->statusFile[$key];
               $imageFile->price                                = $request->price[$key];
            if ($request->price[$key] == 0) $imageFile->is_free = 1;
            $imageFile->save();
        }


        if ($image->status == 3) {
            notify($image->user, 'IMAGE_REJECT', [
                'title'    => $image->title,
                'category' => $image->category->name,
                'reason'   => $image->reason
            ]);
        } elseif ($image->status == 1) {
            notify($image->user, 'IMAGE_APPROVED', [
                'title'    => $image->title,
                'category' => $image->category->name
            ]);
        }

        $notify[] = ['success', 'Image updated successfully'];
        return back()->withNotify($notify);
    }

    public function downloadFile($id)
    {
        $imageFile = ImageFile::findOrFail($id);
        return DownloadFile::download($imageFile);
    }

    protected function imageData($scope = null)
    {
        $images = Image::query();
        if ($scope) {
            $images = Image::$scope();
        }
        return  $images->searchable(['title', 'category:name', 'user:username,firstname,lastname', 'collections:title', 'admin:username,name', 'reviewer:username,name'])->withSum('files as total_downloads', 'total_downloads')->orderBy('id', 'desc')->with('category', 'user')->paginate(getPaginate());
    }
}
