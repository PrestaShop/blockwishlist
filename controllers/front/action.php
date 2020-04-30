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
            $this->ajaxDie(
                Tools::jsonEncode([
                    'success' => false,
                    'message' => $this->module->l('You aren\'t logged in', 'mywishlist'),
                ])
            );
        }

        $params = Tools::getValue('params');

        $params = [
            'idWishlist' => 3,
            'id_product' => 1,
            'id_product_attribute' => 3,
            'quantity' => 2,
            'name' => 'newWishTestName'
        ];

        if (method_exists($this, Tools::getValue('action') . 'Action')) {
            call_user_func([$this, Tools::getValue('action') . 'Action'], $params);
        }

        $this->ajaxDie(
            Tools::jsonEncode([
                'success' => false,
                'message' => $this->module->l('Unknow action', 'mywishlist'),
            ])
        );
    }

    public function addProductToWishlistAction($params)
    {
        $id_customer = (int) $this->context->customer->id;
        $idWishlist = (int) $params['idWishlist'];
        $id_product = (int) $params['id_product'];
        $id_product_attribute = (int) $params['id_product_attribute'];
        $quantity = (int) $params['quantity'];

        if ($idWishlist !== 0) {
            if (wishlist::exists($idWishlist, $this->context->customer->id) === false) {
                $wishlist = new wishlist();
                $wishlist->id_shop = $this->context->shop->id;
                $wishlist->id_shop_group = $this->context->shop->id_shop_group;
                $wishlist->id_customer = $this->context->customer->id;
                $wishlist->name = 'default';
                $wishlist->token = $this->generateWishlistToken();
                $wishlist->default = 1;
                $wishlist->add();
            }
        } else {
            $wishlist = new Wishlist($idWishlist);
        }

        $productIsAdded = $wishlist->addProduct(
            $idWishlist,
            $this->context->customer->id,
            $id_product,
            $id_product_attribute,
            $quantity
        );

        if ($productIsAdded === false) {
            $this->ajaxDie(
                Tools::jsonEncode([
                    'success' => false,
                    'message' => $this->module->l('There was an error adding the product', 'mywishlist'),
                ])
            );
        }

        $this->ajaxDie(
            Tools::jsonEncode([
                'success' => false,
                'message' => $this->module->l('Product added', 'mywishlist'),
            ])
        );
    }

    public function createNewWishlistAction($params)
    {
        if (isset($params['name'])) {
            $wishlist = new Wishlist();
            $wishlist->name= $params['name'];
            $wishlist->id_shop_group = $this->context->shop->id_shop_group;
            $wishlist->id_customer = $this->context->customer->id;
            $wishlist->id_shop = $this->context->shop->id;
            $wishlist->token = $this->generateWishlistToken();

            if (true === $wishlist->save()) {
                $this->ajaxDie(
                    Tools::jsonEncode([
                        'status' => 'success',
                        'message' => $this->module->l('The list has been properly created', 'mywishlist'),
                        'datas' => [
                            'name' => $wishlist->name,
                            'id' => $wishlist->id
                        ]
                    ])
                );
            }

            $this->ajaxDie(
                Tools::jsonEncode([
                    'success' => false,
                    'message' => $this->module->l('Error saving the new wishlist', 'mywishlist')
                ])
            );
        } else {
            $this->ajaxDie(
                Tools::jsonEncode([
                    'success' => false,
                    'message' => $this->module->l('Missing name parameter', 'mywishlist')
                ])
            );
        }
    }

    public function renameWishlistAction($params)
    {
        if (isset($params['idWishlist'], $params['name'])) {
            $wishlist = new wishlist($params['idWishlist']);
            $wishlist->name = $$params['name'];

            if (true === $wishlist->save()) {
                $this->ajaxDie(
                    Tools::jsonEncode([
                        'success' => true,
                        'message' => $this->module->l('Wishlist has been renamed', 'mywishlist')
                    ])
                );
            }

            $this->ajaxDie(
                Tools::jsonEncode([
                    'success' => false,
                    'message' => $this->module->l("Wishlist couldn't been renamed", 'mywishlist')
                ])
            );
        }

        $this->ajaxDieMissingParams();
    }

    public function deleteWishlistAction($params)
    {
        if (isset($params['idWishlist'])) {
            $wishlist = new wishlist($params['idWishlist']);

            if (true === $wishlist->delete()) {
                $this->ajaxDie(
                    Tools::jsonEncode([
                        'success' => true,
                        'message' => $this->module->l('Wishlist has been removed', 'mywishlist')
                    ])
                );
            }

            $this->ajaxDie(
                Tools::jsonEncode([
                    'success' => false,
                    'message' => $this->module->l("Wishlist couldn't been removed", 'mywishlist')
                ])
            );
        }

        $this->ajaxDieMissingParams();
    }

    public function deleteProductFromWishlistAction($params)
    {
        if (
            isset($params['idWishlist'])
            && isset($params['id_product'])
            && isset($params['id_product_attribute'])
            && isset($params['id_customer'])
        ) {
            $isDeleted = wishlist::removeProduct(
                $params['idWishlist'],
                $params['id_customer'],
                $params['id_product'],
                $params['id_product_attribute']
            );

            if (true === $isDeleted) {
                $this->ajaxDie(
                    Tools::jsonEncode([
                        'success' => true,
                        'message' => $this->module->l('Product succesfully removed', 'mywishlist')
                    ])
                );
            }

            $this->ajaxDie(
                Tools::jsonEncode([
                    'success' => false,
                    'message' => $this->module->l('Unable to remove product from wishlist', 'mywishlist')
                ])
            );
        }

        $this->ajaxDieMissingParams();
    }

    public function updateProductFromWishlistAction($params)
    {
        if (isset(
            $params['idWishlist'],
            $params['id_product'],
            $params['id_product_attribute'],
            $params['priority'],
            $params['quantity']
        )) {
            $isDeleted = wishlist::updateProduct(
                $params['idWishlist'],
                $params['id_product'],
                $params['id_product_attribute'],
                $params['priority'],
                $params['quantity']
            );

            if ($isDeleted) {
                $this->ajaxDie(
                    Tools::jsonEncode([
                        'success' => true,
                        'message' => $this->module->l('Product succesfully updated', 'mywishlist')
                    ])
                );
            }

            $this->ajaxDie(
                Tools::jsonEncode([
                    'success' => false,
                    'message' => $this->module->l('Unable to update product from wishlist', 'mywishlist')
                ])
            );
        }

        $this->ajaxDieMissingParams();
    }

    public function getAllWishlistAction()
    {
        $infos = Wishlist::getAllWishlistsByIdCustomer($this->context->customer->id);

        if (!empty($infos)) {
            $this->ajaxDie(
                Tools::jsonEncode([
                    'wishlists' => $infos,
                ])
            );
        }
        $this->ajaxDieMissingParams();
    }

    private function generateWishlistToken()
    {
        return strtoupper(substr(sha1(uniqid(rand(), true) . _COOKIE_KEY_ . $this->context->customer->id), 0, 16));
    }

    private function ajaxDieMissingParams()
    {
        $this->ajaxDie(
            Tools::jsonEncode([
                'success' => false,
                'message' => $this->module->l('Request is missing one or multiple parameters', 'mywishlist')
            ])
        );
    }
// TODO
// vérifier avec valentin ce dont il a besoin pour constituer un produit en front
    public function getProductsByWishlisActiont($params)
    {
        $arrProducts = [];
        $wishlistProducts = new WishList::getProductsByWishlist($params['idWishlist']);
        foreach($wishlistProducts as $product) {
            $arrProducts[$product['id_product']]['product'] = (new \Product(
                    $product['id_product'],
                    false,
                    $this->context->language->id,
                    $this->context->shop->id,
                ))
                ->updateAttribute($product['id_product_attribute']);
            $arrProducts[$product['id_product']]['quantity'] = $product['quantity'];
        }
    }
}
