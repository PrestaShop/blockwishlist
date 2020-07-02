<?php

namespace PrestaShop\Module\BlockWishList\Grid;

use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;

final class StatisticsQueryBuilder extends AbstractDoctrineQueryBuilder
{
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria){}
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria){}
}
