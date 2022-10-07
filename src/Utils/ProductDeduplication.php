<?php

namespace PrestaShop\Module\BlockWishList\Utils;

use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductListingLazyArray;

class ProductDeduplication
{
    /**
     * @param ProductListingLazyArray[] $products $products
     *
     * @return ProductListingLazyArray[]
     */
    public static function deduplicateSameProducts(array $products)
    {
        $newProducts = [];
        foreach ($products as $product) {
            if (!in_array($product, $newProducts, false)) {
                $newProducts[] = $product;
            }
        }

        return $newProducts;
    }
}
