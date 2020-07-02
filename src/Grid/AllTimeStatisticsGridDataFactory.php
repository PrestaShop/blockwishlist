<?php

namespace PrestaShop\Module\BlockWishList\Grid;

use Doctrine\Common\Cache\CacheProvider;
use PrestaShop\Module\BlockWishList\Calculator\StatisticsCalculator;
use PrestaShop\PrestaShop\Core\Grid\Data\Factory\GridDataFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;

class AllTimeStatisticsGridDataFactory implements GridDataFactoryInterface
{
    const CACHE_LIFETIME_SECONDS = 86400;

    /* @var CacheProvider $cache */
    private $cache;

    /* @var LegacyContext $context */
    private $context;

    public function __construct(CacheProvider $cache, $context)
    {
        $this->cache = $cache;
        $this->context = $context;
    }

    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        // if ($this->cache->contains('blockwishlist.stats.allTime')) {
        //     $results = $this->cache->fetch('blockwishlist.stats.allTime');
        // } else {
            $results = (new StatisticsCalculator($this->context))->computeStatsFor('allTime');
            // $this->cache->save('blockwishlist.stats.allTime', $results, self::CACHE_LIFETIME_SECONDS);
            // $this->cache->save('blockwishlist.stats.currentYear', $results['currentYear'], self::CACHE_LIFETIME_SECONDS);
            // $this->cache->save('blockwishlist.stats.currentMonth', $results['currentMonth'], self::CACHE_LIFETIME_SECONDS);
            // $this->cache->save('blockwishlist.stats.currentDay', $results['currentDay'], self::CACHE_LIFETIME_SECONDS);
        // }

        return new GridData(new RecordCollection($results), count($results));
    }
}
