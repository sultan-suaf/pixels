<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Models\Image;
use App\Models\Collection;
use Illuminate\Http\Request;
use App\Models\CollectionImage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CollectionController extends Controller
{
    public function all()
    {
        $user        = auth()->user();
        $pageTitle   = "All Collections";
        $collections = Collection::where('user_id', $user->id)->withCount('images')->orderBy('id', 'desc')->paginate(getPaginate());
return view('Template::user.collection.list', compact('pageTitle', 'collections'));
    }

    public function addCollection(Request $request)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:40'
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->all()]);
            }
        } else {
            $request->validate([
                'title'       => 'required|string|max:40',
                'description' => 'nullable|string|max:255',
                'is_public'   => 'required|in:0,1'
            ]);
        }

        $user       = auth()->user();
        $collection = Collection::where('user_id', $user->id)->where('title', $request->title)->first();

        if (!$collection) {
            $collection              = new Collection();
            $collection->user_id     = $user->id;
            $collection->description = $request->description ?? null;
            $collection->is_public   = $request->is_public ?? 1;
            $collection->title       = $request->title;
            $collection->save();
        }

        if ($request->ajax()) {
            return response()->json(['collection' => $collection]);
        }

        $notify[] = ['success', 'Collection added successfully'];
        return back()->withNotify($notify);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title'       => 'required|string|max:40',
            'description' => 'nullable|string|max:255',
            'is_public'   => 'required|in:0,1'
        ]);

        $collection              = Collection::where('user_id', auth()->id())->findOrFail($id);
        $collection->title       = $request->title;
        $collection->description = $request->description ?? null;
        $collection->is_public   = $request->is_public;
        $collection->save();

        $notify[] = ['success', 'Collection updated successfully'];
        return back()->withNotify($notify);
    }

    public function delete($id)
    {
        $collection = Collection::where('user_id', auth()->id())->findOrFail($id);
        $collection->collectionImage()->delete();
        $collection->delete();
        $notify[] = ['success', 'Collection deleted successfully'];
        return back()->withNotify($notify);
    }

    public function imageData(Request $request)
    {
        $image = Image::where('id', $request->image)->first();
        $html  = view('Template::partials.collection_content', compact('image'))->render();
        return response()->json(['html' => $html]);
    }

    public function addImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image'        => 'required|exists:images,id',
            'collection'   => 'nullable|array',
            'collection.*' => 'required',
        ], [
            'collection.*.required' => 'Select any collection'
        ]);


        if ($validator->fails()) {
            return response()->json([
                'error'   => $validator->errors()->all(),
                'success' => false,
            ]);
        }

        $user = auth()->user();

        if ($request->collection) {
            $user->collectionImages()->where('image_id', $request->image)->whereNotIn('collection_id', $request->collection)->delete();

            $collectionImage = [];
            foreach ($request->collection as $key => $collection) {
                $exist = CollectionImage::where('collection_id', $collection)->where('image_id', $request->image)->exists();

                if (!$exist) {
                    $collectionImage[$key]['collection_id'] = $collection;
                    $collectionImage[$key]['image_id']      = $request->image;
                    $collectionImage[$key]['created_at']    = Carbon::now();
                }
            }
            CollectionImage::insert($collectionImage);
            return response()->json([
                'image'   => $request->image,
                'tooltip' => 'Collected',
                'success' => true,
            ]);
        } else {
            $user->collectionImages()->where('image_id', $request->image)->delete();
            return response()->json(['image' => $request->image, 'tooltip' => 'Collect', 'success' => true]);
        }
    }
}
