<?php

namespace PrestaShop\Module\BlockWishList\Grid;

use Doctrine\Common\Cache\CacheProvider;
use PrestaShop\Module\BlockWishList\Calculator\StatisticsCalculator;
use PrestaShop\PrestaShop\Core\Grid\Data\Factory\GridDataFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;

class CurrentYearStatisticsGridDataFactory implements GridDataFactoryInterface
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
        // if ($this->cache->contains('blockwishlist.stats.currentYear')) {
        //     $results = $this->cache->fetch('blockwishlist.stats.currentYear');
        // } else {
            $results = (new StatisticsCalculator($this->context))->computeStatsFor('currentYear');
            $this->cache->save('blockwishlist.stats.currentYear', $results, self::CACHE_LIFETIME_SECONDS);
        // }

        return new GridData(new RecordCollection($results), count($results));
    }
}
