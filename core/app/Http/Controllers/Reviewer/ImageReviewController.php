<?php

namespace App\Http\Controllers\reviewer;

use App\Http\Controllers\Controller;
use App\Lib\DownloadFile;
use App\Models\Image;
use App\Models\ImageFile;
use App\Models\Reason;
use Illuminate\Http\Request;

class ImageReviewController extends Controller
{

    public function pending()
    {
        $pageTitle = "Pending Images";
        $images    = $this->imageData('pending');
        return view('reviewer.image.list', compact('pageTitle', 'images'));
    }

    public function approved()
    {
        $pageTitle = "Approved Images";
        $images    = $this->imageData('approved');
        return view('reviewer.image.list', compact('pageTitle', 'images'));
    }

    public function rejected()
    {
        $pageTitle = "Rejected Images";
        $images    = $this->imageData('rejected');
        return view('reviewer.image.list', compact('pageTitle', 'images'));
    }

    public function detail($id)
    {
        $image     = Image::with(['category', 'user', 'files'])->findOrFail($id);
        $pageTitle = "Details of " . $image->title;
        $reasons   = Reason::all();
        return view('reviewer.image.detail', compact('image', 'pageTitle', 'reasons'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => "required|in:1,3",
            'reason' => 'required_if:status,3'
        ]);
        $image = Image::with('category')->findOrFail($id);

        $status             = $request->status == 3 ? 'rejected' : 'approved';
        $image->status      = $request->status;
        $image->reason      = $request->reason;
        $image->reviewer_id = auth('reviewer')->id();
        $image->admin_id    = 0;
        $image->save();

        if ($image->status == 3) {
            notify($image->user, 'IMAGE_REJECT', [
                'title'    => $image->title,
                'category' => $image->category->name,
                'reason'   => $image->reason
            ]);
        } else {
            notify($image->user, 'IMAGE_APPROVED', [
                'title'    => $image->title,
                'category' => $image->category->name
            ]);
        }

        $notify[] = ['success', 'Image ' . $status . ' successfully'];
        return to_route('reviewer.images.pending')->withNotify($notify);
    }

    public function downloadFile($id)
    {
        $imageFile = ImageFile::findOrFail($id);
        return DownloadFile::download($imageFile);
    }

    protected function imageData($scope = null)
    {
        if ($scope) {
            $images = Image::$scope();
        } else {
            $images = Image::query();
        }
        return  $images->searchable(['title', 'category:name', 'user:username,firstname,lastname', 'collections:title', 'admin:name,username', 'reviewer:name,username'])->withCount(['files as total_files'])->orderBy('id', 'desc')->with('category', 'user', 'admin', 'reviewer')->paginate(getPaginate());
    }
}
