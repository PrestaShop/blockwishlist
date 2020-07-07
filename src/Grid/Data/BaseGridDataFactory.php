<?php

namespace PrestaShop\Module\BlockWishList\Grid\Data;

use Doctrine\Common\Cache\CacheProvider;
use PrestaShop\Module\BlockWishList\Calculator\StatisticsCalculator;

class BaseGridDataFactory
{
    /* @var CacheProvider $cache */
    protected $cache;

    /* @var StatisticsCalculator $calculator */
    protected $calculator;

    public function __construct(CacheProvider $cache, StatisticsCalculator $calculator)
    {
        $this->cache = $cache;
        $this->calculator = $calculator;
    }
}
