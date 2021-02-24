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
 * @param BlockWishList $moduleadd upgrade
 *
 * @return bool
 */
function upgrade_module_2_0_0($module)
{
    if (false === (new Install($module->getTranslator()))->run()) {
        return false;
    }

    $products = Db::getInstance()->executeS(
        'SELECT wp.`id_product`, wp.`id_product_attribute`
        FROM `' . _DB_PREFIX_ . 'wishlist_product` wp'
    );

    foreach ($products as $k => $field) {
        $newStat = new Statistics();
        $newStat->id_product = $field['id_product'];
        $newStat->id_product_attribute = $field['id_product_attribute'];
        $newStat->id_shop = $this->context->shop->id;
        $newStat->save();
    }

    return $module->registerHook(BlockWishList::HOOKS)
        && Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'wishlist_email`');
}
