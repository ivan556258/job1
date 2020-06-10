<?php


namespace App\SiteMapGenerator;


use Psr\Http\Message\UriInterface;
use Spatie\Crawler\CrawlProfile;

class SiteMapGeneratorProfile extends CrawlProfile
{

    /**
     * Determine if the given url should be crawled.
     *
     * @param \Psr\Http\Message\UriInterface $url
     *
     * @return bool
     */
    public function shouldCrawl(UriInterface $url): bool
    {
        return $url->getPath() !== '' && $url->getHost() === config('app.domain') && $url->getQuery() === '' && $url->getFragment() === '';
    }
}