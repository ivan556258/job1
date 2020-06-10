<?php
namespace App\Listeners;

class Listener
{
    public function handle($event)
    {
        $method = 'on' . class_basename($event);

        if (method_exists($this, $method)) {
            call_user_func([$this, $method], $event);
        }
    }
}