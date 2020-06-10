<?php
namespace App\Facades;

use App\Profile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;

/**
 * @method static createExtraData(Request $request, Model $profile)
 * @method static getShowVariables(Model $profile)
 * @method static getPreviewVariables(Model $profile)
 * @method static getFormVariables(Request $request = null, Profile $profile = null)
 * @method static applyFilters(Request $request, Builder $profiles)
 * @method static getFiltersVariables(Request $request)
 * @method static reviews(Profile $profile, bool $withReviews = true, bool $withComments = true)
 * @method static getCostOfProfiles(int $profilesCount)
 * @method static getNewestProfiles()
 * @method static getCountOfProfiles(string $userRole = 'individual')
 */
class ProfileService extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'profile_service';
    }
}