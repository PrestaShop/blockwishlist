<?php

namespace PrestaShop\Module\BlockWishList\Grid\Data;

use PrestaShop\PrestaShop\Core\Grid\Data\Factory\GridDataFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

class CurrentMonthStatisticsGridDataFactory extends BaseGridDataFactory implements GridDataFactoryInterface
{
    // 1 week
    const CACHE_LIFETIME_SECONDS = 604800;

    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        if ($this->cache->contains('blockwishlist.stats.currentMonth')) {
            $results = $this->cache->fetch('blockwishlist.stats.currentMonth');
        } else {
            $results = $this->calculator->computeStatsFor('currentMonth');
            $this->cache->save('blockwishlist.stats.currentMonth', $results, self::CACHE_LIFETIME_SECONDS);
        }

        return new GridData(new RecordCollection($results), count($results));
    }
}
