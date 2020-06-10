<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;

class ImageUploaded
{
    use SerializesModels;

    private $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function path()
    {
        return $this->path;
    }
}
