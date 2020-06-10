<?php

namespace App\Listeners;

use App\Events\ImageUploaded;
use Grechanyuk\TinyPNG\Facades\TinyPNG;
use Intervention\Image\Facades\Image;
use UniSharp\LaravelFilemanager\Events\ImageWasUploaded;
use Imagick;

class UploadImageListener extends Listener
{
    public function onImageWasUploaded(ImageWasUploaded $event)
    {
        $path = $event->path();
        if (exif_imagetype($path)) {
            $this->optimizeImage($path);
        }
    }

    public function onImageUploaded(ImageUploaded $event)
    {
        $path = $event->path();
        if (exif_imagetype($path) === IMAGETYPE_JPEG || exif_imagetype($path) === IMAGETYPE_PNG) {
            $this->optimizeImage($path);
        }
    }

    //Оптимизируем фото
     private function optimizeImage(string $path)
    {
        $img = Image::make($path);
        $img->text(config('app.domain'), $img->getWidth()/2, $img->getHeight()/2, function ($font) use($img) {
            $font->file(public_path('fonts/GothamPro.ttf'));
            $font->color([255, 255, 255, 0.5]);
            $font->size($img->getWidth()/10);
            $font->align('center');
            $font->valign('middle');
        });

        try {
            //Оптимизируем фото через tinypng.com
            TinyPNG::fromBuffer($img->encode('png'), $path);
        } catch (\InvalidArgumentException $e) {
            $img->save($path);
            \Log::warning('Can not optimize image', ['message' => $e->getMessage()]);
        }

        $img->destroy();
    } 

//Оптимизируем фото
/* private function optimizeImage(string $path)
{
   
  
    $img = Image::make($path);
    $img->text(config('app.domain'), $img->getWidth()/2, $img->getHeight()/2, function ($font) use($img) {
        $font->file(public_path('fonts/GothamPro.ttf'));
        $font->color([255, 255, 255, 0.5]);
        $font->size($img->getWidth()/10);
        $font->align('center');
        $font->valign('middle');
    });

    $img->save($path);

    $info = getimagesize($path);


    if ($info['mime'] == 'image/jpeg') 
    {
        $image = imagecreatefromjpeg($path);
        
    }
    elseif ($info['mime'] == 'image/gif') 
    {
        $image = imagecreatefromgif($path);
    }
    elseif ($info['mime'] == 'image/png') 
    {
        $image = imagecreatefrompng($path);
    }
    else
    {
        die('Unknown image gfile format');
    }
 
    //compress and save file to jpg
    imagejpeg($image, $path, 10);
    $img->destroy();
    imagedestroy($image);
} */
}