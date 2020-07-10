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
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;

class BlockWishlistViewModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
    }

    public function initContent()
    {
        parent::initContent();
        $token = Tools::getValue('token');

        $module = new BlockWishList();

        if (true === empty($token)) {
            $this->setTemplate('module:blockwishlist/views/templates/pages/view.tpl');

            return false;
        }

        $wishlist = WishList::getByToken($token);
        WishList::refreshWishList($wishlist['id_wishlist']);

        $products = WishList::getProductByIdCustomer(
            (int) $wishlist['id_wishlist'],
            (int) $wishlist['id_customer'],
            $this->context->language->id,
            null,
            true
        );

        $nb_products = count($products);
        $priority_names = [0 => $module->l('High'), 1 => $module->l('Medium'), 2 => $module->l('Low')];

        for ($i = 0; $i < $nb_products; ++$i) {
            $product = new Product((int) $products[$i]['id_product'], true, $this->context->language->id);

            $products[$i]['priority_name'] = $priority_names[$products[$i]['priority']];
            $quantity = Product::getQuantity((int) $products[$i]['id_product'], $products[$i]['id_product_attribute']);
            $products[$i]['attribute_quantity'] = $quantity;
            $products[$i]['product_quantity'] = $quantity;
            $products[$i]['allow_oosp'] = $product->isAvailableWhenOutOfStock((int) $product->out_of_stock);

            if ($products[$i]['id_product_attribute'] != 0) {
                $combination_imgs = $product->getCombinationImages($this->context->language->id);
                if (isset($combination_imgs[$products[$i]['id_product_attribute']][0])) {
                    $products[$i]['cover'] = $product->id . '-' . $combination_imgs[$products[$i]['id_product_attribute']][0]['id_image'];
                } else {
                    $cover = Product::getCover($product->id);
                    $products[$i]['cover'] = $product->id . '-' . $cover['id_image'];
                }
            } else {
                $images = $product->getImages($this->context->language->id);
                foreach ($images as $image) {
                    if ($image['cover']) {
                        $products[$i]['cover'] = $product->id . '-' . $image['id_image'];
                        break;
                    }
                }
            }
            if (!isset($products[$i]['cover'])) {
                $products[$i]['cover'] = $this->context->language->iso_code . '-default';
            }

            $products[$i]['bought'] = false;
        }

        $assembler = new ProductAssembler($this->context);
        $presenterFactory = new ProductPresenterFactory($this->context);
        $presentationSettings = $presenterFactory->getPresentationSettings();
        $presenter = new ProductListingPresenter(
            new ImageRetriever(
                $this->context->link
            ),
            $this->context->link,
            new PriceFormatter(),
            new ProductColorsRetriever(),
            $this->context->getTranslator()
        );
        $products_for_template = [];

        if (is_array($products)) {
            foreach ($products as $rawProduct) {
                $products_for_template[] = $presenter->present(
                    $presentationSettings,
                    $assembler->assembleProduct($rawProduct),
                    $this->context->language
                );
            }
        }

        WishList::incCounter((int) $wishlist['id_wishlist']);
        $ajax = Configuration::get('PS_BLOCK_CART_AJAX');

        $wishlists = WishList::getByIdCustomer((int) $wishlist['id_customer']);

        foreach ($wishlists as $key => $item) {
            if ($item['id_wishlist'] == $wishlist['id_wishlist']) {
                unset($wishlists[$key]);
                break;
            }

            $this->context->controller->registerJavascript(
                'blockwishlistController',
                'modules/blockwishlist/public/productslist.bundle.js',
                [
                  'priority' => 200,
                ]
            );

            $this->context->smarty->assign(
                [
                    'current_wishlist' => $wishlist,
                    'token' => $token,
                    'ajax' => (($ajax == 1) ? '1' : '0'),
                    'wishlists' => $wishlists,
                    'isShare' => true,
                    'products' => $products_for_template,
                ]
            );

            $this->setTemplate('module:blockwishlist/views/templates/pages/view.tpl');
        }
    }
}
