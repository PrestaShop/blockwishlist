<?php

namespace PrestaShop\Module\BlockWishList\Grid\Data;

use PrestaShop\PrestaShop\Core\Grid\Data\Factory\GridDataFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;

class AllTimeStatisticsGridDataFactory extends BaseGridDataFactory implements GridDataFactoryInterface
{
    // 1 month
    const CACHE_LIFETIME_SECONDS = 2629746;

    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        if ($this->cache->contains('blockwishlist.stats.allTime')) {
            $results = $this->cache->fetch('blockwishlist.stats.allTime');
        } else {
            $results = $this->calculator->computeStatsFor('allTime');
            $this->cache->save('blockwishlist.stats.allTime', $results, self::CACHE_LIFETIME_SECONDS);
        }

        return new GridData(new RecordCollection($results), count($results));
    }
}
