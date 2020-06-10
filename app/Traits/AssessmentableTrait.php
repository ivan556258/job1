<?php
namespace App\Traits;

use App\UserAssessment;
use Illuminate\Support\Facades\Auth;

trait AssessmentableTrait
{
    //Получаем список лайков/дизлайков
    public function assessments(string $type = null)
    {
        $assessment = $this->morphMany(UserAssessment::class, 'assessmentable');
        if($type) {
            $assessment->where('type', $type);
        }
        return $assessment;
    }

    //Добавляем лайк/дизлайк
    public function addAssessment(int $user_id = null, string $type = 'like')
    {
        $this->assessments($type)->create([
            'user_id' => $user_id ? $user_id : Auth::id(),
            'type' => $type
        ]);
    }

    //Удаляем лайк/дизлайк
    public function removeAssessment(int $user_id = null, string $type = 'like')
    {
        $this->assessments($type)->where('user_id', ($user_id) ? $user_id : Auth::id())->delete();
    }

    //Ставим/убираем лайк/дизлайк
    public function toggleAssessment($user_id = null, string $type = 'like')
    {
        $assessment = $this->assessments()->first();
        if($assessment) {
            if($assessment->type == $type) {
                $this->removeAssessment($user_id, $type);
            } else {
                $this->removeAssessment($user_id, $assessment->type);
                $this->addAssessment($user_id, $type);
            }
        } else {
            $this->addAssessment($user_id, $type);
        }
    }

    //Проверка на лайк/дизлайк
    public function isAssessmented($user_id = null, string $type = 'like')
    {
        return $this->assessments($type)->where('user_id', ($user_id) ? $user_id : Auth::id())->exists();
    }

    //Список пользователь, которые поставили лайк/дизлайк
    public function assessmentedBy(string $type = 'like')
    {
        return $this->assessments($type)->with('user')->get()->mapWithKeys(function ($item) {
            return [$item['user']->id => $item['user']];
        });
    }

    //Количество лайков/дизлайков
    public function assessmentsCount(string $type = 'like')
    {
        return $this->assessments($type)->count();
    }

    //Количество лайков
    public function likes()
    {
        return $this->assessmentsCount();
    }

    //Количество дизлайков
    public function dislikes()
    {
        return $this->assessmentsCount('dislike');
    }
}