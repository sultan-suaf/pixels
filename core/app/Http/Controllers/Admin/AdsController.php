<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ads;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class AdsController extends Controller
{

    public function index()
    {
        $pageTitle = 'All Ads';
        $ads       = Ads::searchable(['title', 'code'])->paginate(getPaginate());
        return view('admin.ads.index', compact('pageTitle', 'ads'));
    }


    public function store(Request $request, $id = 0)
    {

        $request->validate([
            'title'      => 'required|string',
            'size'       => 'required|in:' . implode(',', adSizes()),
            'type'       => 'required|in:0,1',
            'code'       => 'required_if:type,1|nullable|string',
            'target_url' => 'required_if:type,0|nullable|url',
            'image'      => ['nullable', new FileTypeValidate(['jpg', 'jpeg', 'png', 'gif'])],
        ]);
        $notification = 'Ads added successfully';
        $ads          = new Ads();
        if ($id) {
            $notification = 'Ads updated successfully';
            $ads          = Ads::findOrFail($id);
            if ($ads->type == 0 && $request->type == 1) fileManager()->removeFile(getFilePath('ads') . '/' . $ads->image);
        }

        $ads->title      = $request->title;
        $ads->size       = $request->size;
        $ads->type       = $request->type;
        $ads->code       = $ads->type == 1 ? $request->code  : '';
        $ads->target_url = $ads->type == 0 ? $request->target_url  : '';

        if ($ads->type == 0 && $request->hasFile('image')) {
            try {
                $ads->image = fileUploader($request->image, getFilePath('ads'), null, $ads->image);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }
        $ads->save();

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    public function delete($id)
    {
        $ads = Ads::findOrFail($id);
        if ($ads->image) fileManager()->removeFile(getFilePath('ads') . '/' . $ads->image);
        $ads->delete();
        $notify[] = ['success', 'Ads deleted successfully'];
        return back()->withNotify($notify);
    }
}
