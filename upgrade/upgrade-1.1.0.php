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
if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @param BlockWishList $module
 *
 * @return bool
 */
function upgrade_module_1_1_0($module)
{
    $result = true;
    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `' . _DB_PREFIX_ . 'wishlist`');

    if (is_array($list_fields)) {
        foreach ($list_fields as $field) {
            if ($field['Field'] === 'id_shop_group') {
                $result = $result && (bool) Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'wishlist` CHANGE `id_group_shop` `id_shop_group` INT( 11 ) NOT NULL DEFAULT "1"');
            }
        }
    }

    return $result
        && $module->registerHook('displayProductListFunctionalButtons')
        && $module->registerHook('top');
}
