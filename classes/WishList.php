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

namespace PrestaShop\Module\BlockWishlist;

class WishList extends \ObjectModel
{
    /** @var int Wishlist ID */
    public $id;

    /** @var int Customer ID */
    public $id_customer;

    /** @var int Token */
    public $token;

    /** @var int Name */
    public $name;

    /** @var string Object creation date */
    public $date_add;

    /** @var string Object last modification date */
    public $date_upd;

    /** @var string Object last modification date */
    public $id_shop;

    /** @var string Object last modification date */
    public $id_shop_group;

    /** @var int default */
    public $default;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'wishlist',
        'primary' => 'id_wishlist',
        'fields' => [
            'id_customer' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'token' => ['type' => self::TYPE_STRING, 'validate' => 'isMessage', 'required' => true],
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isMessage', 'required' => true],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_shop_group' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'default' => ['type' => self::TYPE_BOOL, 'validate' => 'isUnsignedId'],
        ],
    ];

    /**
     * Get Customers having a wishlist
     *
     * @return array Results
     */
    public static function getCustomers()
    {
        $cache_id = 'WhishList::getCustomers';

        if (!\Cache::isStored($cache_id)) {
            $result = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                SELECT c.`id_customer`, c.`firstname`, c.`lastname`
                    FROM `' . _DB_PREFIX_ . 'wishlist` w
                INNER JOIN `' . _DB_PREFIX_ . 'customer` c ON c.`id_customer` = w.`id_customer`
                ORDER BY c.`firstname` ASC'
            );

            \Cache::store($cache_id, $result);
        }

        return \Cache::retrieve($cache_id);
    }

    /**
     * Return true if wishlist exists else false
     *
     *  @return bool exists
     */
    public static function exists($id_wishlist, $id_customer)
    {
        if (!\Validate::isUnsignedId($id_wishlist) || !\Validate::isUnsignedId($id_customer)) {
            return false;
        }

        $result = \Db::getInstance()->getRow('
            SELECT `id_wishlist`, `name`, `token`
                FROM `' . _DB_PREFIX_ . 'wishlist`
            WHERE `id_wishlist` = ' . (int) ($id_wishlist) . '
            AND `id_customer` = ' . (int) ($id_customer) . '
            AND `id_shop` = ' . (int) \Context::getContext()->shop->id
        );

        return (bool) $result;
    }

    /**
     * Set current WishList as default
     *
     * @return bool
     */
    public function setDefault()
    {
        if ($default = $this->getDefault($this->id_customer)) {
            \Db::getInstance()->update('wishlist', ['default' => '0'], 'id_wishlist = ' . $default[0]['id_wishlist']);
        }

        return \Db::getInstance()->update('wishlist', ['default' => '1'], 'id_wishlist = ' . $this->id);
    }

    /**
     * Return if there is a default already set
     *
     * @return bool
     */
    public static function isDefault($id_customer)
    {
        return (bool) \Db::getInstance()->getValue('SELECT * FROM `' . _DB_PREFIX_ . 'wishlist` WHERE `id_customer` = ' . $id_customer . ' AND `default` = 1');
    }

    public static function getDefault($id_customer)
    {
        return \Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'wishlist` WHERE `id_customer` = ' . $id_customer . ' AND `default` = 1');
    }

    /**
     * Add product to ID wishlist
     *
     * @return bool succeed
     */
    public static function addProduct($id_wishlist, $id_customer, $id_product, $id_product_attribute, $quantity)
    {
        if (!\Validate::isUnsignedId($id_wishlist) ||
            !\Validate::isUnsignedId($id_customer) ||
            !\Validate::isUnsignedId($id_product) ||
            !\Validate::isUnsignedId($quantity)
        ) {
            die(\Tools::displayError());
        }

        $result = \Db::getInstance()->getRow('
            SELECT wp.`quantity`
                FROM `' . _DB_PREFIX_ . 'wishlist_product` wp
            JOIN `' . _DB_PREFIX_ . 'wishlist` w ON (w.`id_wishlist` = wp.`id_wishlist`)
            WHERE wp.`id_wishlist` = ' . (int) ($id_wishlist) . '
            AND w.`id_customer` = ' . (int) ($id_customer) . '
            AND wp.`id_product` = ' . (int) ($id_product) . '
            AND wp.`id_product_attribute` = ' . (int) ($id_product_attribute)
        );

        if (empty($result) === false && sizeof($result)) {
            if (($result['quantity'] + $quantity) <= 0) {
                return WishList::removeProduct($id_wishlist, $id_customer, $id_product, $id_product_attribute);
            } else {
                // TODO: use a method for this like updateProduct ?
                return \Db::getInstance()->execute('
                    UPDATE `' . _DB_PREFIX_ . 'wishlist_product` SET
                    `quantity` = ' . (int) ($quantity + $result['quantity']) . '
                    WHERE `id_wishlist` = ' . (int) ($id_wishlist) . '
                    AND `id_product` = ' . (int) ($id_product) . '
                    AND `id_product_attribute` = ' . (int) ($id_product_attribute)
                );
            }
        } else {
            return \Db::getInstance()->execute('
                INSERT INTO `' . _DB_PREFIX_ . 'wishlist_product` (`id_wishlist`, `id_product`, `id_product_attribute`, `quantity`, `priority`) VALUES (
                ' . (int) ($id_wishlist) . ',
                ' . (int) ($id_product) . ',
                ' . (int) ($id_product_attribute) . ',
                ' . (int) ($quantity) . ', 1)'
            );
        }
    }

    /**
     * Remove product from wishlist
     *
     * @return boolean
     */
    public static function removeProduct($id_wishlist, $id_customer, $id_product, $id_product_attribute)
    {
        if (!\Validate::isUnsignedId($id_wishlist) ||
            !\Validate::isUnsignedId($id_customer) ||
            !\Validate::isUnsignedId($id_product)
        ) {
            return false;
        }

        $result = \Db::getInstance()->getRow('
            SELECT w.`id_wishlist`, wp.`id_wishlist_product`
            FROM `'._DB_PREFIX_.'wishlist` w
            LEFT JOIN `'._DB_PREFIX_.'wishlist_product` wp ON (wp.`id_wishlist` = w.`id_wishlist`)
            WHERE `id_customer` = '.(int) $id_customer.'
            AND w.`id_wishlist` = '.(int) $id_wishlist
        );

        if (empty($result) === true ||
            $result === false ||
            !sizeof($result) ||
            $result['id_wishlist'] != $id_wishlist
        ) {
            return false;
        }

        // Delete product in wishlist_product_cart
        \Db::getInstance()->execute('
            DELETE FROM `'._DB_PREFIX_.'wishlist_product_cart`
            WHERE `id_wishlist_product` = '.(int) $result['id_wishlist_product']
        );

        return \Db::getInstance()->execute('
            DELETE FROM `'._DB_PREFIX_.'wishlist_product`
            WHERE `id_wishlist` = '.(int) $id_wishlist.'
            AND `id_product` = '.(int) $id_product.'
            AND `id_product_attribute` = '.(int) $id_product_attribute
        );
    }

    /**
     * Update product to wishlist
     *
     * @return boolean succeed
     */
    public static function updateProduct($id_wishlist, $id_product, $id_product_attribute, $priority, $quantity)
    {
        if (!\Validate::isUnsignedId($id_wishlist) ||
            !\Validate::isUnsignedId($id_product) ||
            !\Validate::isUnsignedId($quantity) ||
            $priority < 0 ||
            $priority > 2
        ) {
            return false;
        }

        return \Db::getInstance()->execute('
            UPDATE `'._DB_PREFIX_.'wishlist_product` SET
            `priority` = '.(int) $priority.',
            `quantity` = '.(int) $quantity.'
            WHERE `id_wishlist` = '.(int) $id_wishlist.'
            AND `id_product` = '.(int) $id_product.'
            AND `id_product_attribute` = '.(int) $id_product_attribute
        );
    }

    /**
     * Get all Wishlists by Customer ID
     *
     * @return array Results
     */
    public static function getAllWishlistsByIdCustomer($id_customer)
    {
        if (\Shop::getContextShopID()) {
            $shop_restriction = 'AND id_shop = '.(int)\Shop::getContextShopID();
        } elseif (\Shop::getContextShopGroupID()) {
            $shop_restriction = 'AND id_shop_group = '.(int)\Shop::getContextShopGroupID();
        } else {
            $shop_restriction = '';
        }

        if (!\Validate::isUnsignedId($id_customer)) {
            die (\Tools::displayError());
        }

        return (\Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT  w.`id_wishlist`, SUM(wp.`quantity`) AS nbProducts, w.`name`
            FROM `'._DB_PREFIX_.'wishlist_product` wp
            INNER JOIN `'._DB_PREFIX_.'wishlist` w ON (w.`id_wishlist` = wp.`id_wishlist`)
            WHERE w.`id_customer` = '.(int) $id_customer.'
            '.$shop_restriction.'
            GROUP BY w.`id_wishlist`
            ORDER BY w.`name` ASC')
        );
    }

    /**
     * Get products by Wishlist
     *
     * @return array|false Results
     */
    public static function getProductsByWishlist($id_wishlist)
    {
        if (!\Validate::isUnsignedId($id_customer) ||
            !\Validate::isUnsignedId($id_lang) ||
            !\Validate::isUnsignedId($id_wishlist)) {
                return false;
        }

        $wishlistProducts = \Db::getInstance()->executeS('
            SELECT `id_product`, `id_product_attribute`, `quantity`
            FROM `'._DB_PREFIX_.'wishlist_product`
            WHERE `id_wishlist` = ' . (int) $id_wishlist
        );

        if (!empty($wishlistProducts)) {
            return $wishlistProducts;
        }

        return false;
    }
}
