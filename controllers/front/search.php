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

use PrestaShop\Module\BlockWishList\Search\WishListProductSearchProvider;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

class BlockWishlistSearchModuleFrontController extends ProductListingFrontController
{
    /**
     * @var BlockWishList
     */
    private $module;

    /**
     * @var WishList
     */
    protected $wishlist;

    /**
     * @var string
     */
    private $page_name;

    public function __construct()
    {
        /** @var BlockWishList $module */
        $module = Module::getInstanceByName('blockwishlist');
        $this->module = $module;

        if (empty($this->module->active)) {
            Tools::redirect('index');
        }

        $this->page_name = 'module-' . $this->module->name . '-' . Dispatcher::getInstance()->getController();

        parent::__construct();

        $this->controller_type = 'modulefront';
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $id_wishlist = (int) Tools::getValue('id_wishlist');
        $this->wishlist = new WishList($id_wishlist);

        if (false === Validate::isLoadedObject($this->wishlist)) {
            Tools::redirect('index.php?controller=404');
        }

        $token = Tools::getValue('token');

        if ($token !== $this->wishlist->token
            && (false === Validate::isLoadedObject($this->context->customer) || (int) $this->wishlist->id_customer !== $this->context->customer->id)
        ) {
            header('HTTP/1.1 403 Forbidden');
            header('Status: 403 Forbidden');
            $this->errors[] = $this->trans(
                'You do not have access to this wishlist.',
                [],
                'Modules.BlockWishList.Shop'
            );
            $this->setTemplate('errors/forbidden');
        }

        $this->context->smarty->assign(
            [
                'id' => $id_wishlist,
                'url' => Context::getContext()->link->getModuleLink('blockwishlist', 'search', ['id_wishlist' => $id_wishlist]),
                'wishlistsLink' => Context::getContext()->link->getModuleLink('blockwishlist', 'lists'),
                'deleteProductUrl' => Context::getContext()->link->getModuleLink('blockwishlist', 'action', ['action' => 'deleteProductFromWishlist']),
            ]
        );

        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function initContent()
    {
        parent::initContent();

        $this->context->controller->registerJavascript(
            'blockwishlistController',
            'modules/blockwishlist/public/productslist.bundle.js',
            [
              'priority' => 200,
            ]
        );

        $this->doProductSearch(
            '../../../modules/blockwishlist/views/templates/pages/products-list.tpl',
            [
                'entity' => 'wishlist_product',
                'id_wishlist' => $this->wishlist->id,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getListingLabel()
    {
        return $this->trans(
            'WishList: %wishlist_name%',
            ['%wishlist_name%' => $this->wishlist->name],
            'Modules.BlockWishList.Shop'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getProductSearchQuery()
    {
        $query = new ProductSearchQuery();
        $query->setSortOrder(
            new SortOrder(
                'product',
                Tools::getProductsOrder('by'),
                Tools::getProductsOrder('way')
            )
        );

        return $query;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultProductSearchProvider()
    {
        return new WishListProductSearchProvider(
            Db::getInstance(),
            $this->wishlist,
            $this->context
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getAjaxProductSearchVariables()
    {
        $search = parent::getAjaxProductSearchVariables();
        // @todo Adds custom data for ajax

        return $search;
    }
}
