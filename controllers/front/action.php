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

use PrestaShop\Module\BlockWishList\Access\CustomerAccess;

class BlockWishListActionModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        if (false === $this->context->customer->isLogged()) {
            $this->ajaxRender(
                json_encode([
                    'success' => false,
                    'message' => $this->trans('You aren\'t logged in', [], 'Modules.Blockwishlist.Shop'),
                ])
            );
            exit;
        }

        $params = Tools::getValue('params');
        // Here we call all methods dynamically given by the path

        if (method_exists($this, Tools::getValue('action') . 'Action')) {
            call_user_func([$this, Tools::getValue('action') . 'Action'], $params);
            exit;
        }

        $this->ajaxRender(
            json_encode([
                'success' => false,
                'message' => $this->trans('Unknown action', [], 'Modules.Blockwishlist.Shop'),
            ])
        );
        exit;
    }

    private function addProductToWishListAction($params)
    {
        $id_product = (int) $params['id_product'];
        $product = new Product($id_product);
        if (!Validate::isLoadedObject($product)) {
            return $this->ajaxRender(
                json_encode([
                    'success' => false,
                    'message' => $this->trans('There was an error adding the product', [], 'Modules.Blockwishlist.Shop'),
                ])
            );
        }

        $idWishList = (int) $params['idWishList'];
        $id_product_attribute = (int) $params['id_product_attribute'];
        $quantity = (int) $params['quantity'];
        if (0 === $quantity) {
            $quantity = $product->minimal_quantity;
        }

        if (false === $this->assertProductAttributeExists($id_product, $id_product_attribute) && $id_product_attribute !== 0) {
            return $this->ajaxRender(
                json_encode([
                    'success' => false,
                    'message' => $this->trans('There was an error while adding the product attributes', [], 'Modules.Blockwishlist.Shop'),
                ])
            );
        }

        $wishlist = new WishList($idWishList);
        // Exit if not owner of the wishlist
        $this->assertWriteAccess($wishlist);

        $productIsAdded = $wishlist->addProduct(
            $idWishList,
            $this->context->customer->id,
            $id_product,
            $id_product_attribute,
            $quantity
        );

        $newStat = new Statistics();
        $newStat->id_product = $id_product;
        $newStat->id_product_attribute = $id_product_attribute;
        $newStat->id_shop = $this->context->shop->id;
        $newStat->save();

        if (false === $productIsAdded) {
            return $this->ajaxRender(
                json_encode([
                    'success' => false,
                    'message' => $this->trans('There was an error adding the product', [], 'Modules.Blockwishlist.Shop'),
                ])
            );
        }

        Hook::exec('actionWishlistAddProduct', [
            'idWishlist' => $idWishList,
            'customerId' => $this->context->customer->id,
            'idProduct' => $id_product,
            'idProductAttribute' => $id_product_attribute,
        ]);

        return $this->ajaxRender(
            json_encode([
                'success' => true,
                'message' => $this->trans('Product added', [], 'Modules.Blockwishlist.Shop'),
            ])
        );
    }

    private function createNewWishListAction($params)
    {
        if (isset($params['name'])) {
            if (!Validate::isGenericName($params['name'])) {
                return $this->ajaxRender(
                    json_encode([
                        'success' => false,
                        'message' => $this->trans('The list name is invalid.', [], 'Modules.Blockwishlist.Shop'),
                        'datas' => [
                            'name' => $params['name'],
                        ],
                    ])
                );
            }

            $wishlist = new WishList();
            $wishlist->name = $params['name'];
            $wishlist->id_shop_group = $this->context->shop->id_shop_group;
            $wishlist->id_customer = $this->context->customer->id;
            $wishlist->id_shop = $this->context->shop->id;
            $wishlist->token = $this->generateWishListToken();

            if (true === $wishlist->save()) {
                return $this->ajaxRender(
                    json_encode([
                        'success' => true,
                        'message' => $this->trans('The list has been properly created', [], 'Modules.Blockwishlist.Shop'),
                        'datas' => [
                            'name' => $wishlist->name,
                            'id_wishlist' => $wishlist->id,
                        ],
                    ])
                );
            }

            return $this->ajaxRender(
                json_encode([
                    'success' => false,
                    'message' => $this->trans('Error saving the new list', [], 'Modules.Blockwishlist.Shop'),
                ])
            );
        } else {
            return $this->ajaxRender(
                json_encode([
                    'success' => false,
                    'message' => $this->trans('Missing name parameter', [], 'Modules.Blockwishlist.Shop'),
                ])
            );
        }
    }

    private function renameWishListAction($params)
    {
        if (isset($params['idWishList'], $params['name'])) {
            if (!Validate::isGenericName($params['name'])) {
                return $this->ajaxRender(
                    json_encode([
                        'success' => false,
                        'message' => $this->trans('The list name is invalid', [], 'Modules.Blockwishlist.Shop'),
                        'datas' => [
                            'name' => $params['name'],
                            'id_whishlist' => $params['idWishList'],
                        ],
                    ])
                );
            }

            $wishlist = new WishList($params['idWishList']);
            // Exit if not owner of the wishlist
            $this->assertWriteAccess($wishlist);

            $wishlist->name = $params['name'];

            if (true === $wishlist->save()) {
                return $this->ajaxRender(
                    json_encode([
                        'success' => true,
                        'message' => $this->trans('List has been renamed', [], 'Modules.Blockwishlist.Shop'),
                    ])
                );
            }

            return $this->ajaxRender(
                json_encode([
                    'success' => false,
                    'message' => $this->trans('List could not be renamed', [], 'Modules.Blockwishlist.Shop'),
                ])
            );
        }

        return $this->ajaxRenderMissingParams();
    }

    private function deleteWishListAction($params)
    {
        if (isset($params['idWishList'])) {
            $wishlist = new WishList($params['idWishList']);

            // Exit if not owner of the wishlist
            $this->assertWriteAccess($wishlist);

            if (true === (bool) $wishlist->delete()) {
                return $this->ajaxRender(
                    json_encode([
                        'success' => true,
                        'message' => $this->trans('List has been removed', [], 'Modules.Blockwishlist.Shop'),
                    ])
                );
            }

            return $this->ajaxRender(
                json_encode([
                    'success' => false,
                    'message' => $this->trans('List deletion was unsuccessful', [], 'Modules.Blockwishlist.Shop'),
                ])
            );
        }

        return $this->ajaxRenderMissingParams();
    }

    private function deleteProductFromWishListAction($params)
    {
        if (
            isset($params['idWishList'])
            && isset($params['id_product'])
            && isset($params['id_product_attribute'])
        ) {
            // Exit if not owner of the wishlist
            $this->assertWriteAccess(
                new WishList($params['idWishList'])
            );

            $isDeleted = WishList::removeProduct(
                $params['idWishList'],
                $this->context->customer->id,
                $params['id_product'],
                $params['id_product_attribute']
            );

            if (true === $isDeleted) {
                return $this->ajaxRender(
                    json_encode([
                        'success' => true,
                        'message' => $this->trans('Product successfully removed', [], 'Modules.Blockwishlist.Shop'),
                    ])
                );
            }

            return $this->ajaxRender(
                json_encode([
                    'success' => false,
                    'message' => $this->trans('Unable to remove product from list', [], 'Modules.Blockwishlist.Shop'),
                ])
            );
        }

        return $this->ajaxRenderMissingParams();
    }

    private function updateProductFromWishListAction($params)
    {
        if (isset(
            $params['idWishList'],
            $params['id_product'],
            $params['id_product_attribute'],
            $params['priority'],
            $params['quantity']
        )) {
            // Exit if not owner of the wishlist
            $this->assertWriteAccess(
                new WishList($params['idWishList'])
            );

            $isDeleted = WishList::updateProduct(
                $params['idWishList'],
                $params['id_product'],
                $params['id_product_attribute'],
                $params['priority'],
                $params['quantity']
            );

            if (true === $isDeleted) {
                return $this->ajaxRender(
                    json_encode([
                        'success' => true,
                        'message' => $this->trans('Product successfully updated', [], 'Modules.Blockwishlist.Shop'),
                    ])
                );
            }

            return $this->ajaxRender(
                json_encode([
                    'success' => false,
                    'message' => $this->trans('Unable to update product from wishlist', [], 'Modules.Blockwishlist.Shop'),
                ])
            );
        }

        return $this->ajaxRenderMissingParams();
    }

    private function getAllWishListAction()
    {
        $infos = WishList::getAllWishListsByIdCustomer($this->context->customer->id);
        if (empty($infos)) {
            $wishlist = new WishList();
            $wishlist->id_shop = $this->context->shop->id;
            $wishlist->id_shop_group = $this->context->shop->id_shop_group;
            $wishlist->id_customer = $this->context->customer->id;
            $wishlist->name = Configuration::get('blockwishlist_WishlistDefaultTitle', $this->context->language->id);
            $wishlist->token = $this->generateWishListToken();
            $wishlist->default = 1;
            $wishlist->add();

            $infos = WishList::getAllWishListsByIdCustomer($this->context->customer->id);
        }

        foreach ($infos as $key => $wishlist) {
            $infos[$key]['shareUrl'] = $this->context->link->getModuleLink('blockwishlist', 'view', ['token' => $wishlist['token']]);
            $infos[$key]['listUrl'] = $this->context->link->getModuleLink('blockwishlist', 'view', ['id_wishlist' => $wishlist['id_wishlist']]);
        }

        if (false === empty($infos)) {
            return $this->ajaxRender(
                json_encode([
                    'wishlists' => $infos,
                ])
            );
        }

        return $this->ajaxRenderMissingParams();
    }

    private function generateWishListToken()
    {
        return strtoupper(substr(sha1(uniqid((string) rand(), true) . _COOKIE_KEY_ . $this->context->customer->id), 0, 16));
    }

    private function ajaxRenderMissingParams()
    {
        return $this->ajaxRender(
            json_encode([
                'success' => false,
                'message' => $this->trans('Request is missing one or multiple parameters', [], 'Modules.Blockwishlist.Shop'),
            ])
        );
    }

    private function addProductToCartAction($params)
    {
        $productAdd = WishList::addBoughtProduct(
            $params['idWishlist'],
            $params['id_product'],
            $params['id_product_attribute'],
            (int) $this->context->cart->id,
            $params['quantity']
        );

        // Transform an add to favorite
        Db::getInstance()->execute('
            UPDATE `' . _DB_PREFIX_ . 'blockwishlist_statistics`
            SET `id_cart` = ' . (int) $this->context->cart->id . '
            WHERE `id_cart` = 0
            AND `id_product` = ' . (int) $params['id_product'] . '
            AND `id_product_attribute` = ' . (int) $params['id_product_attribute'] . '
            AND `id_shop`= ' . $this->context->shop->id
        );

        if (true === $productAdd) {
            return $this->ajaxRender(
                json_encode([
                    'success' => true,
                    'message' => $this->trans('Product added to cart', [], 'Modules.Blockwishlist.Shop'),
                ])
            );
        }

        return $this->ajaxRender(
            json_encode([
                'success' => false,
                'message' => $this->trans('Error when adding product to cart', [], 'Modules.Blockwishlist.Shop'),
            ])
        );
    }

    private function getUrlByIdWishListAction($params)
    {
        $wishlist = new WishList($params['idWishList']);

        return $this->ajaxRender(
            json_encode([
                'status' => 'true',
                'url' => $this->context->link->getModuleLink('blockwishlist', 'view', ['token' => $wishlist->token]),
            ])
        );
    }

    /**
     * Stop the execution if the current customer isd not allowed to alter the wishlist
     *
     * @param WishList $wishlist
     */
    private function assertWriteAccess(WishList $wishlist)
    {
        if ((new CustomerAccess($this->context->customer))->hasWriteAccessToWishlist($wishlist)) {
            return;
        }

        $this->ajaxRender(
            json_encode([
                'success' => false,
                'message' => $this->trans('You\'re not allowed to manage this list.', [], 'Modules.Blockwishlist.Shop'),
            ])
        );
        exit;
    }

    /**
     * Check if product attribute id is related to the product
     *
     * @param int $id_product
     * @param int $id_product_attribute
     *
     * @return bool
     */
    private function assertProductAttributeExists($id_product, $id_product_attribute)
    {
        return Db::getInstance()->getValue(
            'SELECT pas.`id_product_attribute` ' .
            'FROM `' . _DB_PREFIX_ . 'product_attribute` pa ' .
            'INNER JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` pas ON (pas.id_product_attribute = pa.id_product_attribute) ' .
            'WHERE pas.id_shop =' . (int) $this->context->shop->id . ' AND pa.`id_product` = ' . (int) $id_product . ' ' .
            'AND pas.id_product_attribute = ' . (int) $id_product_attribute
        );
    }
}
