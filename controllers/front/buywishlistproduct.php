<?php
/**
 * 2007-2018 PrestaShop
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class BlockWishlistBuywishlistproductModuleFrontController
 */
class BlockWishlistBuywishlistproductModuleFrontController extends ModuleFrontController
{
    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function initContent()
    {
        parent::initContent();

        $error = '';
        /** @var BlockWishlist $module */
        $module = $this->module;

        $token = Tools::getValue('token');
        $idProduct = (int) Tools::getValue('id_product');
        $idProductAttribute = (int) Tools::getValue('id_product_attribute');
        if (Configuration::get('PS_TOKEN_ENABLE') == 1 && strcmp(Tools::getToken(false), Tools::getValue('static_token'))) {
            $error = $module->l('Invalid token', 'buywishlistproduct');
        }

        if (!strlen($error) &&
            empty($token) === false &&
            empty($idProduct) === false) {
            $wishlist = Wishlist::getByToken($token);
            if ($wishlist !== false) {
                Wishlist::addBoughtProduct($wishlist['id_wishlist'], $idProduct, $idProductAttribute, $this->context->cart->id, 1);
            }
        } else {
            $error = $module->l('You must log in', 'buywishlistproduct');
        }

        if (empty($error) === false) {
            echo $error;
        }
        exit;
    }
}



