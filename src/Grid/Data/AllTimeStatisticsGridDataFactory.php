<?php

namespace PrestaShop\Module\BlockWishList\Grid\Data;

use PrestaShop\PrestaShop\Core\Grid\Data\Factory\GridDataFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

class AllTimeStatisticsGridDataFactory extends BaseGridDataFactory implements GridDataFactoryInterface
{
    // 1 month
    const CACHE_LIFETIME_SECONDS = 2629746;

    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        if ($this->cache->contains(self::CACHE_KEY_STATS_ALL_TIME . $this->shopId)) {
            $results = $this->cache->fetch(self::CACHE_KEY_STATS_ALL_TIME . $this->shopId);
        } else {
            $results = $this->calculator->computeStatsFor('allTime');
            $this->cache->save(self::CACHE_KEY_STATS_ALL_TIME . $this->shopId, $results, self::CACHE_LIFETIME_SECONDS);
        }

        return new GridData(new RecordCollection($results), count($results));
    }
}
