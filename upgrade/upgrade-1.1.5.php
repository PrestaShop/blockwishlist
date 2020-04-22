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
if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @param BlockWishList $module
 *
 * @return bool
 */
function upgrade_module_1_1_5($module)
{
    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `' . _DB_PREFIX_ . 'wishlist`');

    if (is_array($list_fields)) {
        foreach ($list_fields as $field) {
            if ($field['Field'] === 'default' && $field['Type'] === 'int(11)') {
                return (bool) Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'wishlist` CHANGE `default` `default` INT( 11 ) NOT NULL DEFAULT "0"');
            }
        }
    }

    return true;
}
