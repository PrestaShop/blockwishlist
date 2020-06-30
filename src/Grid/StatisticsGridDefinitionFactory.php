<?php

namespace PrestaShop\Module\BlockWishList\Grid;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;

final class StatisticsGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    protected function getId()
    {
        return 'statistics';
    }

    protected function getName()
    {
        return 'statistics';
        // return $this->trans('Products', [], 'Admin.Advparameters.Feature');
    }

    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add((new DataColumn('id_product'))
                ->setName($this->trans('ID', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'id_product',
                ])
            )
            ->add((new DataColumn('reference'))
                ->setName($this->trans('Reference', [], 'Admin.Advparameters.Feature'))
                ->setOptions([
                    'field' => 'reference',
                ])
            )
            ->add((new DataColumn('name'))
                ->setName($this->trans('Name', [], 'Admin.Advparameters.Feature'))
                ->setOptions([
                    'field' => 'name',
                ])
            )
        ;
    }
}
