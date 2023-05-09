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

namespace PrestaShop\Module\BlockWishList\Database;

use BlockWishList;
use Db;
use Tab;
use Validate;

class Uninstall
{
    public function run()
    {
        return $this->dropTables() && $this->uninstallTabs();
    }

    private function dropTables()
    {
        $sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'wishlist`';
        $sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'wishlist_product`';
        $sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'wishlist_product_cart`';
        $sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'blockwishlist_statistics`';

        $result = true;
        foreach ($sql as $query) {
            $result = $result && Db::getInstance()->execute($query);
        }

        return $result;
    }

    private function uninstallTabs()
    {
        $uninstallTabCompleted = true;

        foreach (BlockWishList::MODULE_ADMIN_CONTROLLERS as $controller) {
            $id_tab = (int) Tab::getIdFromClassName($controller['class_name']);
            $tab = new Tab($id_tab);
            if (Validate::isLoadedObject($tab)) {
                $uninstallTabCompleted = $uninstallTabCompleted && $tab->delete();
            }
        }

        return $uninstallTabCompleted;
    }
}
