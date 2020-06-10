<?php
namespace App\Observers;

use App\Service;
use App\Traits\TranslationTrait;
use App\UpdateSiteMap;

class ServiceObserver extends Observer
{
    use TranslationTrait;

    public function created(Service $service)
    {
        $this->clearCache('services');
        $this->updateSiteMaps();
    }

    public function updated(Service $service)
    {
        $this->clearCache('services');
    }

    public function deleted(Service $service)
    {
        $this->getTranslationModel('service', null, $service->id)->delete();
        $this->clearCache('services');
        $this->updateSiteMaps();
    }

    private function updateSiteMaps()
    {
        UpdateSiteMap::truncate();
        UpdateSiteMap::create();
    }
}