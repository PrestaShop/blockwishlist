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

namespace PrestaShop\Module\BlockWishList\Database;

class Install
{
    private $module;

    public function __construct(\BlockWishList $module)
    {
        $this->module = $module;
    }

    public function installTables()
    {
        $sql = [];

        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'wishlist` (
          `id_wishlist` int(10) unsigned NOT NULL auto_increment,
          `id_customer` int(10) unsigned NOT NULL,
          `id_shop` int(10) unsigned default 1,
          `id_shop_group` int(10) unsigned default 1,
          `token` varchar(64) NOT NULL,
          `name` varchar(64) NOT NULL,
          `counter` int(10) unsigned NULL,
          `date_add` datetime NOT NULL,
          `date_upd` datetime NOT NULL,
          `default` int(10) unsigned default 0,
          PRIMARY KEY  (`id_wishlist`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'wishlist_product` (
          `id_wishlist_product` int(10) NOT NULL auto_increment,
          `id_wishlist` int(10) unsigned NOT NULL,
          `id_product` int(10) unsigned NOT NULL,
          `id_product_attribute` int(10) unsigned NOT NULL,
          `quantity` int(10) unsigned NOT NULL,
          `priority` int(10) unsigned NOT NULL,
          PRIMARY KEY  (`id_wishlist_product`)
        ) ENGINE=' . _MYSQL_ENGINE_ . '  DEFAULT CHARSET=utf8;';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'wishlist_product_cart` (
          `id_wishlist_product` int(10) unsigned NOT NULL,
          `id_cart` int(10) unsigned NOT NULL,
          `quantity` int(10) unsigned NOT NULL,
          `date_add` datetime NOT NULL
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        $result = true;

        foreach ($sql as $query) {
            $result = $result && \Db::getInstance()->execute($query);
        }

        return $result;
    }
}
