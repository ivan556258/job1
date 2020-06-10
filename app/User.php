<?php

namespace App;

use App\Notifications\VerifyEmail;
use App\Scopes\UserOrderScope;
use App\Traits\BalanceTrait;
use App\Traits\SearchableTrait;
use App\Traits\TelephoneTrait;
use Grechanyuk\Favorite\Traits\FavoritabilityTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use jeremykenedy\LaravelRoles\Traits\HasRoleAndPermission;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;
    use HasRoleAndPermission;
    use FavoritabilityTrait;
    use BalanceTrait;
    use TelephoneTrait;
    use SearchableTrait;

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new UserOrderScope());
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'city_id',
        'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    private $role;

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail());
    }

    public function setPasswordAttribute($val)
    {
        if(!empty($val)) {
            $this->attributes['password'] = bcrypt($val);
        }
    }

    public function setTelephoneAttribute($val)
    {
        $this->attributes['telephone'] = telephoneFormat($val);
    }

    public function profiles()
    {
        return $this->hasMany(Profile::class);
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class)->orderBy('created_at', 'desc');
    }

    public function balanceTransactions()
    {
        return $this->transactions()->where(function ($query) {
            $query->where('status', 3)->orWhere('type', 'expense');
        });
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get all roles as collection.
     *
     * @return Collection
     */
    public function getRole()
    {
        return (!$this->role) ? $this->role = $this->roles()->first() : $this->role;
    }

    public function notificationSettings()
    {
        return $this->hasMany(UserNotification::class);
    }

    public function advertising()
    {
        return $this->hasMany(AdvertisingBanner::class);
    }

    public function salon()
    {
        return $this->hasOne(Salon::class);
    }

    public function prices()
    {
        return $this->hasManyThrough(ProfilePrice::class, Profile::class);
    }

    public function profile_comments()
    {
        return $this->hasMany(ProfileComment::class);
    }

    public function devicesInformation()
    {
        return $this->hasMany(UserDeviceInformation::class);
    }

    public function telephones()
    {
        return $this->hasMany(UserTelephone::class);
    }
    
    public function getIsSalonAttribute()
    {
        return $this->getRoles()->pluck('id')->contains(3);
    }
    
    public function getIsIndividualAttribute()
    {
        return $this->getRoles()->pluck('id')->contains(2);
    }
}
