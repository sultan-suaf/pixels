<?php

namespace App\Http\Controllers\Admin;

use App\Models\FileType;
use Illuminate\Http\Request;
use App\Rules\FileTypeValidate;
use App\Http\Controllers\Controller;

class FileTypeController extends Controller
{
    public function all()
    {
        $pageTitle  = 'All File Type';
        $fileTypes = FileType::searchable(['name'])->paginate(getPaginate());
        return view('admin.filetype.index', compact('pageTitle', 'fileTypes'));
    }

    public function store(Request $request, $id = 0)
    {

        $this->validation($request, $id);

        if (!$id) {
            $notification = 'Filetype added successfully';
            $fileType     = new FileType();
        } else {
            $notification = 'FileType updated successfully';
            $fileType     = FileType::findOrFail($id);
        }

        if ($request->hasFile('image')) {
            try {
                $path  = getFilePath('fileType');
                $size  = getFileSize('fileType');
                $image = fileUploader($request->image, $path, $size, $fileType->image);

                $fileType->image = $image;
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload the image'];
                return back()->withNotify($notify);
            }
        }

        if ($request->hasFile('collection_image')) {
            try {
                $path  = getFilePath('fileTypeCollection');
                $size  = getFileSize('fileTypeCollection');
                $collection_image = fileUploader($request->collection_image, $path, $size, $fileType->collection_image);
                $fileType->collection_image = $collection_image;
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload the image'];
                return back()->withNotify($notify);
            }
        }

        if ($request->hasFile('video')) {
            try {
                $videoLocation = getFilePath('fileTypeVideo');
                $uploadedVideoName = fileUploader($request->video, $videoLocation, null, $fileType->video);
                $fileType->video = $uploadedVideoName;
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload the video'];
                return back()->withNotify($notify);
            }
        }

        $fileType->icon                     = $request->icon;
        $fileType->name                     = $request->name;
        $fileType->slug                     = $request->slug;
        $fileType->supported_file_extension = $request->file_extension;
        $fileType->save();

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        return FileType::changeStatus($id);
    }

    private function validation($request, $id)
    {
        $imageValidation = $id ? 'nullable'  : 'required';

        $request->validate([
            'name'             => 'required|unique:file_types,name,' . $id,
            'slug'             => 'required|string|max:40|unique:file_types,slug,' . $id,
            'icon'             => 'required',
            'image'            => [$imageValidation, new FileTypeValidate(['jpg', 'jpeg', 'png'])],
            'collection_image' => [$imageValidation, new FileTypeValidate(['jpg', 'jpeg', 'png'])],
            'video'            => ['nullable', new FileTypeValidate(['mp4', '3gp']), "max:15360"],
            'file_extension'   => 'required|array|min:1'
        ]);
    }

    public function videoRemove($id)
    {
        $fileType = FileType::findOrFail($id);
        if ($fileType->video) {
            $filePath = getFilePath('fileTypeVideo');
            removeFile($filePath . '/' . $fileType->video);
            $fileType->video = null;
            $fileType->save();

            $notify[] = ['success', 'Video delete successfully'];
            return back()->withNotify($notify);
        }

        $notify[] = ['error', 'Video doesn\'t exist'];
        return back()->withNotify($notify);
    }
}
