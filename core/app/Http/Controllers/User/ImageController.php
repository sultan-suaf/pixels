<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Models\Like;
use App\Models\Color;
use App\Models\Image;
use App\Models\Category;
use App\Constants\Status;
use App\Lib\DownloadFile;
use App\Models\ImageFile;
use App\Lib\StorageManager;
use Illuminate\Http\Request;
use App\Rules\FileTypeValidate;
use App\Http\Controllers\Controller;
use App\Models\FileType;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageController extends Controller
{
    public function all()
    {
        $pageTitle = "All Images";
        $images    = $this->imageData();
        return view('Template::user.image.list', compact('pageTitle', 'images'));
    }

    public function pending()
    {
        $pageTitle = "Pending Images";
        $images    = $this->imageData('pending');
        return view('Template::user.image.list', compact('pageTitle', 'images'));
    }

    public function rejected()
    {
        $pageTitle = "Rejected Images";
        $images    = $this->imageData('rejected');
        return view('Template::user.image.list', compact('pageTitle', 'images'));
    }

    public function approved()
    {
        $pageTitle = "Approved Images";
        $images    = $this->imageData('approved');
        return view('Template::user.image.list', compact('pageTitle', 'images'));
    }

    public function add()
    {
        $pageTitle  = "Upload Image";
        $categories = Category::active()->orderBy('name')->get();
        $colors     = Color::all();
        $images     = Image::approved()
            ->inrandomOrder()
            ->pluck('tags')
            ->toArray();
        $fileTypes = FileType::active()->get();
        $tags      = array_slice(array_unique(array_merge(...$images)), 0, 50);
        return view('Template::user.image.upload', compact('pageTitle', 'categories', 'colors', 'tags', 'fileTypes'));
    }


    public function store(Request $request)
    {
        $user        = auth()->user();
        $general     = gs();
        $dailyUpload = Image::with('files')->where('user_id', $user->id)->whereDate('created_at', Carbon::now())->count();

        if ($general->upload_limit < $dailyUpload) {
            return response()->json([
                'status' => false,
                'error'  => 'Daily upload limit has been over'
            ]);
        }

        $validator = $this->validation($request, $general);


        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error'  => $validator->errors()->all()
            ]);
        }

        $category = Category::active()->find($request->category);
        if (!$category) {
            return response()->json([
                'status' => false,
                'error'  => 'Category not found'
            ]);
        }

        $fileType = FileType::active()->find($request->file_type);


        if (!$fileType) {
            return response()->json([
                'status' => false,
                'error'  => 'File Type not found'
            ]);
        }


        $tagCount = count($request->tags);

        if ($tagCount > 10) {
            return response()->json([
                'status' => false,
                'error'  => 'you can not use more than 10 tags'
            ]);
        }

        $image = new Image();

        $response = $this->processImageData($image, $request);

        if (array_key_exists('error', $response)) {
            $type    = 'error';
            $message = $response['error'];
            $status  = false;
        } else {
            $type    = 'success';
            $message = $response['success'];
            $status  = true;
        }

        return response()->json([
            'status' => $status,
            $type    => $message
        ]);
    }


    public function edit($id)
    {
        $image      = Image::with('files')->where('user_id', auth()->id())->findOrFail($id);
        $pageTitle  = 'Update image - ' . $image->title;
        $categories = Category::active()->orderBy('name')->get();
        $colors     = Color::all();
        $fileTypes  = FileType::active()->get();
        $images     = Image::approved()->inrandomOrder()->pluck('tags')->toArray();
        $tags       = array_slice(array_unique(array_merge(...$images)), 0, 50);

        return view('Template::user.image.upload', compact('pageTitle', 'categories', 'colors', 'image', 'tags', 'fileTypes'));
    }

    public function updateImage(Request $request, $id)
    {
        $user    = auth()->user();
        $general = gs();

        $image = Image::where('user_id', $user->id)->find($id);

        if (!$image) {
            return response()->json([
                'status' => false,
                'error'  => 'Resource not found'
            ]);
        }

        $validator = $this->validation($request, $general, true);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error'  => $validator->errors()->all()
            ]);
        }

        $category = Category::active()->find($request->category);

        if (!$category) {
            return response()->json([
                'status' => false,
                'error'  => 'Category not found'
            ]);
        }

        $fileType = FileType::active()->find($request->file_type);

        if (!$fileType) {
            return response()->json([
                'status' => false,
                'error'  => 'File type not found'
            ]);
        }


        if ($general->storage_type == 1) {
            if ($request->hasFile('photo')) {
                $photo      = getFilePath('stockImage') . '/' . $image->image_name;
                $photoThumb = getFilePath('stockImage') . '/' . $image->thumb;
                removeFile($photo);
                removeFile($photoThumb);
            }
        }

        $response = $this->processImageData($image, $request, true);

        if (array_key_exists('error', $response)) {
            $type    = 'error';
            $message = $response['error'];
            $status  = false;
        } else {
            $type    = 'success';
            $message = $response['success'];
            $status  = true;
        }

        return response()->json([
            'status' => $status,
            $type    => $message
        ]);
    }

    public function updateLike(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'image' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }

        $user  = auth()->user();
        $image = Image::where('id', $request->image)->first();

        if (!$image) {
            return response()->json(['error' => 'Image not found']);
        }

        $like = Like::where('image_id', $image->id)->where('user_id', $user->id)->first();

        if (!$like) {
            $like           = new Like();
            $like->user_id  = $user->id;
            $like->image_id = $image->id;
            $like->save();
            $image->total_like += 1;
        } else {
            $like->delete();
            $image->total_like -= 1;
        }

        $image->save();
        $userTotalLike = Image::where('user_id', $image->user_id)->sum('total_like');

        return response()->json(['status' => 'success', 'total_like' => $image->total_like, 'user_total_like' => $userTotalLike]);
    }

    public function download($id)
    {
        $imageFile = ImageFile::findOrFail($id);
        $user      = auth()->user()->load('downloads');

        if ($imageFile->image->user_id == $user->id || $user->downloads->where('image_file_id', $imageFile->id)->first()) {
            return DownloadFile::download($imageFile);
        } else {
            $notify[] = ['error', 'Invalid Request'];
            return to_route('user.image.all')->withNotify($notify);
        }
    }

    public function downloadVideo($id)
    {
        $imageFile = Image::findOrFail($id);
        $user      = auth()->user();

        if ($imageFile->video) {
            return DownloadFile::downloadVideo($imageFile);
        } else {
            $notify[] = ['error', 'Video not found'];
            return to_route('user.image.all')->withNotify($notify);
        }
    }

    public function changeImageFileActiveStatus($id)
    {
        $imageFile         = ImageFile::findOrFail($id);
        $imageFile->status = $imageFile->status ? Status::DISABLE : Status::ENABLE;
        $imageFile->save();

        $notify[] = ['success', 'Status change successfully'];
        return back()->withNotify($notify);
    }

    protected function imageData($scope = null)
    {
        $user   = auth()->user();
        $images = Image::where('user_id', $user->id);

        if ($scope) {
            $images = $images->$scope();
        }

        return $images->withSum('files as total_downloads', 'total_downloads')->with('category')->orderBy('id', 'desc')->paginate(getPaginate(21));
    }

    protected function processImageData($image, $request, $isUpdate = false)
    {
        $user    = auth()->user();
        $general = gs();

        $directory        = date("Y") . "/" . date("m") . "/" . date("d");
        $imageLocation    = getFilePath('stockImage') . '/' . $directory;
        $fileLocation     = getFilePath('stockFile') . '/' . $directory;
        $videoLocation    = getFilePath('stockVideo') . '/' . $directory;
        $removeFileMethod = $general->storage_type == 1 ?  'removeFile' : 'removeFileFromStorageManager';

        if ($request->hasFile('photo')) {

            try {
                $filename = uniqid() . time() . '.' . $request->photo->getClientOriginalExtension();

                $manager = new ImageManager(
                    new Driver()
                );

                $photo = $manager->read($request->photo);
                if ($general->watermark == Status::ENABLE) {
                    $watermark = $manager->read('assets/images/watermark.png')->cover($photo->width(), $photo->height())->rotate(45, 'transparent');
                    $photo->cover($photo->width(), $photo->height());
                    $photo->place($watermark, 'center', 0, 0, 25);
                }

                $thumb = $manager->read($request->photo);
                $thumb->resize(600, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

                $image->image_width  = $thumb->width();
                $image->image_height = $thumb->height();

                if ($general->storage_type == 1) {
                    if (!file_exists($imageLocation)) {
                        mkdir($imageLocation, 0755, true);
                    }

                    $photo->save($imageLocation . '/' . $filename);
                    $thumb->save($imageLocation . '/thumb_' . $filename);
                } else {
                    $servers              = [2 => "ftp", 3 => "wasabi", 4 => "do", 5 => "vultr"];
                    $server               = $servers[$general->storage_type];
                    $storageManager       = new StorageManager($server);
                    $storageManager->path = 'images/' . $directory;
                    $storageManager->old  = @$image->image_name;

                    $storageManager->uploadImage($photo, $filename);
                    $storageManager->uploadImage($thumb, $filename, true);
                }

                $image->image_name = $directory . '/' . $filename;
                $image->thumb      = $directory . '/thumb_' . $filename;
            } catch (\Exception $exp) {
                return ['error' =>  $exp->getMessage()];
            }
        }

        if ($request->hasFile('video')) {
            try {
                if ($general->storage_type == 1) {
                    $uploadedVideoName = fileUploader($request->video, $videoLocation);
                    if ($image->video) {
                        removeFile(getFilePath('stockVideo') . '/' . $image->video);
                    }
                } else {
                    $uploadedVideoName = storageManager($request->video, $videoLocation);
                    if ($image->video) {
                        removeFileFromStorageManager(getFilePath('stockVideo') . '/' . $image->video);
                    }
                }

                $image->video = $directory . '/' . $uploadedVideoName;
            } catch (\Exception $exp) {
                return ['error' => $exp->getMessage()];
            }
        }

        $storeFileArr = [];
        if ($request->hasFile('file')) {
            $filePath = 'files/' . $directory;
            try {
                //new added file for create
                foreach ($request->file as $value) {
                    $fileName       = $general->storage_type == 1 ? fileUploader($value, $fileLocation) : storageManager($value, $filePath);
                    $storeFileArr[] = $directory . '/' . $fileName;
                }
            } catch (\Exception $exp) {
                return ['error' => $exp->getMessage()];
            }
        }

        if ($isUpdate) {
            foreach ($request->removed_file ?? [] as $fileId) {
                $removedFile = ImageFile::where('id', $fileId)->where('image_id', $image->id)->first();
                if ($removedFile) {
                    $removeFileMethod($general->storage_type == 1 ? getFilePath('stockFile') . '/' . $removedFile->file : 'files/' . $removedFile->file);

                    $removedFile->delete();
                }
            }
            foreach ($request->old_file ?? [] as $key => $oldRequestFile) {
                $oldFile = ImageFile::where('id', $key)->where('image_id', $image->id)->first();
                if ($oldFile) {
                    $oldFile->resolution = @$oldRequestFile['resolution'];
                    $oldFile->is_free    = @$oldRequestFile['is_free'];
                    $oldFile->price      = @$oldRequestFile['price'];
                    $oldFile->status     = @$oldRequestFile['status'];
                    if (@$oldRequestFile['file']) {
                        try {
                            $removeFileMethod($general->storage_type == 1 ? getFilePath('stockFile') . '/' . $oldFile->file : 'files/' . $oldFile->file);

                            $fileName      = $general->storage_type == 1 ? fileUploader(@$oldRequestFile['file'], $fileLocation) : storageManager(@$oldRequestFile['file'], $filePath);
                            $oldFile->file = $directory . '/' . $fileName;
                        } catch (\Throwable $exp) {
                            return ['error' => $exp->getMessage()];
                        }
                    }
                    $oldFile->save();
                }
            }
        }

        $image->user_id     = $user->id;
        $image->category_id = $request->category;
        $image->title       = $request->title;
        $image->description = $request->description;

        $image->upload_date = now();
        $image->track_id    = getTrx();

        if ($isUpdate) {
            $image->status = Status::IMAGE_PENDING;
        }
        $image->status       = $general->auto_approval ? Status::IMAGE_APPROVED : Status::IMAGE_PENDING;
        $image->tags         = $request->tags;
        $image->extensions   = $request->extensions;
        $image->colors       = $request->colors;
        $image->file_type_id = $request->file_type;
        $image->save();

        foreach ($request->resolution ?? [] as $key => $value) {
            $imageFile             = new ImageFile();
            $imageFile->track_id   = getTrx();
            $imageFile->image_id   = $image->id;
            $imageFile->resolution = $value;
            $imageFile->is_free    = $request->is_free[$key];
            $imageFile->status     = $request->status[$key];
            $imageFile->price      = $request->price[$key];
            $imageFile->file       = $storeFileArr[$key];
            $imageFile->save();
        }


        $notification = 'Image uploaded successfully';
        if ($isUpdate) {
            $notification = 'Image updated successfully';
        }

        return ['success' => $notification];
    }


    protected function validation($request, $general, $isUpdate = false)
    {

        $fileTypeValidator = Validator::make($request->all(), [
            'file_type' => 'required|exists:file_types,id'
        ]);

        if ($fileTypeValidator->fails()) return $fileTypeValidator;

        $colors                  = Color::pluck('color_code')->implode(',');
        $photoValidation         = 'required';
        $fileValidation          = 'required';
        $imageFileDataValidation = 'required';

        if ($isUpdate) {
            $photoValidation         = 'nullable';
            $fileValidation          = 'nullable';
            $imageFileDataValidation = 'nullable';
        }

        $fileExtensions = getFileExt($request->file_type);

        $validator = Validator::make($request->all(), [
            'category'       => 'required|integer|gt:0',
            'photo'          => [$photoValidation, new FileTypeValidate(['jpg', 'png', 'jpeg'])],
            'file_type'      => 'required|exists:file_types,id',
            'video'          => ['nullable', new FileTypeValidate(['mp4', "3gp"]), "max:15360"],
            'file'           => 'nullable|array',
            'file.*'         => [$fileValidation, new FileTypeValidate(['zip'])],
            'title'          => 'required|max:40',
            'removed_file'   => 'nullable|array',
            'removed_file.*' => 'nullable|exists:image_files,id',
            'old_file'       => 'nullable|array',
            'old_file.*'     => 'nullable|array',

            'old_file.*.resolution' => 'required|string|max:40',
            'old_file.*.file'       => [$fileValidation, new FileTypeValidate(['zip'])],
            'old_file.*.is_free'    => 'required|in:0,1',
            'old_file.*.price'      => 'nullable|required_if:is_free,0|numeric|gte:0|lte:' . $general->image_maximum_price,
            'old_file.*.status'     => 'required|in:0,1',

            'resolution'   => $imageFileDataValidation . '|array',
            'resolution.*' => $imageFileDataValidation . '|string|max:40',
            'description'  => 'required|string',
            'tags'         => 'required|array',
            'tags.*'       => 'required|string',
            'colors'       => 'required|array',
            'colors.*'     => 'required|in:' . $colors,
            'extensions'   => 'required|array',
            'extensions.*' => 'required|string|in:' . implode(',', $fileExtensions),
            'is_free'      => $imageFileDataValidation . '|array',
            'is_free.*'    => $imageFileDataValidation . '|in:0,1',                                                                     //0 = Premium, 1=Free
            'status'       => $imageFileDataValidation . '|array',
            'status.*'     => $imageFileDataValidation . '|in:0,1',                                                                     //1 = Enable, 0=Disable
            'price'        => $imageFileDataValidation . '|array',
            'price.*'      => $imageFileDataValidation . '|required_if:is_free.*,0|numeric|gte:0|lte:' . $general->image_maximum_price
        ], [
            'price.required_if' => 'Price field is required if the image is premium'
        ]);

        return $validator;
    }
}
