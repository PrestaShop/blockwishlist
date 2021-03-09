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

use PrestaShop\Module\BlockWishList\Access\CustomerAccess;
use PrestaShop\Module\BlockWishList\Search\WishListProductSearchProvider;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrderFactory;

/**
 * View the content of a personal wishlist
 */
class BlockWishlistViewModuleFrontController extends ProductListingFrontController
{
    /**
     * Made public as the core considers this class as an ModuleFrontController
     * and other modules expects to find the $module property.
     *
     * @var BlockWishList
     */
    public $module;

    /**
     * @var WishList
     */
    protected $wishlist;

    /**
     * @var CustomerAccess
     */
    private $customerAccess;

    public function __construct()
    {
        /** @var BlockWishList $module */
        $module = Module::getInstanceByName('blockwishlist');
        $this->module = $module;

        if (empty($this->module->active)) {
            Tools::redirect('index');
        }

        parent::__construct();

        $this->controller_type = 'modulefront';
        $this->customerAccess = new CustomerAccess($this->context->customer);
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $id_wishlist = $this->getWishlistId();
        $this->wishlist = new WishList($id_wishlist);

        if (false === Validate::isLoadedObject($this->wishlist)) {
            Tools::redirect('index.php?controller=404');
        }

        parent::init();

        if (false === $this->customerAccess->hasReadAccessToWishlist($this->wishlist)) {
            header('HTTP/1.1 403 Forbidden');
            header('Status: 403 Forbidden');
            $this->errors[] = $this->trans(
                'You do not have access to this wishlist.',
                [],
                'Modules.Blockwishlist.Shop'
            );
            $this->setTemplate('errors/forbidden');

            return;
        }

        $this->context->smarty->assign(
            [
                'id' => $id_wishlist,
                'wishlistName' => $this->wishlist->name,
                'isGuest' => !$this->customerAccess->hasWriteAccessToWishlist($this->wishlist),
                'url' => Context::getContext()->link->getModuleLink('blockwishlist', 'view', $this->getAccessParams()),
                'wishlistsLink' => Context::getContext()->link->getModuleLink('blockwishlist', 'lists'),
                'deleteProductUrl' => Context::getContext()->link->getModuleLink('blockwishlist', 'action', ['action' => 'deleteProductFromWishlist']),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function initContent()
    {
        parent::initContent();

        if (false === $this->customerAccess->hasReadAccessToWishlist($this->wishlist)) {
            return;
        }

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
            'Modules.Blockwishlist.Shop'
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
            new SortOrderFactory($this->getTranslator()),
            $this->getTranslator()
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getAjaxProductSearchVariables()
    {
        parent::getAjaxProductSearchVariables();
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
            $query->setSortOrder(SortOrder::newFromString($encodedSortOrder));
        }

        // get the parameters containing the encoded facets from the URL
        $encodedFacets = Tools::getValue('q');
        $query->setEncodedFacets($encodedFacets);
        $result = $provider->runQuery($context, $query);

        if (!$result->getCurrentSortOrder()) {
            $result->setCurrentSortOrder($query->getSortOrder());
        }

        // prepare the products
        $products = $this->prepareMultipleProductsForTemplate($result->getProducts());

        // render the facets with the core
        $rendered_facets = $this->renderFacets($result);
        $rendered_active_filters = $this->renderActiveFilters($result);
        $pagination = $this->getTemplateVarPagination($query, $result);

        // prepare the sort orders
        // note that, again, the product controller is sort-orders agnostic
        // a module can easily add specific sort orders that it needs to support (e.g. sort by "energy efficiency")
        $sort_orders = $this->getTemplateVarSortOrders(
            $result->getAvailableSortOrders(),
            $query->getSortOrder()->toString()
        );

        $sort_selected = false;
        $labelDefaultSort = '';
        if (!empty($sort_orders)) {
            foreach ($sort_orders as $order) {
                if ($order['field'] == 'id_wishlist_product') {
                    $labelDefaultSort = $order['label'];
                }
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
            'sort_selected' => $sort_selected ? $sort_selected : $labelDefaultSort,
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

    /**
     * Depending on the parameters sent, checks if the current visitor may reach the page
     *
     * @return int|false
     */
    private function getWishlistId()
    {
        if (Tools::getIsset('id_wishlist')) {
            return (int) Tools::getValue('id_wishlist');
        }

        if (Tools::getIsset('token')) {
            $wishlistData = WishList::getByToken(
                Tools::getValue('token')
            );

            if (!empty($wishlistData['id_wishlist'])) {
                return $wishlistData['id_wishlist'];
            }
        }

        return false;
    }

    /**
     * @return array
     */
    private function getAccessParams()
    {
        if (Tools::getIsset('token')) {
            return ['token' => Tools::getValue('token')];
        }
        if (Tools::getIsset('id_wishlist')) {
            return ['id_wishlist' => Tools::getValue('id_wishlist')];
        }

        return [];
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = $this->addMyAccountToBreadcrumb();
        $breadcrumb['links'][] = [
            'title' => Configuration::get('blockwishlist_WishlistPageName', $this->context->language->id),
            'url' => $this->context->link->getModuleLink('blockwishlist', 'lists'),
        ];
        $breadcrumb['links'][] = [
            'title' => $this->wishlist->name,
            'url' => Context::getContext()->link->getModuleLink('blockwishlist', 'view', $this->getAccessParams()),
        ];

        return $breadcrumb;
    }
}
