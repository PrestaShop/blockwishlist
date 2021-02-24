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

use BlockWishList;
use Configuration;
use Db;
use Language;
use Symfony\Component\Translation\TranslatorInterface;
use Tab;

class Install
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function run()
    {
        return $this->installTables()
            && $this->installConfiguration()
            && $this->installTabs();
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

        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'blockwishlist_statistics` (
            `id_statistics` int(10) unsigned NOT NULL auto_increment,
            `id_cart` int(10) unsigned default NULL,
            `id_product` int(10) unsigned NOT NULL,
            `id_product_attribute` int(10) unsigned NOT NULL,
            `date_add` datetime NOT NULL,
            `id_shop` int(10) unsigned default 1,
            PRIMARY KEY  (`id_statistics`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        $result = true;

        foreach ($sql as $query) {
            $result = $result && Db::getInstance()->execute($query);
        }

        return $result;
    }

    public function installConfiguration()
    {
        $pageName = $defaultName = $createButtonLabel = [];

        foreach (Language::getLanguages() as $lang) {
            $pageName[$lang['id_lang']] = $this->translator->trans('My wishlists', [], 'Modules.Blockwishlist.Admin', $lang['locale']);
            $defaultName[$lang['id_lang']] = $this->translator->trans('My wishlist', [], 'Modules.Blockwishlist.Admin', $lang['locale']);
            $createButtonLabel[$lang['id_lang']] = $this->translator->trans('Create new list', [], 'Modules.Blockwishlist.Admin', $lang['locale']);
        }

        return Configuration::updateValue('blockwishlist_WishlistPageName', $pageName)
            && Configuration::updateValue('blockwishlist_WishlistDefaultTitle', $defaultName)
            && Configuration::updateValue('blockwishlist_CreateButtonLabel', $createButtonLabel);
    }

    public function installTabs()
    {
        $installTabCompleted = true;

        foreach (BlockWishList::MODULE_ADMIN_CONTROLLERS as $controller) {
            if (Tab::getIdFromClassName($controller['class_name'])) {
                continue;
            }

            $tab = new Tab();
            $tab->class_name = $controller['class_name'];
            $tab->active = $controller['visible'];
            foreach (Language::getLanguages() as $lang) {
                $tab->name[$lang['id_lang']] = $this->translator->trans($controller['name'], [], 'Modules.BlockWishList.Admin', $lang['locale']);
            }
            $tab->id_parent = Tab::getIdFromClassName($controller['parent_class_name']);
            $tab->module = 'blockwishlist';
            $installTabCompleted = $installTabCompleted && $tab->add();
        }

        return $installTabCompleted;
    }
}
