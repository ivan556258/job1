<?php
namespace App\Events;

class ImageDeleted
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