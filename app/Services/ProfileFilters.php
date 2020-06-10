<?php


namespace App\Services;


use App\Scopes\ProfilesBlockScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileFilters extends Filters
{
    protected $guardedFilters = [
        'block',
        'user',
        'telephone',
        'city_id'
    ];

    protected function user($value, $profiles)
    {
        if ($value) {
            $profiles->whereHas('user', function ($query) use ($value) {
                return $query->where('name', 'LIKE', '%' . $value . '%');
            });
        }
    }

    protected function telephone($value, $profiles)
    {
        if ($value) {
            $profiles->whereHas('telephone', function ($query) use ($value) {
                return $query->where('telephone', 'LIKE', '%' . $value . '%');
            });
        }
    }

    protected function city_id($value, $profiles)
    {
        if ($value) {
            $profiles->whereHas('user', function ($query) use ($value) {
                return $query->where('city_id', 'LIKE', '%' . $value . '%');
            });
        }
    }

    protected function block($value, $profiles)
    {
        if ($value == 1) {
            $profiles->blocked();
        }

        if ($value == 2) {
            $profiles->withGlobalScope('ProfilesBlockScope', new ProfilesBlockScope);
        }
    }

    protected function name_telephone($value, $profiles)
    {
        if ($value) {
            $profiles->whereHas('publishes', function ($query) {
                return $query->where('telephone_search', 1);
            })->whereHas('telephone', function ($query) use ($value) {
                return $query->where('telephone', 'LIKE', '%' . $value . '%');
            })->orWhere('name', 'LIKE', '%' . $value . '%');
        }
    }

    protected function user_role($value, $profiles)
    {
        if ($value) {
            $profiles->whereHas('user.roles', function ($query) use ($value) {
                return $query->where('slug', $value);
            });
        }
    }

    protected function active($value, $profiles)
    {
        if ($value !== null) {
            $profiles->where('active', $value);
        }
    }

    protected function unconfirmed_photo($value, $profiles)
    {
        if ($value !== null) {
            $profiles->whereHas('images', function ($query) use ($value) {
                return $query->where('active', $value);
            });
        }
    }

    protected function name($value, $profiles)
    {
        if ($value) {
            $profiles->where('name', 'LIKE', '%' . $value . '%');
        }
    }

    protected function metro_id($value, $profiles)
    {
        if ($value) {
            $profiles->whereHas('metros', function ($query) use ($value) {
                return $query->where('metros.id', $value);
            });
        }
    }

    protected function service_id($value, $profiles)
    {
        if ($value) {
            $profiles->whereHas('services', function ($query) use ($value) {
                return $query->where('services.id', $value);
            });
        }
    }

    protected function service_ids($value, $profile)
    {
        if ($value && is_array($value)) {
            $profile->whereHas('services', function ($query) use ($value) {
                return $query->whereIn('services.id', $value);
            });
        }
    }

    protected function place($value, $profiles)
    {
        if ($value) {
            $profiles->whereHas('prices', function ($query) use ($value) {
                return $query->where('type', $value);
            });
        }
    }

    protected function has_telephone($value, $profiles)
    {
        if ($value) {
            $profiles->whereHas('telephone', function ($query) {
                $query->whereNotNull('telephone_id');
            });
        }
    }

    protected function has_images($value, $profiles)
    {
        if ($value) {
            $profiles->whereHas('images', function ($query) {
                $query->where('active', 1);
            });
        }
    }

    protected function has_videos($value, $profiles)
    {
        if ($value) {
            $profiles->whereHas('videos', function ($query) {
                $query->where('active', 1);
            });
        }
    }

    protected function has_reviews($value, $profiles)
    {
        if ($value) {
            $profiles->whereHas('reviews', function ($query) {
                $query->where('active', 1);
            });
        }
    }

    protected function newest($value, $profiles)
    {
        if ($value) {
            $profiles->newest();
        }
    }

    protected function age_from($value, $profiles)
    {
        if ($value) {
            $profiles->where('birth', '<=', date('Y') - $value);
        }
    }

    protected function age_to($value, $profiles)
    {
        if ($value) {
            $profiles->where('birth', '>=', date('Y') - $value);
        }
    }

    protected function price_from($value, $profiles)
    {
        if ($value) {
            $profiles->whereHas('prices', function ($query) use ($value) {
                $query->where('price', '>=', $value);
            });
        }
    }

    protected function price_to($value, $profiles)
    {
        if ($value) {
            $profiles->whereHas('prices', function ($query) use ($value) {
                $query->where('price', '<=', $value);
            });
        }
    }
    
    protected function unactive($value, $profiles)
    {
        if ($value) {
            $profiles->where('active', 0);
        }
    }
    
    
}
