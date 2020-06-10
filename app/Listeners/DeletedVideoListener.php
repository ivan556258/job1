<?php
namespace App\Events;

use App\Listeners\Listener;
use Illuminate\Support\Facades\Storage;

class DeletedVideoListener extends Listener
{
    public function onVideoDeleted(VideoDeleted $event)
    {
        Storage::disk('publicFolder')->delete($event->path());
    }
}
