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
            'token' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true],
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_shop_group' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'default' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
        ],
    ];

    /**
     * Get Customers having a wishlist
     *
     * @return array Results
     */
    public static function getCustomers()
    {
        $cache_id = 'WishList::getCustomers';

        if (false === \Cache::isStored($cache_id)) {
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
        if (false === \Validate::isUnsignedId($id_wishlist) || false === \Validate::isUnsignedId($id_customer)) {
            return false;
        }

        $result = \Db::getInstance()->getRow('
            SELECT `id_wishlist`, `name`, `token`
                FROM `' . _DB_PREFIX_ . 'wishlist`
            WHERE `id_wishlist` = ' . (int) $id_wishlist . '
            AND `id_customer` = ' . (int) $id_customer . '
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
            \Db::getInstance()->update('wishlist', ['default' => '0'], 'id_wishlist = ' .(int) $default);
        }

        return \Db::getInstance()->update('wishlist', ['default' => '1'], 'id_wishlist = ' .(int) $this->id);
    }

    /**
     * Return if there is a default already set
     *
     * @return bool
     */
    public static function isDefault($id_customer)
    {
        return (bool) \Db::getInstance()->getValue('SELECT 1 FROM `' . _DB_PREFIX_ . 'wishlist` WHERE `id_customer` = ' . (int) $id_customer . ' AND `default` = 1');
    }

    public static function getDefault($id_customer)
    {
        return (int) \Db::getInstance()->getValue('SELECT `id_wishlist` FROM `' . _DB_PREFIX_ . 'wishlist` WHERE `id_customer` = ' . (int) $id_customer . ' AND `default` = 1');
    }

    /**
     * Add product to ID wishlist
     *
     * @return bool succeed
     */
    public static function addProduct($id_wishlist, $id_customer, $id_product, $id_product_attribute, $quantity)
    {
        if (false === \Validate::isUnsignedId($id_wishlist) ||
            false === \Validate::isUnsignedId($id_customer) ||
            false === \Validate::isUnsignedId($id_product) ||
            false === \Validate::isUnsignedId($quantity)
        ) {
            \Tools::displayError();
        }

        $result = \Db::getInstance()->getRow('
            SELECT wp.`quantity`
                FROM `' . _DB_PREFIX_ . 'wishlist_product` wp
            JOIN `' . _DB_PREFIX_ . 'wishlist` w ON (w.`id_wishlist` = wp.`id_wishlist`)
            WHERE wp.`id_wishlist` = ' . (int) $id_wishlist . '
            AND w.`id_customer` = ' . (int) $id_customer . '
            AND wp.`id_product` = ' . (int) $id_product . '
            AND wp.`id_product_attribute` = ' . (int) ($id_product_attribute)
        );

        if (false === empty($result)) {
            if (($result['quantity'] + $quantity) <= 0) {
                return WishList::removeProduct($id_wishlist, $id_customer, $id_product, $id_product_attribute);
            } else {
                // TODO: use a method for this like updateProduct ?
                return \Db::getInstance()->update(
                    'wishlist_product',
                    [
                        'quantity' => (int) ($quantity + $result['quantity']),
                    ],
                    '`id_wishlist` = ' . (int) $id_wishlist . ' AND `id_product` = ' . (int) $id_product . ' AND `id_product_attribute` = ' . (int) $id_product_attribute
                );
            }
        } else {
            return \Db::getInstance()->insert(
                'wishlist_product',
                [
                    'id_wishlist' => (int) $id_wishlist,
                    'id_product' => (int) $id_product,
                    'id_product_attribute' => (int) $id_product_attribute,
                    'quantity' => (int) $id_product_attribute,
                    'priority' => 1
                ]
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
        if (false === \Validate::isUnsignedId($id_wishlist) ||
            false === \Validate::isUnsignedId($id_customer) ||
            false === \Validate::isUnsignedId($id_product)
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

        if (true === empty($result)) {
            return false;
        }

        // Delete product in wishlist_product_cart
        \Db::getInstance()->delete(
            'wishlist_product_cart',
            'id_wishlist_product = ' . (int) $result['id_wishlist_product']
        );

        return \Db::getInstance()->delete(
            'wishlist_product',
            'id_wishlist = '. (int) $id_wishlist . ' AND id_product = ' . (int) $id_product . ' AND id_product_attribute = '. (int) $id_product_attribute
        );
    }

    /**
     * Update product to wishlist
     *
     * @return boolean succeed
     */
    public static function updateProduct($id_wishlist, $id_product, $id_product_attribute, $priority, $quantity)
    {
        if (false === \Validate::isUnsignedId($id_wishlist) ||
            false === \Validate::isUnsignedId($id_product) ||
            false === \Validate::isUnsignedId($quantity) ||
            $priority < 0 ||
            $priority > 2
        ) {
            return false;
        }

        return \Db::getInstance()->update(
            'wishlist_product',
            [
                'priority' => (int) $priority,
                'quantity' => (int) $quantity,
            ],
            'id_wishlist = '. (int) $id_wishlist . 'id_product` = ' . (int) $id_product . 'id_product_attribute` = ' . (int) $id_product_attribute
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

        if (false === \Validate::isUnsignedId($id_customer)) {
            \Tools::displayError();
        }

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT  w.`id_wishlist`, SUM(wp.`quantity`) AS nbProducts, w.`name`
            FROM `'._DB_PREFIX_.'wishlist_product` wp
            RIGHT JOIN `'._DB_PREFIX_.'wishlist` w ON (w.`id_wishlist` = wp.`id_wishlist`)
            WHERE w.`id_customer` = '.(int) $id_customer.'
            '.$shop_restriction.'
            GROUP BY w.`id_wishlist`
            ORDER BY w.`name` ASC'
        );
    }

    /**
     * Get products by Wishlist
     *
     * @return array|false Results
     */
    public static function getProductsByWishlist($id_wishlist)
    {
        $wishlistProducts = \Db::getInstance()->executeS('
            SELECT `id_product`, `id_product_attribute`, `quantity`
            FROM `'._DB_PREFIX_.'wishlist_product`
            WHERE `id_wishlist` = ' . (int) $id_wishlist
        );

        if (false === empty($wishlistProducts)) {
            return $wishlistProducts;
        }

        return false;
    }

    /**
     * Get Wishlist products by Customer ID
     *
     * @return array Results
     */
    public static function getProductByIdCustomer($id_wishlist, $id_customer, $id_lang, $id_product = null, $quantity = false)
    {
        if (false === \Validate::isUnsignedId($id_customer) ||
            false === \Validate::isUnsignedId($id_lang) ||
            false === \Validate::isUnsignedId($id_wishlist)
        ){
            return false;
        }
        $products = \Db::getInstance()->executeS('
            SELECT wp.`id_product`, wp.`quantity`, p.`quantity` AS product_quantity, pl.`name`, wp.`id_product_attribute`, wp.`priority`, pl.link_rewrite, cl.link_rewrite AS category_rewrite
            FROM `'._DB_PREFIX_.'wishlist_product` wp
            LEFT JOIN `'._DB_PREFIX_.'product` p ON p.`id_product` = wp.`id_product`
            '.\Shop::addSqlAssociation('product', 'p').'
            LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON pl.`id_product` = wp.`id_product`'.\Shop::addSqlRestrictionOnLang('pl').'
            LEFT JOIN `'._DB_PREFIX_.'wishlist` w ON w.`id_wishlist` = wp.`id_wishlist`
            LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON cl.`id_category` = product_shop.`id_category_default` AND cl.id_lang='.(int) $id_lang.\Shop::addSqlRestrictionOnLang('cl').'
            WHERE w.`id_customer` = '.(int) $id_customer.'
            AND pl.`id_lang` = '.(int) $id_lang.'
            AND wp.`id_wishlist` = '.(int) $id_wishlist.
            (empty($id_product) === false ? ' AND wp.`id_product` = '.(int) $id_product : '').
            ($quantity == true ? ' AND wp.`quantity` != 0': '').'
            GROUP BY p.id_product, wp.id_product_attribute'
        );
        if (true === empty($products)){
            return [];
        }
        for ($i = 0; $i < sizeof($products); ++$i)
        {
            if (isset($products[$i]['id_product_attribute']) &&
                \Validate::isUnsignedInt($products[$i]['id_product_attribute']))
            {
                $result = \Db::getInstance()->executeS('
                SELECT al.`name` AS attribute_name, pa.`quantity` AS "attribute_quantity"
                FROM `'._DB_PREFIX_.'product_attribute_combination` pac
                LEFT JOIN `'._DB_PREFIX_.'attribute` a ON (a.`id_attribute` = pac.`id_attribute`)
                LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag ON (ag.`id_attribute_group` = a.`id_attribute_group`)
                LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int) $id_lang.')
                LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int) $id_lang.')
                LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (pac.`id_product_attribute` = pa.`id_product_attribute`)
                '.\Shop::addSqlAssociation('product_attribute', 'pa').'
                WHERE pac.`id_product_attribute` = '.(int)($products[$i]['id_product_attribute']));
                $products[$i]['attributes_small'] = '';
                if ($result)
                    foreach ($result AS $k => $row)
                        $products[$i]['attributes_small'] .= $row['attribute_name'].', ';
                $products[$i]['attributes_small'] = rtrim($products[$i]['attributes_small'], ', ');
                if (isset($result[0]))
                    $products[$i]['attribute_quantity'] = $result[0]['attribute_quantity'];
            } else {
                $products[$i]['attribute_quantity'] = $products[$i]['product_quantity'];
            }
        }
        return $products;
    }

    /**
     * Add bought product
     *
     * @return boolean succeed
     */
    public static function addBoughtProduct($id_wishlist, $id_product, $id_product_attribute, $id_cart, $quantity)
    {
        if (!\Validate::isUnsignedId($id_wishlist) ||
            !\Validate::isUnsignedId($id_product) ||
            !\Validate::isUnsignedId($quantity)) {
                return false;
            }

        $result = \Db::getInstance()->getRow('
            SELECT `quantity`, `id_wishlist_product`
            FROM `'._DB_PREFIX_.'wishlist_product` wp
            WHERE `id_wishlist` = '.(int)$id_wishlist.'
            AND `id_product` = '.(int)$id_product.'
            AND `id_product_attribute` = '.(int)$id_product_attribute);

        if (!sizeof($result) ||
            ($result['quantity'] - $quantity) < 0 ||
            $quantity > $result['quantity'])
            {
                return false;
            }

            \Db::getInstance()->executeS('
            SELECT *
            FROM `'._DB_PREFIX_.'wishlist_product_cart`
            WHERE `id_wishlist_product`='.(int) $result['id_wishlist_product'].' AND `id_cart`='.(int) $id_cart
            );

        if (\Db::getInstance()->NumRows() > 0) {
            $result2 = \Db::getInstance()->execute('
                UPDATE `'._DB_PREFIX_.'wishlist_product_cart`
                SET `quantity`=`quantity` + '.(int) $quantity.'
                WHERE `id_wishlist_product`='.(int) $result['id_wishlist_product'].' AND `id_cart`='.(int) $id_cart
                );
        }  else {
            $result2 = \Db::getInstance()->execute('
                INSERT INTO `'._DB_PREFIX_.'wishlist_product_cart`
                (`id_wishlist_product`, `id_cart`, `quantity`, `date_add`) VALUES(
                '.(int) $result['id_wishlist_product'].',
                '.(int) $id_cart.',
                '.(int) $quantity.',
                \''.pSQL(date('Y-m-d H:i:s')).'\')');
        }

        if ($result2 === false) {
            return false;
        }

        return (\Db::getInstance()->execute('
            UPDATE `'._DB_PREFIX_.'wishlist_product` SET
            `quantity` = '.(int) ($result['quantity'] - $quantity).'
            WHERE `id_wishlist` = '.(int) $id_wishlist.'
            AND `id_product` = '.(int) $id_product.'
            AND `id_product_attribute` = '.(int) $id_product_attribute));
    }
}
