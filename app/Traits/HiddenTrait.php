<?php


namespace App\Traits;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Response;

trait HiddenTrait
{
    public function toggleHide(Model $entity)
    {
        $json['success'] = false;
        if(isset($entity->hidden)) {
            $entity->hidden = !$entity->hidden;
            $entity->save();
            $json['success'] = true;
            $json['status'] = $entity->hidden;
        }

        return Response::json($json);
    }
}