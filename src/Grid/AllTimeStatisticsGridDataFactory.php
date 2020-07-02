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

    /* @var StatisticsCalculator $calculator */
    private $calculator;

    public function __construct(CacheProvider $cache, StatisticsCalculator $calculator)
    {
        $this->cache = $cache;
        $this->calculator = $calculator;
    }

    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        // if ($this->cache->contains('blockwishlist.stats.allTime')) {
        //     $results = $this->cache->fetch('blockwishlist.stats.allTime');
        // } else {
            $results = $this->calculator->computeStatsFor('allTime');
            $this->cache->save('blockwishlist.stats.allTime', $results, self::CACHE_LIFETIME_SECONDS);
        // }

        return new GridData(new RecordCollection($results), count($results));
    }
}
