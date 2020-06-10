<?php
namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class ProfileAdvertisingWasUpdated
{
    use SerializesModels;

    private $newProfiles;
    private $oldProfiles;

    public function __construct(Collection $newProfiles, Collection $oldProfiles)
    {
        $this->newProfiles = $newProfiles;
        $this->oldProfiles = $oldProfiles;
    }

    /**
     * @return Collection
     */
    public function getNewProfiles(): Collection
    {
        return $this->newProfiles;
    }

    /**
     * @return Collection
     */
    public function getOldProfiles(): Collection
    {
        return $this->oldProfiles;
    }
}