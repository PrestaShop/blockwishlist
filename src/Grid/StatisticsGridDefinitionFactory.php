<?php
/**
 * 2007-2020 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\Module\BlockWishList\Grid;

use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractGridDefinitionFactory;

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
                ->setName($this->trans('ID', [], 'prestashop.module.blockwishlist.statistics.ID'))
                ->setOptions([
                    'field' => 'id_product',
                ])
            )
            ->add((new DataColumn('name'))
                ->setName($this->trans('name', [], 'prestashop.module.blockwishlist.statistics.name'))
                ->setOptions([
                    'field' => 'name',
                ])
            )
            ->add((new DataColumn('reference'))
                ->setName($this->trans('reference', [], 'prestashop.module.blockwishlist.statistics.reference'))
                ->setOptions([
                    'field' => 'reference',
                ])
            )

            ->add((new DataColumn('category_name'))
                ->setName($this->trans('category_name', [], 'prestashop.module.blockwishlist.statistics.category_name'))
                ->setOptions([
                    'field' => 'category_name',
                ])
            )
            ->add((new DataColumn('price'))
                ->setName($this->trans('price', [], 'prestashop.module.blockwishlist.statistics.price'))
                ->setOptions([
                    'field' => 'price',
                ])
            )
            ->add((new DataColumn('quantity'))
                ->setName($this->trans('quantity', [], 'prestashop.module.blockwishlist.statistics.quantity'))
                ->setOptions([
                    'field' => 'quantity',
                ])
            )
            ->add((new DataColumn('conversionRate'))
                ->setName($this->trans('conversionRate', [], 'prestashop.module.blockwishlist.statistics.conversionRate'))
                ->setOptions([
                    'field' => 'conversionRate',
                ])
            )
        ;
    }
}
