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

use Db;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchProviderInterface;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchResult;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrderFactory;
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
     * @var contexts
     */
    private $contexts;

    /**
     * @var SortOrderFactory
     */
    private $sortOrderFactory;

    /**
     * @param Db $db
     * @param WishList $wishList
     */
    public function __construct(Db $db, WishList $wishList, $contexts)
    {
        $this->db = $db;
        $this->wishList = $wishList;
        $this->context = $contexts;
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
        $querySearch = new \DbQuery();

        if ('products' === $type) {
            $querySearch->select('p.*');
            $querySearch->select('wp.`quantity` AS wishlist_quantity');
            $querySearch->select('wp.`id_product_attribute`');
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
                    INTERVAL ' . (0 <= (int) \Configuration::get('PS_NB_DAYS_NEW_PRODUCT') ? \Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20) . ' DAY
                )
            ) > 0 AS new'
            );
            if (\Combination::isFeatureActive()) {
                $querySearch->select('product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity, IFNULL(product_attribute_shop.`id_product_attribute`,0) AS id_product_attribute');
            }
        } else {
            $querySearch->select('COUNT(wp.id_product)');
        }

        $querySearch->from('product', 'p');
        $querySearch->join(\Shop::addSqlAssociation('product', 'p'));
        $querySearch->innerJoin('wishlist_product', 'wp', 'wp.`id_product` = p.`id_product`');
        $querySearch->leftJoin('category_product', 'cp', 'p.id_product = cp.id_product');

        if (\Combination::isFeatureActive()) {
            $querySearch->leftJoin('product_attribute_shop', 'product_attribute_shop', 'p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop=' . (int) $context->getIdShop());
        }

        if ('products' === $type) {
            $querySearch->join(\Product::sqlStock('p', 0));
            $querySearch->leftJoin('product_lang', 'pl', 'p.`id_product` = pl.`id_product` AND pl.`id_lang` = ' . (int) $context->getIdLang() . \Shop::addSqlRestrictionOnLang('pl'));
            $querySearch->leftJoin('image_shop', 'image_shop', 'image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop = ' . (int) $context->getIdShop());
            $querySearch->leftJoin('image_lang', 'il', 'image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) $context->getIdLang());
            $querySearch->leftJoin('category', 'ca', 'cp.`id_category` = ca.`id_category` AND ca.`active` = 1');
        }

        if (\Group::isFeatureActive()) {
            $groups = \FrontController::getCurrentCustomerGroups();
            $sqlGroups = false === empty($groups) ? 'IN (' . implode(',', $groups) . ')' : '=' . (int) \Group::getCurrent()->id;
            $querySearch->leftJoin('category_group', 'cg', 'cp.`id_category` = cg.`id_category` AND cg.`id_group`' . $sqlGroups);
        }

        $querySearch->where('wp.id_wishlist = ' . (int) $this->wishList->id);
        $querySearch->where('product_shop.active = 1');
        $querySearch->where('product_shop.visibility IN ("both", "catalog")');
        $querySearch->groupBy('p.id_product');

        if ('products' === $type) {
            $querySearch->orderBy($query->getSortOrder()->toLegacyOrderBy(true) . ' ' . $query->getSortOrder()->toLegacyOrderWay());
            $querySearch->limit(((int) $query->getPage() - 1) * (int) $query->getResultsPerPage(), (int) $query->getResultsPerPage());

            $products = $this->db->executeS($querySearch);

            if (empty($products)) {
                return [];
            }

            $products_for_template = [];

            if (is_array($products)) {
                foreach ($products as $rawProduct) {
                    $rawProduct['show_availability'] = $rawProduct['show_price'] && \Configuration::get('PS_STOCK_MANAGEMENT');

                    if ($rawProduct['show_availability']) {
                        $availableQuantity = $rawProduct['quantity'] - $rawProduct['wishlist_quantity'];
                        if (isset($rawProduct['stock_quantity'])) {
                            $availableQuantity = $rawProduct['stock_quantity'] - $rawProduct['wishlist_quantity'];
                        }
                        if ($availableQuantity >= 0) {
                            $rawProduct['availability_date'] = $rawProduct['available_date'];

                            if ($rawProduct['quantity'] < \Configuration::get('PS_LAST_QTIES')) {
                                //$this->applyLastItemsInStockDisplayRule();
                            } else {
                                $rawProduct['availability_message'] = $rawProduct['available_now'] ? $rawProduct['available_now']
                                    : \Configuration::get('PS_LABEL_IN_STOCK_PRODUCTS', $this->context->language->id);
                                $rawProduct['availability'] = 'available';
                            }
                        } elseif ($rawProduct['allow_oosp']) {
                            $rawProduct['availability_message'] = $rawProduct['available_later'] ? $rawProduct['available_later']
                                : \Configuration::get('PS_LABEL_OOS_PRODUCTS_BOA', $this->context->language->id);
                            $rawProduct['availability_date'] = $rawProduct['available_date'];
                            $rawProduct['availability'] = 'available';
                        } elseif ($rawProduct['wishlist_quantity'] > 0 && $rawProduct['quantity'] > 0) {
                            // $rawProduct['availability_message'] = $this->translator->trans(
                            //     'There are not enough products in stock',
                            //     [],
                            //     'Shop.Notifications.Error'
                            // );
                            $rawProduct['availability'] = 'unavailable';
                            $rawProduct['availability_date'] = null;
                        } elseif (!empty($rawProduct['quantity_all_versions']) && $rawProduct['quantity_all_versions'] > 0) {
                            $rawProduct['availability_message'] = $this->translator->trans(
                                'Product available with different options',
                                [],
                                'Shop.Theme.Catalog'
                            );
                            $rawProduct['availability_date'] = $rawProduct['available_date'];
                            $rawProduct['availability'] = 'unavailable';
                        } else {
                            $rawProduct['availability_message'] =
                                \Configuration::get('PS_LABEL_OOS_PRODUCTS_BOD', $this->context->language->id);
                            $rawProduct['availability_date'] = $rawProduct['available_date'];
                            $rawProduct['availability'] = 'unavailable';
                        }
                        $rawProduct['customization_required'] = false;
                    } else {
                        $rawProduct['availability_message'] = null;
                        $rawProduct['availability_date'] = null;
                        $rawProduct['availability'] = null;
                        $rawProduct['customization_required'] = false;
                    }
                    $products_for_template[] = $rawProduct;
                }
            }

            return $products_for_template;
        }

        return (int) $this->db->getValue($querySearch);
    }
}
