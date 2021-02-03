<?php

namespace PrestaShop\Module\BlockWishList\Grid\Data;

use PrestaShop\PrestaShop\Core\Grid\Data\Factory\GridDataFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

class CurrentYearStatisticsGridDataFactory extends BaseGridDataFactory implements GridDataFactoryInterface
{
    // 1 month
    const CACHE_LIFETIME_SECONDS = 2629746;

    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        if ($this->cache->contains(self::CACHE_KEY_STATS_CURRENT_YEAR . $this->shopId)) {
            $results = $this->cache->fetch(self::CACHE_KEY_STATS_CURRENT_YEAR . $this->shopId);
        } else {
            $results = $this->calculator->computeStatsFor('currentYear');
            $this->cache->save(self::CACHE_KEY_STATS_CURRENT_YEAR . $this->shopId, $results, self::CACHE_LIFETIME_SECONDS);
        }

        return new GridData(new RecordCollection($results), count($results));
    }
}
