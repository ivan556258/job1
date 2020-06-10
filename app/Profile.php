<?php

namespace App;

use App\Scopes\ProfileOrderScope;
use App\Traits\ProfileImageTrait;
use App\Traits\ProfileTrait;
use App\Traits\StatusesTrait;
use Grechanyuk\Favorite\Traits\FavoriteableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class Profile extends Model
{
    use StatusesTrait;
    use FavoriteableTrait;
    use ProfileTrait;

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new ProfileOrderScope());
    }

    protected $fillable = [
        'name',
        'birth',
        'active',
        'address',
        'description',
        'sex',
        'experience',
        'education_id',
        'area_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'block',
        'map_coordinates',
        'verification',
        'user_id',
        'area',
        'verification_paid',
        'verification_at',
        'telephone_id'
    ];

    protected $casts = [
        'verification_at' => 'datetime',
        'activated_at' => 'date'
    ];

    public function setBlockAttribute($val)
    {
        if (Auth::user()->level() > 3) {
            $this->attributes['block'] = $val;
        }
    }

    public function setVerificationAttribute($val)
    {
        if (Auth::user()->level() > 3) {
            $this->attributes['verification'] = $val;
        }
    }

    public function image()
    {
        return $this->hasOne(ProfileImage::class)->where('main', 1);
    }

    public function images()
    {
        return $this->hasMany(ProfileImage::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function work_schedules()
    {
        return $this->hasMany(WorkSchedule::class);
    }

    public function prices()
    {
        return $this->hasMany(ProfilePrice::class);
    }

    public function pricesIn()
    {
        return $this->prices()->whereType('in');
    }

    public function pricesOut()
    {
        return $this->prices()->whereType('out');
    }

    public function education()
    {
        return $this->belongsTo(Education::class);
    }

    public function scopeBlocked($query)
    {
        $query->where('block', 1);
    }

    public function scopeOnlyNew($query)
    {
        $date = Carbon::now()->addDays($this->getTime());

        return $query->where('profiles.created_at', '>', $date);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function scopeSearch($query, string $profile, string $column = 'name')
    {
        $query->where($column, 'LIKE', '%' . $profile . '%');
    }

    public function individual_service()
    {
        return $this->hasOne(ProfileIndividualService::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class)->withPivot('price');
    }

    public function conveniences()
    {
        return $this->belongsToMany(Convenience::class);
    }

    public function clients()
    {
        return $this->hasOne(ProfileClient::class);
    }

    public function metros()
    {
        return $this->belongsToMany(Metro::class);
    }

    public function publishes()
    {
        return $this->hasOne(ProfilePublish::class);
    }

    public function statistic()
    {
        return $this->hasMany(ProfileStatistic::class);
    }

    public function expenses()
    {
        return $this->transactions()->whereType('expense');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function advertising()
    {
        return $this->hasMany(ProfileAdvertising::class);
    }

    public function advertisingBanners()
    {
        return $this->morphMany(AdvertisingBanner::class, 'bannerable');
    }

    public function advertisingSchedule()
    {
        return $this->hasMany(ProfileAdvertisingSchedule::class);
    }

    public function videos()
    {
        return $this->hasMany(ProfileVideo::class);
    }

    public function verification_images()
    {
        return $this->hasMany(ProfileVerificationImage::class);
    }

    public function scopeNewest($query)
    {
        $query->where('created_at', '>=', Carbon::now()->addDays($this->getTime()));
    }

    public function scopePayVerification($query, int $days, string $operator = '<=')
    {
        $query->where('paid', 1)->where('verification_at', $operator, \Carbon\Carbon::now()->addDays($days));
    }

    public function comments()
    {
        return $this->hasMany(ProfileComment::class);
    }

    public function telephone()
    {
        return $this->belongsTo(UserTelephone::class);
    }
}
