<?php

namespace PrestaShop\Module\BlockWishList\Grid;

use Doctrine\Common\Cache\CacheProvider;
use PrestaShop\Module\BlockWishList\Calculator\StatisticsCalculator;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Grid\Data\Factory\GridDataFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;

class StatisticsGridDataFactory implements GridDataFactoryInterface
{
    const CACHE_LIFETIME_SECONDS = 86400;
    const YEAR_CACHE_LIFETIME_SECONDS = 86400;
    const MONTH_CACHE_LIFETIME_SECONDS = 86400;
    const DAY_CACHE_LIFETIME_SECONDS = 86400;

    /* @var CacheProvider $cache */
    private $cache;

    /* @var LegacyContext $cache */
    private $context;

    public function __construct(CacheProvider $cache, LegacyContext $context)
    {
        $this->cache = $cache;
        $this->context = $context->getContext();
    }

    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        // if ($this->cache->contains('blockwishlist.stats.allTime')) {
        //     $results = [
        //         'allTime' => $this->cache->fetch('blockwishlist.stats.allTime'),
        //         'currentYear' => $this->cache->fetch('blockwishlist.stats.currentYear'),
        //         'currentMonth' => $this->cache->fetch('blockwishlist.stats.currentMonth'),
        //         'currentDay' => $this->cache->fetch('blockwishlist.stats.currentDay'),
        //     ];
        // } else {
            $results = (new StatisticsCalculator($this->context))->computeAllStats();
            // $this->cache->save('blockwishlist.stats.allTime', $results['allTime'], self::CACHE_LIFETIME_SECONDS);
            // $this->cache->save('blockwishlist.stats.currentYear', $results['currentYear'], self::CACHE_LIFETIME_SECONDS);
            // $this->cache->save('blockwishlist.stats.currentMonth', $results['currentMonth'], self::CACHE_LIFETIME_SECONDS);
            // $this->cache->save('blockwishlist.stats.currentDay', $results['currentDay'], self::CACHE_LIFETIME_SECONDS);
        // }

        return new GridData(
            new RecordCollection($results['allTime']),
            count($results['allTime']),
            'cached datas'
        );
    }
}
