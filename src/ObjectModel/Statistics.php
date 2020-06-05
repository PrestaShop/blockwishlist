<?php

namespace PrestaShop\Module\BlockWishList\ObjectModel;

class Statistics extends \ObjectModel
{
    /** @var int ID */
    public $id_statistics;

    /** @var int id_product */
    public $id_product;

    /** @var int id_product_attribute */
    public $id_product_attribute;

    /** @var String date_add */
    public $date_add;

    /** @var null|int date_add */
    public $id_cart;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'blockwishlist_statistics',
        'primary' => 'id_statistics',
        'fields' => [
            'id_cart' => ['type' => self::TYPE_INT, 'required' => false],
            'id_product' => ['type' => self::TYPE_INT, 'required' => true],
            'id_product_attribute' => ['type' => self::TYPE_INT, 'required' => true],
            'date_add' => ['type' => self::TYPE_DATE, 'required' => true],
        ],
    ];
}
