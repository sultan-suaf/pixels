<?php

namespace App\Lib;

class DownloadFile
{
    public static function download($file)
    {
        abort_if($file->file == null, 404, 'File is no longer available.');
        $general   = gs();
        $filePath  = fileUrl($file->file);
        $parseUrl  = parse_url($filePath);
        $extension = getExtension(@$parseUrl['path'] ?? $filePath);
        $fileName  = "{$general->site_name}_{$file->track_id}_{$file->resolution}.{$extension}";
        $fileName = str_replace('/', '-', $fileName);

        if ($general->storage_type == 1) {
            $headers = [
                'Content-Type' => 'application/octet-stream',
                'Cache-Control' => 'no-store, no-cache'
            ];
            file_get_contents($filePath);
            return response()->download($filePath, $fileName, $headers);
        } else {
            header('Content-type: application/octet-stream');
            header("Content-Disposition: attachment; filename=" . $fileName);

            while (ob_get_level()) {
                ob_end_clean();
            }
            readfile($filePath);
        }
    }

    public static function downloadVideo($file)
    {
        abort_if($file->video == null, 404, 'File is no longer available.');
        $general   = gs();
        $filePath  = VideoFileUrl($file->video);
        $parseUrl  = parse_url($filePath);
        $extension = getExtension(@$parseUrl['path'] ?? $filePath);
        $fileName  = "{$general->site_name}_{$file->track_id}_{$file->resolution}.{$extension}";
        $fileName = str_replace('/', '-', $fileName);

        if ($general->storage_type == 1) {
            $headers = [
                'Content-Type' => 'application/octet-stream',
                'Cache-Control' => 'no-store, no-cache'
            ];
            file_get_contents($filePath);
            return response()->download($filePath, $fileName, $headers);
        } else {
            header('Content-type: application/octet-stream');
            header("Content-Disposition: attachment; filename=" . $fileName);

            while (ob_get_level()) {
                ob_end_clean();
            }
            readfile($filePath);
        }
    }
}
