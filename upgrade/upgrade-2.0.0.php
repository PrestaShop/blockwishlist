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

use PrestaShop\Module\BlockWishList\Database\Install;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @param BlockWishList $module
 *
 * @return bool
 */
function upgrade_module_2_0_0($module)
{
    if (false === (new Install($module->getTranslator()))->run()) {
        return false;
    }

    $db = Db::getInstance();
    $now = date('Y-m-d H:i:s');

    return $module->registerHook(BlockWishList::HOOKS)
        && $db->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'wishlist_email`')
        && $db->execute('
        INSERT INTO `' . _DB_PREFIX_ . 'blockwishlist_statistics` (`id_product`, `id_product_attribute`, `date_add`, `id_shop`)
        SELECT `id_product`, `id_product_attribute`, "' . pSQL($now) . '", ' . (int) Configuration::get('PS_SHOP_DEFAULT') . ' FROM `' . _DB_PREFIX_ . 'wishlist_product`
    ');
}
