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

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;

class AdminAjaxPrestashopWishlistController extends FrameworkBundleAdminController
{
    private function setWishlistConfigurationAction($params)
    {
        // $key must be ID of lang so json for wishlistPageName should look like:
        // {"wishlistPageName": {"1":"wishlistPageNameFR", "1":"wishlistPageNameFR"} }
        if (isset($params['wishlistPageName'])) {
            $wishlistNames = json_decode($params['wishlistPageName'], true);
            foreach ($wishlistNames as $key => $value) {
                Configuration::udpateValue('blockwishlist_wishlistPageName',[$key, $value]);
            }
        }

        if (isset($params['wishlistDefaultTitle'])) {
            $wishlistDefaultTitle = json_decode($params['wishlistDefaultTitle'], true);
            foreach ($wishlistDefaultTitle as $key => $value) {
                Configuration::udpateValue('blockwishlist_wishlistDefaultTitle',[$key, $value]);
            }
        }

        if (isset($params['createNewButtonLabel'])) {
            $createNewButtons = json_decode($params['createNewButton'], true);
            foreach ($createNewButtons as $key => $value) {
                Configuration::udpateValue('blockwishlist_createNewButtonLabel',[$key, $value]);
            }
        }
    }

    private function getWishlistConfigurationAction($params)
    {
        $datas = [
            'wishlistNames' => Configuration::get('blockwishlist_wishlistPageName'),
            'wishlistDefaultTitles' => Configuration::get('blockwishlist_wishlistDefaultTitle'),
            'wishlistCreateNewButtonsLabel' => Configuration::get('blockwishlist_createNewButtonLabel')
        ];

        return json_encode($datas);
    }
}
