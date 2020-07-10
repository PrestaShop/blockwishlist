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

use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrderFactory;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\FacetsRendererInterface;
use PrestaShop\Module\BlockWishList\Search\WishListProductSearchProvider;

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
                'wishlistName' => $this->wishlist->name,
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
            new SortOrderFactory($this->getTranslator())
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getAjaxProductSearchVariables()
    {
        $data = parent::getAjaxProductSearchVariables();

        $context = parent::getProductSearchContext();
        $query = $this->getProductSearchQuery();
        $provider = $this->getDefaultProductSearchProvider();

        $resultsPerPage = (int) Tools::getValue('resultsPerPage');
        if ($resultsPerPage <= 0) {
            $resultsPerPage = Configuration::get('PS_PRODUCTS_PER_PAGE');
        }

        // we need to set a few parameters from back-end preferences
        $query
            ->setResultsPerPage($resultsPerPage)
            ->setPage(max((int) Tools::getValue('page'), 1))
        ;

        // set the sort order if provided in the URL
        if (($encodedSortOrder = Tools::getValue('order'))) {
            $query->setSortOrder(SortOrder::newFromString(
                $encodedSortOrder
            ));
        }

        // get the parameters containing the encoded facets from the URL
        $encodedFacets = Tools::getValue('q');

        $query->setEncodedFacets($encodedFacets);

        /** @var ProductSearchResult $result */
        $result = $provider->runQuery(
            $context,
            $query
        );

        if (!$result->getCurrentSortOrder()) {
            $result->setCurrentSortOrder($query->getSortOrder());
        }

        // prepare the products
        $products = $this->prepareMultipleProductsForTemplate(
            $result->getProducts()
        );

        // render the facets
        // with the core
        $rendered_facets = $this->renderFacets(
            $result
        );
        $rendered_active_filters = $this->renderActiveFilters(
            $result
        );


        $pagination = $this->getTemplateVarPagination(
            $query,
            $result
        );

        // prepare the sort orders
        // note that, again, the product controller is sort-orders
        // agnostic
        // a module can easily add specific sort orders that it needs
        // to support (e.g. sort by "energy efficiency")
        $sort_orders = $this->getTemplateVarSortOrders(
            $result->getAvailableSortOrders(),
            $query->getSortOrder()->toString()
        );

        $sort_selected = false;
        if (!empty($sort_orders)) {
            foreach ($sort_orders as $order) {
                if (isset($order['current']) && true === $order['current']) {
                    $sort_selected = $order['label'];

                    break;
                }
            }
        }

        $searchVariables = [
            'result' => $result,
            'label' => $this->getListingLabel(),
            'products' => $products,
            'sort_orders' => $sort_orders,
            'sort_selected' => $sort_selected,
            'pagination' => $pagination,
            'rendered_facets' => $rendered_facets,
            'rendered_active_filters' => $rendered_active_filters,
            'js_enabled' => $this->ajax,
            'current_url' => $this->updateQueryString([
                'q' => $result->getEncodedFacets(),
            ]),
        ];

        Hook::exec('filterProductSearch', ['searchVariables' => &$searchVariables]);
        Hook::exec('actionProductSearchAfter', $searchVariables);

        return $searchVariables;
    }
}
