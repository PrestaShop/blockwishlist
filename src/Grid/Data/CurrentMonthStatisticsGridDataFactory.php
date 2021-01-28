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
        $shop_id = (int) \Context::getContext()->shop->id;
        if ($this->cache->contains(self::CACHE_KEY_STATS_CURRENT_MONTH . $shop_id)) {
            $results = $this->cache->fetch(self::CACHE_KEY_STATS_CURRENT_MONTH . $shop_id);
        } else {
            $results = $this->calculator->computeStatsFor('currentMonth');
            $this->cache->save(self::CACHE_KEY_STATS_CURRENT_MONTH . $shop_id, $results, self::CACHE_LIFETIME_SECONDS);
        }

        return new GridData(new RecordCollection($results), count($results));
    }
}
