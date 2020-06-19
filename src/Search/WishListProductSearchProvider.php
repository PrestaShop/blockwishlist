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
     * @var WishList
     */
    private $wishList;

    /**
     * @param WishList $wishList
     */
    public function __construct(WishList $wishList)
    {
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
        $products = []; // @todo Implement Search
        $count = 0; // @todo Implement Search
        $result = new ProductSearchResult();
        $result->setProducts($products);
        $result->setTotalProductsCount($count);

        return $result;
    }
}
