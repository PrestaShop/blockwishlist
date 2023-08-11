<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
class Statistics extends ObjectModel
{
    /** @var int ID */
    public $id_statistics;

    /** @var int id_product */
    public $id_product;

    /** @var int id_product_attribute */
    public $id_product_attribute;

    /** @var string date_add */
    public $date_add;

    /** @var int|null date_add */
    public $id_cart;

    /** @var int ID */
    public $id_shop;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'blockwishlist_statistics',
        'primary' => 'id_statistics',
        'fields' => [
            'id_cart' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => false],
            'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_product_attribute' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'date_add' => ['type' => self::TYPE_DATE, 'required' => true],
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
        ],
    ];

    /**
     * @param int|null $id_product
     * @param int|null $id_product_attribute
     *
     * @return bool
     */
    public static function removeProductFromStatistics($id_product = null, $id_product_attribute = null)
    {
        if ($id_product === null && $id_product_attribute === null) {
            return false;
        }

        return Db::getInstance()->delete(
            'blockwishlist_statistics',
            ($id_product ? 'id_product = ' . (int) $id_product : '')
            . ($id_product && $id_product_attribute ? ' AND ' : '')
            . ($id_product_attribute ? ' id_product_attribute = ' . (int) $id_product_attribute : '')
        );
    }

    /**
     * @return void
     */
    public static function removeNonExistingProductAttributesFromStatistics()
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('bws.id_product_attribute');
        $dbQuery->from('blockwishlist_statistics', 'bws');
        $dbQuery->leftJoin('product_attribute', 'pa', 'bws.id_product_attribute = pa.id_product_attribute');
        $dbQuery->where('pa.id_product_attribute IS NULL');
        $productAttributes = Db::getInstance()->executeS($dbQuery);

        foreach ($productAttributes as $productAttribute) {
            self::removeProductFromStatistics(null, (int) $productAttribute['id_product_attribute']);
        }
    }
}
