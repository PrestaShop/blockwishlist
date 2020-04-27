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

use PrestaShop\Module\BlockWishlist\WishList;

class BlockWishlistActionModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        if (!$this->context->customer->isLogged()) {
            die(Tools::jsonEncode(
                [
                    'success' => false,
                    'error' => $this->module->l('You aren\'t logged in', 'mywishlist'),
                ]));
        }
        $params = Tools::getValue('params');

        $params = [
            'id_wishlist' => 3,
            'id_product' => 1,
            'id_product_attribute' => 3,
            'quantity' => 2,
        ];

        if (method_exists($this, Tools::getValue('action') . 'Action')) {
            call_user_func([$this, Tools::getValue('action') . 'Action'], $params);
        } else {
            die(Tools::jsonEncode(
                [
                    'success' => false,
                    'error' => $this->module->l('Unknow action', 'mywishlist'),
                ]));
        }

        die;
        // return error response
    }

    public function AddToAWishlistAction($params)
    {
        $id_customer = (int) $this->context->customer->id;
        $id_wishlist = (int) $params['id_wishlist'];
        $id_product = (int) $params['id_product'];
        $id_product_attribute = (int) $params['id_product_attribute'];
        $quantity = (int) $params['quantity'];

        if (wishlist::exists($id_wishlist, $id_customer) === false && $id_wishlist !== 0) {
            $wishlist = new wishlist();
            $wishlist->id_shop = $this->context->shop->id;
            $wishlist->id_shop_group = $this->context->shop->id_shop_group;
            $wishlist->id_customer = $id_customer;
            $wishlist->name = 'default';
            $wishlist->token = strtoupper(substr(sha1(uniqid(rand(), true) . _COOKIE_KEY_ . $this->context->customer->id), 0, 16));
            $wishlist->default = 1;
            $wishlist->add();
        } else {
            $wishlist = new wishlist($id_wishlist);
        }

        if ($wishlist->addProduct($id_wishlist, $this->context->customer->id, $id_product, $id_product_attribute, $quantity) === false) {
            die(Tools::jsonEncode(
                [
                    'success' => false,
                    'error' => $this->module->l('There was an error adding the product', 'mywishlist'),
                ]));
        }

        die(Tools::jsonEncode(
            [
                'success' => true,
                'success' => $this->module->l('Product add', 'mywishlist'),
            ]));
    }
}
