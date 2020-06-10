<?php
namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

trait AssessmentsTrait
{
    //Метод для сохранения лайка/дизлайка
    public function toggleAssessment(Request $request, $entity)
    {
        $json['success'] = false;
        $type = $request->input('type');
        if($type) {
            $entity->toggleAssessment(null, $type);
            $json['success'] = true;
            $json['result'] = $entity->isAssessmented(null, $type);
            $json['assessment_count_likes'] = $entity->assessmentsCount();
            $json['assessment_count_dislikes'] = $entity->assessmentsCount('dislike');
        }

        return Response::json($json);
    }
}