<?php
namespace App\Events;

use App\Listeners\Listener;
use Illuminate\Support\Facades\Storage;

class DeletedImageListener extends Listener
{
    public function onImageDeleted(ImageDeleted $event)
    {
        Storage::disk('publicFolder')->delete($event->path());
    }
}
