<?php

use \Intervention\Image\Image;

if(!function_exists('resizeImage')) {
    function resizeImage(string $path, $width = null, $height = null, string $format = null)
    {
        return makeImage($path, $width, $height, 'cache', 'resize', $format);
    }
}

if(!function_exists('cropImage')) {
    function cropImage(string $path, $width = null, $height = null, string $format = null, $coordX = null, $coordY = null)
    {
        return makeImage($path, $width, $height, 'cache/cropped', 'crop', $format);
    }
}

if(!function_exists('encodeImage')) {
    function encodeImage(string $path, string $format)
    {
        return makeImage($path, null, null, 'cache/encoded', 'encode', $format);
    }
}

if(!function_exists('canBeEncoded')) {
    function canBeEncoded(string $path = null)
    {
        return $path && pathinfo($path, PATHINFO_EXTENSION) !== 'gif';
    }
}

if(!function_exists('makeImage')) {
    function makeImage(string $path, $width = null, $height = null, $cacheFolderName = 'cache', $callback = 'resize', string $format = null)
    {
        setlocale(LC_ALL, 'ru_RU.utf8');
		
        if (is_file(public_path($path))) {
            $fileBaseName = pathinfo($path, PATHINFO_BASENAME);
            $fileName = pathinfo($path, PATHINFO_FILENAME);
            $fileExt = pathinfo($path, PATHINFO_EXTENSION);
            if($fileExt === 'gif') {
                return $path;
            }

            if($format) {
                $fileExt = $format;
            }
            $path = public_path(pathinfo($path, PATHINFO_DIRNAME)) . '/';
            $cachePath = $path . $cacheFolderName . '/';
            if ($width === null && $height === null) {
                $cacheFileName = $fileName . 'waters.' . $fileExt;
            } else {
                $cacheFileName = $fileName . $width . 'x' . $height . '.' . $fileExt;
            }

            if (!file_exists($cachePath)) {
                mkdir($cachePath, 0777, true);
            }

            if (is_file($cachePath . $cacheFileName)) {
                return str_replace(public_path(), '', $cachePath) . $cacheFileName;
            } else {
                $img = Intervention\Image\Facades\Image::make($path . $fileBaseName);
                if(function_exists($callback)) {
                    call_user_func($callback, $img, $width, $height);
                }

                $img->save($cachePath . $cacheFileName);
                $img->destroy();

                return str_replace(public_path(), '', $cachePath) . $cacheFileName;
            }
        }

        return null;
    }
}

if(!function_exists('encode')) {
    function encode(Image $img, $format)
    {
        if($img->extension !== 'gif') {
            $img->encode($format);
        }

        return $img;
    }
}
if(!function_exists('resize')) {
    function resize(Image $img, $width, $height)
    {
        if($width === null && $height === null) {
            return $img;
        }

        if($width === null || $height === null) {
            return $img->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
            });
        }

        return $img->resize($width, $height);
    }
}

if(!function_exists('crop')) {
    function crop(Image $img, $width, $height, $coordX = null, $coordY = null)
    {

        $tmpHeight = $img->getHeight()/$height;
        $tmpWidth = $img->getWidth()/$width;

        if($tmpHeight > $tmpWidth) {
            resize($img, $width, null);
        } else {
            resize($img, null, $height);
        }

        return $img->crop($width, $height, $coordX, $coordY);
    }
}