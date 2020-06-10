<?php
namespace App\Events;

class VideoDeleted
{
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