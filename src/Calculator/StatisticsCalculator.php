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

namespace PrestaShop\Module\BlockWishList\Calculator;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Product\ProductPresenter;

class StatisticsCalculator
{
    const ARRAY_KEYS_STATS = [
        'allTime',
        'currentYear',
        'currentMonth',
        'currentDay',
    ];

    private $context;
    private $productAssembler;

    public function __construct(LegacyContext $context)
    {
        $this->context = $context->getContext();
        $this->context->customer = new \Customer(); // (╯°□°)╯︵ ┻━┻
        $this->productAssembler = new \ProductAssembler($this->context);
    }

    /**
     * computeStatsFor
     *
     * @param string|null $statsRange
     *
     * @return array
     */
    public function computeStatsFor($statsRange = null)
    {
        $query = new \DbQuery();
        $query->select('id_product');
        $query->select('id_product_attribute');
        $query->select('date_add');
        $query->select('id_statistics');
        $query->from('blockwishlist_statistics');

        if (null !== $statsRange) {
            switch ($statsRange) {
                case 'currentYear':
                    $dateStart = (new \DateTime('now'))->modify('-1 year')->format('Y-m-d H:i:s');
                break;
                case 'currentMonth':
                    $dateStart = (new \DateTime('now'))->modify('-1 month')->format('Y-m-d H:i:s');
                break;
                case 'currentDay':
                    $dateStart = (new \DateTime('now'))->modify('-1 day')->format('Y-m-d H:i:s');
                break;
                case 'allTime':
                    $dateStart = null;
                break;
                default:
                    $dateStart = null;
                break;
            }

            if (null !== $dateStart) {
                $query->where('date_add >= "' . $dateStart . '"');
            }
        }

        $results = \Db::getInstance()->executeS($query);
        $stats = [];

        foreach ($results as $result) {
            $productAttributeKey = $result['id_product'] . '.' . $result['id_product_attribute'];

            if (isset($stats[$productAttributeKey])) {
                $stats[$productAttributeKey] = $stats[$productAttributeKey] + 1;
            } else {
                $stats[$productAttributeKey] = 1;
            }
        }

        arsort($stats);
        $stats = array_slice($stats, 0, 10);
        $this->computeConversionRate($stats, $dateStart);

        return $stats;
    }

    /**
     * computeconversionRate
     *
     * @param array $stats by reference
     * @param string|null $statsKey
     *
     * @return void
     */
    public function computeConversionRate(&$stats, $dateStart = null)
    {
        $position = 0;
        foreach ($stats as $idProductAndAttribute => $count) {
            // first ID is product, second one is product_attribute
            $ids = explode('.', $idProductAndAttribute);
            $id_product = $ids[0];
            $id_product_attribute = $ids[1];
            $productDetails = $this->productAssembler->assembleProduct([
                'id_product' => $id_product,
                'id_product_attribute' => $id_product_attribute,
            ]);
            $imgDetails = $this->getProductImage($productDetails);
            $stats[$idProductAndAttribute] = [
                'position' => $position,
                'count' => $count,
                'id_product' => $id_product,
                'id_product_attribute' => $id_product_attribute,
                'name' => $productDetails['name'],
                'category_name' => $productDetails['category_name'],
                'image_small_url' => $imgDetails['small']['url'],
                'link' => $productDetails['link'],
                'reference' => $productDetails['reference'],
                'price' => $productDetails['price'],
                'quantity' => $productDetails['quantity'],
                'conversionRate' => $this->computeConversionByProduct($id_product, $id_product_attribute, $dateStart),
            ];
            $position++;
        }
    }

    /**
     * getProductImage
     *
     * @param array $productDetails
     *
     * @return array
     */
    public function getProductImage($productDetails)
    {
        $imgDetails = [];

        $presenterFactory = new \ProductPresenterFactory($this->context);
        $presentationSettings = $presenterFactory->getPresentationSettings();
        $presenter = new ProductPresenter(
            new ImageRetriever(
                $this->context->link
            ),
            $this->context->link,
            new PriceFormatter(),
            new ProductColorsRetriever(),
            $this->context->getTranslator()
        );

        $presentedProduct = $presenter->present(
            $presentationSettings,
            $productDetails,
            $this->context->language
        );

        foreach ($presentedProduct as $key => $value) {
            if ($key == 'embedded_attributes') {
                $imgDetails = $value['cover'];
            }
        }

        return $imgDetails;
    }

    /**
     * computeConversionByProduct
     *
     * @param string $id_product
     * @param string $id_product_attribute
     * @param string $dateStart (Y-m-d H:i:s)
     *
     * @return float
     */
    public function computeConversionByProduct($id_product, $id_product_attribute, $dateStart = null)
    {
        $queryCarts = new \DbQuery();
        $queryCarts->select('id_cart');
        $queryCarts->from('blockwishlist_statistics');
        $queryCarts->where('id_cart != 0');
        $queryCarts->where('id_product = ' . $id_product);
        $queryCarts->where('id_product_attribute = ' . $id_product_attribute);

        if (null != $dateStart) {
            $queryCarts->where('date_add >= "' . $dateStart . '"');
        }

        $carts = \Db::getInstance()->executeS($queryCarts);
        $nbCartPaidAndShipped = 0;

        foreach ($carts as $cart) {
            $queryOrder = new \DbQuery();
            $queryOrder->select('id_order');
            $queryOrder->from('orders');
            $queryOrder->where('id_cart = ' . $cart['id_cart']);
            $orderID = \Db::getInstance()->getRow($queryOrder);

            if (empty($orderID)) {
                continue;
            }

            $order = new \Order($orderID['id_order']);

            if ($order->hasBeenPaid() >= 1) {
                foreach ($order->getProducts() as $product) {
                    // if product/attribute combination is still in the order
                    if ($product['product_id'] == $id_product
                        && $product['product_attribute_id'] == $id_product_attribute
                    ) {
                        ++$nbCartPaidAndShipped;
                    }
                }
            }
        }

        $queryCountAll = new \DbQuery();
        $queryCountAll->select('COUNT(id_statistics)');
        $queryCountAll->from('blockwishlist_statistics');
        $queryCountAll->where('id_product = ' . $id_product);
        $queryCountAll->where('id_product_attribute = ' . $id_product_attribute);

        if (null != $dateStart) {
            $queryCountAll->where('date_add >= "' . $dateStart . '"');
        }

        $countAddedToWishlist = \Db::getInstance()->getValue($queryCountAll);

        return round(($nbCartPaidAndShipped / $countAddedToWishlist) * 100, 2);
    }
}
