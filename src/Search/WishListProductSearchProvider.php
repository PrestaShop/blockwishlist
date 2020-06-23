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
     * @param Db $db
     * @param WishList $wishList
     */
    public function __construct(Db $db, WishList $wishList)
    {
        $this->db = $db;
        $this->wishList = $wishList;
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
        // @todo Complete SQL Query
        $querySearch = new \DbQuery();
        $querySearch->select(); // @todo Set fields used to render Product, example \ManufacturerCore::getProducts()
        $querySearch->from();
        $querySearch->innerJoin();
        $querySearch->where('id_wishlist = ' . (int) $this->wishList->id);
        $querySearch->limit(); // @todo use ProductSearchQuery to get pagination...

        $products = $this->db->executeS($querySearch);

        if (empty($products)) {
            $products = [];
        }

        // @todo Complete SQL Query count
        $querySearch = new \DbQuery();
        $querySearch->select('COUNT(*)');
        $querySearch->from();
        $querySearch->innerJoin();
        $querySearch->where('id_wishlist = ' . (int) $this->wishList->id);
        $querySearch->where(); // @todo use ProductSearchContext to get Language identifier etc...
        // @todo No use pagination here, we want count all results

        $count = (int) $this->db->getValue($querySearch);

        $result = new ProductSearchResult();
        $result->setProducts($products);
        $result->setTotalProductsCount($count);

        return $result;
    }
}
