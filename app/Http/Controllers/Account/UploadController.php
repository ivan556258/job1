<?php

namespace App\Http\Controllers\Account;

use App\Events\ImageUploaded;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class UploadController extends Controller
{
    protected $imageRules = [
        'images.*.image' => 'mimetypes:image/jpeg,image/png,video/mp4'
    ];

    public function uploadImages(Request $request)
    {

        $json['success'] = false;
        $json['images'] = [];

        if ($request->input('type') == 'blob') {
            foreach ($request->input('images') as $img) {

                $i = $img['image'];
                $i = str_replace('data:image/jpeg;base64,', '', $i);

                $data = base64_decode($i);
                $name = uniqid() . '_' . uniqid() . '.jpeg';
                $path = '/var/www/masssage.ru/public/files/users/' . Auth::user()->id;
                $file = '/var/www/masssage.ru/public/files/users/' . Auth::user()->id . '/' . $name;
                $fileS = '/files/users/' . Auth::user()->id . '/' . $name;
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                file_put_contents($file, $data);

                $fileType = 'images';

                $json[$fileType][] = [
                    'image_url' => url($fileS),
                    'image' => $fileS
                ];
                event(new ImageUploaded($file));

                $json['success'] = true;
                return Response::json($json);

            }

        }
        if ($request->input('type') == 'noBlob') {
            $json['success'] = false;
            $v = $this->validateForm($request);
            if ($v->fails()) {
                $json['errors'] = $v->errors();
                return Response::json($json);
            }

            $json['images'] = [];

            foreach ($request->file('images') as $image) {
                $path = $image['image']->store('/files/users/' . Auth::user()->id, 'publicFolder');
                $fileType = 'images';
                if (strpos($image['image']->getMimeType(), 'video') === 0) {
                    $fileType = 'videos';
                }

                $json[$fileType][] = [
                    'image_url' => url($path),
                    'image' => $path
                ];
                event(new ImageUploaded($path));
            }

            $json['success'] = true;
            return Response::json($json);
        }
    }

    private function validateForm(Request $request)
    {
        $v = \Validator::make($request->all(), $this->imageRules);

        return $v;
    }
}
