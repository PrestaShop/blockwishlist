<?php
/**
 * 2007-2020 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2020 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

 namespace PrestaShop\Module\BlockWishList\Database;

 class Install
 {
     private $module;

     public function __construct(\BlockWishList $module) {
         $this->module = $module;
     }

     public function installTables()
     {
        $sql = [];

        $sql[] = ' CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'wishlist` (
          `id_wishlist` int(10) unsigned NOT NULL auto_increment,
          `id_customer` int(10) unsigned NOT NULL,
          `token` varchar(64) character set utf8 NOT NULL,
          `name` varchar(64) character set utf8 NOT NULL,
          `counter` int(10) unsigned NULL,
          `id_shop` int(10) unsigned default 1,
          `id_shop_group` int(10) unsigned default 1,
          `date_add` datetime NOT NULL,
          `date_upd` datetime NOT NULL,
          `default` int(10) unsigned default 0,
          PRIMARY KEY  (`id_wishlist`)
        ) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=utf8;';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'wishlist_email` (
          `id_wishlist` int(10) unsigned NOT NULL,
          `email` varchar(128) character set utf8 NOT NULL,
          `date_add` datetime NOT NULL
        ) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=utf8;';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'wishlist_product` (
          `id_wishlist_product` int(10) NOT NULL auto_increment,
          `id_wishlist` int(10) unsigned NOT NULL,
          `id_product` int(10) unsigned NOT NULL,
          `id_product_attribute` int(10) unsigned NOT NULL,
          `quantity` int(10) unsigned NOT NULL,
          `priority` int(10) unsigned NOT NULL,
          PRIMARY KEY  (`id_wishlist_product`)
        ) ENGINE=_MYSQL_ENGINE_  DEFAULT CHARSET=utf8;';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'wishlist_product_cart` (
          `id_wishlist_product` int(10) unsigned NOT NULL,
          `id_cart` int(10) unsigned NOT NULL,
          `quantity` int(10) unsigned NOT NULL,
          `date_add` datetime NOT NULL
        ) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=utf8;';

        foreach ($sql as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }
        return true;
     }
 }
