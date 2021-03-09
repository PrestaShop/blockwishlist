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

namespace PrestaShop\Module\BlockWishList\Search;

use Combination;
use Configuration;
use Db;
use DbQuery;
use FrontController;
use Group;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchProviderInterface;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchResult;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrderFactory;
use Product;
use Shop;
use Symfony\Component\Translation\TranslatorInterface;
use WishList;

/**
 * Responsible of getting products for specific wishlist.
 */
class WishListProductSearchProvider implements ProductSearchProviderInterface
{
    /**
     * @var Db
     */
    private $db;

    /**
     * @var WishList
     */
    private $wishList;

    /**
     * @var SortOrderFactory
     */
    private $sortOrderFactory;

    /**
     * @var TranslatorInterface the translator
     */
    private $translator;

    /**
     * @param Db $db
     * @param WishList $wishList
     */
    public function __construct(
        Db $db,
        WishList $wishList,
        SortOrderFactory $sortOrderFactory,
        TranslatorInterface $translator
    ) {
        $this->db = $db;
        $this->wishList = $wishList;
        $this->sortOrderFactory = $sortOrderFactory;
        $this->translator = $translator;
    }

    /**
     * @param ProductSearchContext $context
     * @param ProductSearchQuery $query
     *
     * @return ProductSearchResult
     */
    public function runQuery(
        ProductSearchContext $context,
        ProductSearchQuery $query
    ) {
        $result = new ProductSearchResult();
        $result->setProducts($this->getProductsOrCount($context, $query, 'products'));
        $result->setTotalProductsCount($this->getProductsOrCount($context, $query, 'count'));
        $sortOrders = $this->sortOrderFactory->getDefaultSortOrders();
        $sortOrders[] = (new SortOrder('wishlist_product', 'id_wishlist_product', 'DESC'))->setLabel($this->translator->trans('Last added', [], 'Modules.Blockwishlist.Shop'));
        $result->setAvailableSortOrders($sortOrders);

        return $result;
    }

    /**
     * @param ProductSearchContext $context
     * @param ProductSearchQuery $query
     * @param string $type
     *
     * @return array|int
     */
    private function getProductsOrCount(
        ProductSearchContext $context,
        ProductSearchQuery $query,
        $type = 'products'
    ) {
        $querySearch = new DbQuery();

        if ('products' === $type) {
            $querySearch->select('p.*');
            $querySearch->select('wp.quantity AS wishlist_quantity');
            $querySearch->select('product_shop.*');
            $querySearch->select('stock.out_of_stock, IFNULL(stock.quantity, 0) AS quantity');
            $querySearch->select('pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`,
            pl.`meta_title`, pl.`name`, pl.`available_now`, pl.`available_later`');
            $querySearch->select('image_shop.`id_image` AS id_image');
            $querySearch->select('il.`legend`');
            $querySearch->select('
            DATEDIFF(
                product_shop.`date_add`,
                DATE_SUB(
                    "' . date('Y-m-d') . ' 00:00:00",
                    INTERVAL ' . (0 <= (int) Configuration::get('PS_NB_DAYS_NEW_PRODUCT') ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20) . ' DAY
                )
            ) > 0 AS new'
            );

            if (Combination::isFeatureActive()) {
                $querySearch->select('product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity, IFNULL(product_attribute_shop.`id_product_attribute`,0) AS id_product_attribute');
            }
        } else {
            $querySearch->select('COUNT(wp.id_product)');
        }

        $querySearch->from('product', 'p');
        $querySearch->join(Shop::addSqlAssociation('product', 'p'));
        $querySearch->innerJoin('wishlist_product', 'wp', 'wp.`id_product` = p.`id_product`');
        $querySearch->leftJoin('category_product', 'cp', 'p.id_product = cp.id_product AND cp.id_category = product_shop.id_category_default');

        if (Combination::isFeatureActive()) {
            $querySearch->leftJoin('product_attribute_shop', 'product_attribute_shop', 'p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`id_product_attribute` = wp.id_product_attribute AND product_attribute_shop.id_shop=' . (int) $context->getIdShop());
        }

        if ('products' === $type) {
            $querySearch->leftJoin('stock_available', 'stock', 'stock.id_product = `p`.id_product AND stock.id_product_attribute = wp.id_product_attribute' . \StockAvailable::addSqlShopRestriction(null, (int) $context->getIdShop(), 'stock'));
            $querySearch->leftJoin('product_lang', 'pl', 'p.`id_product` = pl.`id_product` AND pl.`id_lang` = ' . (int) $context->getIdLang() . \Shop::addSqlRestrictionOnLang('pl'));
            $querySearch->leftJoin('image_shop', 'image_shop', 'image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop = ' . (int) $context->getIdShop());
            $querySearch->leftJoin('image_lang', 'il', 'image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) $context->getIdLang());
            $querySearch->leftJoin('category', 'ca', 'cp.`id_category` = ca.`id_category` AND ca.`active` = 1');
        }

        if (Group::isFeatureActive()) {
            $groups = FrontController::getCurrentCustomerGroups();
            $sqlGroups = false === empty($groups) ? 'IN (' . implode(',', $groups) . ')' : '=' . (int) Group::getCurrent()->id;
            $querySearch->leftJoin('category_group', 'cg', 'cp.`id_category` = cg.`id_category` AND cg.`id_group`' . $sqlGroups);
        }

        $querySearch->where('wp.id_wishlist = ' . (int) $this->wishList->id);
        $querySearch->where('product_shop.active = 1');
        $querySearch->where('product_shop.visibility IN ("both", "catalog")');

        if ('products' === $type) {
            $sortOrder = $query->getSortOrder()->toLegacyOrderBy(true);
            $querySearch->orderBy($sortOrder . ' ' . $query->getSortOrder()->toLegacyOrderWay());
            $querySearch->limit(((int) $query->getPage() - 1) * (int) $query->getResultsPerPage(), (int) $query->getResultsPerPage());
            $products = $this->db->executeS($querySearch);

            if (empty($products)) {
                return [];
            }

            return Product::getProductsProperties((int) $context->getIdLang(), $products);
        }

        return (int) $this->db->getValue($querySearch);
    }
}
