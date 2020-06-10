<?php
namespace App\Events;

use Illuminate\Queue\SerializesModels;

class MonthlyCostWasChanged
{
    use SerializesModels;

    private $newCost;
    private $userRoleSlug;

    public function __construct(float $newCost, string $userRoleSlug)
    {
        $this->userRoleSlug = $userRoleSlug;
        $this->newCost = $newCost;
    }

    /**
     * @return string
     */
    public function getUserRoleSlug(): string
    {
        return $this->userRoleSlug;
    }

    /**
     * @return float
     */
    public function getNewCost(): float
    {
        return $this->newCost;
    }
}