<?php

namespace PrestaShop\Module\BlockWishList\Grid\Data;

use Doctrine\Common\Cache\CacheProvider;
use PrestaShop\Module\BlockWishList\Calculator\StatisticsCalculator;

class BaseGridDataFactory
{
    const CACHE_KEY_STATS_CURRENT_DAY = 'blockwishlist.stats.currentDay';
    const CACHE_KEY_STATS_CURRENT_MONTH = 'blockwishlist.stats.currentMonth';
    const CACHE_KEY_STATS_CURRENT_YEAR = 'blockwishlist.stats.currentYear';
    const CACHE_KEY_STATS_ALL_TIME = 'blockwishlist.stats.allTime';

    /* @var CacheProvider $cache */
    protected $cache;

    /* @var StatisticsCalculator $calculator */
    protected $calculator;

    /**
     * @var int|null
     */
    protected $shopId;

    public function __construct(CacheProvider $cache, StatisticsCalculator $calculator, $shopId)
    {
        $this->cache = $cache;
        $this->calculator = $calculator;
        $this->shopId = $shopId;
    }
}
