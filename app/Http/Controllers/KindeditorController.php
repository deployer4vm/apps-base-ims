<?php

namespace App\Http\Controllers;

use App\Base\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class KindeditorController extends BaseController
{
    public function upload(Request $request)
    {
        $directory = strtolower((string) $request->query('dir', 'image'));
        if (!in_array($directory, ['image', 'images'], true)) {
            return $this->uploadAlert('Only image uploads are allowed.');
        }

        $validator = Validator::make($request->all(), [
            'imgFile' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:2048',
        ]);
        if ($validator->fails()) {
            return $this->uploadAlert($validator->errors()->first('imgFile'));
        }

        $path = $request->file('imgFile')->store('editor/images/'.date('Ymd'));
        if (!$path) {
            return $this->uploadAlert('File upload failed.');
        }

        return response()->json([
            'error' => 0,
            'url' => url('/storage/'.$path),
        ]);
    }

    private function uploadAlert($message)
    {
        return response()->json(['error' => 1, 'message' => $message], 422);
    }

    public function filemanager(Request $request)
    {
        $directory = strtolower((string) $request->query('dir', 'image'));
        if (!in_array($directory, ['', 'image', 'images'], true)) {
            return response()->json(['error' => 1, 'message' => 'Invalid directory.'], 422);
        }

        $requestedPath = trim((string) $request->query('path', ''), '/');
        if (strpos($requestedPath, '..') !== false || strpos($requestedPath, '\\') !== false) {
            return response()->json(['error' => 1, 'message' => 'Invalid path.'], 422);
        }

        $root = 'editor/images';
        $currentPath = $root.($requestedPath !== '' ? '/'.$requestedPath : '');
        if (!Storage::exists($currentPath)) {
            Storage::makeDirectory($currentPath);
        }

        $order = strtolower((string) $request->query('order', 'name'));
        if (!in_array($order, ['name', 'size', 'type'], true)) {
            $order = 'name';
        }

        $allowedExtensions = ['gif', 'jpg', 'jpeg', 'png', 'webp'];
        $fileList = [];

        foreach (Storage::directories($currentPath) as $path) {
            $fileList[] = [
                'is_dir' => true,
                'has_file' => count(Storage::allFiles($path)) > 0,
                'filesize' => 0,
                'is_photo' => false,
                'filetype' => '',
                'filename' => basename($path),
                'datetime' => date('Y-m-d H:i:s'),
            ];
        }

        foreach (Storage::files($currentPath) as $path) {
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            if (!in_array($extension, $allowedExtensions, true)) {
                continue;
            }
            $fileList[] = [
                'is_dir' => false,
                'has_file' => false,
                'filesize' => Storage::size($path),
                'dir_path' => '',
                'is_photo' => true,
                'filetype' => $extension,
                'filename' => basename($path),
                'datetime' => date('Y-m-d H:i:s', Storage::lastModified($path)),
            ];
        }

        usort($fileList, function ($a, $b) use ($order) {
            if ($a['is_dir'] !== $b['is_dir']) {
                return $a['is_dir'] ? -1 : 1;
            }
            if ($order === 'size') {
                return $a['filesize'] <=> $b['filesize'];
            }
            if ($order === 'type') {
                return strcmp($a['filetype'], $b['filetype']);
            }
            return strcmp($a['filename'], $b['filename']);
        });

        return response()->json([
            'moveup_dir_path' => $requestedPath === ''
                ? ''
                : preg_replace('/(^|\/)[^\/]+\/?$/', '', $requestedPath),
            'current_dir_path' => $requestedPath === '' ? '' : $requestedPath.'/',
            'current_url' => url('/storage/'.$currentPath).'/',
            'total_count' => count($fileList),
            'file_list' => $fileList,
        ]);
    }
}
