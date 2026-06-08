<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaFile;
use Illuminate\Http\Request;
use App\Services\FileManagerService;

class FileManagerController extends Controller
{

    # constructor
    public function __construct()
    {
        $this->middleware(['permission:file_manager'])->only(['index']);
    }

    # file manager index
    public function index(Request $request)
    {
        return view('backend.admin.file-manager.index');
    }

    # upload files
    public function upload(Request $request)
    {
        $response = FileManagerService::upload($request);
        return $response['result'];
    }

    # get uploaded files
    public function getUploadedFiles(Request $request)
    {
        $response = FileManagerService::getUploadedFiles($request);
        return $response['result'];
    }

    # delete files
    public function destroy(Request $request, $id)
    {
        $response = FileManagerService::destroy($request, $id);
        return $response['result'];
    }

    # for input preview box
    public function getPreviewFiles(Request $request)
    {
        $ids = explode(',', $request->ids);
        $files = MediaFile::whereIn('id', $ids)->get();
        return $files;
    }

    # download file
    public function attachmentDownload($id)
    {
        //  
    }

    # remove background
    public function backgroundRemove(Request $request, $id)
    {
        $response = FileManagerService::backgroundRemove($request, $id);
        return $response['result'];
    }

    # alt Text
    public function altText(Request $request, $id)
    {
        $response = FileManagerService::altText($request, $id);
        return $response['result'];
    }
}
