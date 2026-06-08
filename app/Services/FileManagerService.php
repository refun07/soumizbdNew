<?php

namespace App\Services;

use App\Models\MediaFile;
use App\Models\User;
use Auth;
use File;
use Http;
use Storage;
use Intervention\Image\Laravel\Facades\Image;

class FileManagerService
{
    # add new data
    public static function upload($request)
    {
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [],
        ];

        try {
            $type = array(
                "jpg" => "image",
                "jpeg" => "image",
                "png" => "image",
                "svg" => "image",
                "webp" => "image",
                "gif" => "image",
                "mp4" => "video",
                "mpg" => "video",
                "mpeg" => "video",
                "webm" => "video",
                "ogg" => "video",
                "avi" => "video",
                "mov" => "video",
                "flv" => "video",
                "swf" => "video",
                "mkv" => "video",
                "wmv" => "video",
                "wma" => "audio",
                "aac" => "audio",
                "wav" => "audio",
                "mp3" => "audio",
                "zip" => "archive",
                "rar" => "archive",
                "7z" => "archive",
                "doc" => "document",
                "txt" => "document",
                "docx" => "document",
                "pdf" => "document",
                "csv" => "document",
                "xml" => "document",
                "ods" => "document",
                "xlr" => "document",
                "xls" => "document",
                "xlsx" => "document"
            );

            if ($request->hasFile('media_file')) {
                $mediaFile = new MediaFile;
                $mediaFile->media_name = null;

                $arr = explode('.', $request->file('media_file')->getClientOriginalName());

                for ($i = 0; $i < count($arr) - 1; $i++) {
                    if ($i == 0) {
                        $mediaFile->media_name .= $arr[$i];
                    } else {
                        $mediaFile->media_name .= "." . $arr[$i];
                    }
                }

                $mediaFile->media_file = $request->file('media_file')->store('uploads/all');
                $mediaFile->user_id = Auth::user()->id;
                $mediaFile->media_extension = $request->file('media_file')->getClientOriginalExtension();
                if (isset($type[$mediaFile->media_extension])) {
                    $mediaFile->media_type = $type[$mediaFile->media_extension];
                } else {
                    $mediaFile->media_type = "others";
                }
                $mediaFile->media_size = $request->file('media_file')->getSize();
                $mediaFile->save();

                // convert to webp if the file extension is jpeg, jpg or png
                if (in_array($mediaFile->media_extension, ['jpeg', 'jpg', 'png']) && extension_loaded('gd') && config('app.demo_mode') != 'On' && getSetting('imageToWebp', 1) != '0') {
                    $newMediaFileName = explode('.', $mediaFile->media_file)[0] . '.webp';

                    //convert to webp
                    Image::read(public_path($mediaFile->media_file))->toWebp(self::webpQuality())->save(public_path($newMediaFileName));

                    // update new file information
                    $mediaFile->media_file = $newMediaFileName;
                    $mediaFile->media_extension = 'webp';
                    $mediaFile->media_size = File::size(public_path($mediaFile->media_file));
                    $mediaFile->save();
                }

                $data = [
                    'status'        => 200,
                    'message'       => '',
                    'result'        => $mediaFile,
                ];
            } else {
                $data = [
                    'status'        => 200,
                    'message'       => '',
                    'result'        => null,
                ];
            }

            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status'    => 403,
                'message'   => translate('Something went wrong'),
                'result'    => [],
            ];
            return $data;
        }
    }

    # get uploaded files
    public static function getUploadedFiles($request)
    {
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [],
        ];

        $user = user();
        if ($user->user_type == "admin" || $user->user_type == "staff") {
            $userIds = User::where('shop_id', $user->shop_id)->pluck('id');
            $mediaFiles = MediaFile::whereIn('user_id', $userIds);
        } else {
            $mediaFiles = MediaFile::where('user_id', Auth::user()->id);
        }
        if ($request->search != null) {
            $mediaFiles->where('media_name', 'like', '%' . $request->search . '%');
        }
        if ($request->sort != null) {
            switch ($request->sort) {
                case 'newest':
                    $mediaFiles->orderBy('created_at', 'desc');
                    break;
                case 'oldest':
                    $mediaFiles->orderBy('created_at', 'asc');
                    break;
                case 'smallest':
                    $mediaFiles->orderBy('media_size', 'asc');
                    break;
                case 'largest':
                    $mediaFiles->orderBy('media_size', 'desc');
                    break;
                default:
                    // code...
                    break;
            }
        }

        $data = [
            'status'    => 200,
            'message'   => '',
            'result'    => $mediaFiles->paginate($request->limit)->appends(request()->query()),
        ];

        return $data;
    }

    # delete files
    public static function destroy($request, $id)
    {
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [],
        ];

        $mediaFile = MediaFile::findOrFail($id);

        if (auth()->user()->user_type == 'seller' && $mediaFile->user_id != auth()->user()->id) {
            $data =  [
                'success'   => false,
                'status'    => 402,
                'message'   => translate("You don't have permission for deleting this!"),
                'result'    => null
            ];

            return $data;
        }
        try {
            if (env('FILESYSTEM_DRIVER') == 's3') {
                Storage::disk('s3')->delete($mediaFile->media_file);
                if (file_exists(public_path() . '/' . $mediaFile->media_file)) {
                    unlink(public_path() . '/' . $mediaFile->media_file);
                }
            } else {
                unlink(public_path() . '/' . $mediaFile->media_file);
            }
            $mediaFile->delete();
        } catch (\Exception $e) {
            $mediaFile->delete();
        }

        $data = [
            'status'    => 200,
            'message'   => translate('File deleted successfully'),
            'result'    => FileManagerService::getUploadedFiles($request),
        ];
        return $data;
    }

    # remove background
    public static function backgroundRemove($request, $id)
    {
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [],
        ];

        $mediaFile = MediaFile::findOrFail($id);

        $newMediaFile = $mediaFile->replicate();
        $newMediaFile->media_file = explode('.', $mediaFile->media_file)[0] . '-transparent' . '.png';

        if ($mediaFile->media_type == 'image') {
            $response = Http::timeout(300)
                ->sink(public_path($newMediaFile->media_file))
                ->withHeaders([
                    'X-Api-Key' => 'token1'
                ])
                ->attach(
                    'image_file',
                    file_get_contents(public_path($mediaFile->media_file)),
                    $mediaFile->media_name . '.' . $mediaFile->media_extension
                )->post('https://bg-remover.epikcoders.com/api/removebg');

            if ($response->successful()) {
                // if gd extension is not loaded
                $newMediaFile->media_name = $mediaFile->media_name . '-transparent';
                $newMediaFile->media_extension = "png";
                $newMediaFile->media_size = File::size(public_path($newMediaFile->media_file));
                $newMediaFile->save();

                // convert to webp
                if (extension_loaded('gd') && config('app.demo_mode') != 'On' && getSetting('imageToWebp', 1) != '0') {
                    $webpMediaFile = explode('.', $mediaFile->media_file)[0] . '-transparent' . '.webp';

                    Image::read(public_path($newMediaFile->media_file))->toWebp(self::webpQuality())->save(public_path($webpMediaFile));

                    $newMediaFile->media_file = $webpMediaFile;
                    $newMediaFile->media_extension = "webp";
                    $newMediaFile->media_size = File::size(public_path($newMediaFile->media_file));
                    $newMediaFile->save();
                }
            }
        }

        $data = [
            'status'    => 200,
            'message'   => translate('Background removed successfully'),
            'result'    => FileManagerService::getUploadedFiles($request),
        ];

        return $data;
    }

    # alt text
    public static function altText($request, $id)
    {
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [],
        ];

        $mediaFile = MediaFile::findOrFail($id);
        if ($mediaFile->media_type == 'image') {
            $mediaFile->alt_text = $request->alt_text;
            $mediaFile->save();
        }

        $data = [
            'status'    => 200,
            'message'   => translate('Alternative text updated successfully'),
            'result'    => FileManagerService::getUploadedFiles($request),
        ];

        return $data;
    }

    private static function webpQuality(): int
    {
        $quality = getSetting('mediaImageQuality') ?: 90;
        $quality = is_numeric($quality) ? (int) $quality : 90;

        return max(10, min(100, $quality));
    }
}
