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

use Customer;
use DateTime;
use Db;
use DbQuery;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductPresenter;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Localization\Locale;
use ProductAssembler;
use ProductPresenterFactory;

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

    /**
     * @var Locale
     */
    private $locale;

    public function __construct(LegacyContext $context, Locale $locale)
    {
        $this->context = $context->getContext();
        $this->context->customer = new Customer();
        $this->productAssembler = new ProductAssembler($this->context);
        $this->locale = $locale;
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
        $query = new DbQuery();
        $query->select('id_product');
        $query->select('id_product_attribute');
        $query->select('date_add');
        $query->select('id_statistics');
        $query->from('blockwishlist_statistics');
        $query->where('id_shop = "' . (int) $this->context->shop->id . '"');

        switch ($statsRange) {
            case 'currentYear':
                $dateStart = (new DateTime('now'))->modify('-1 year')->format('Y-m-d H:i:s');
            break;
            case 'currentMonth':
                $dateStart = (new DateTime('now'))->modify('-1 month')->format('Y-m-d H:i:s');
            break;
            case 'currentDay':
                $dateStart = (new DateTime('now'))->modify('-1 day')->format('Y-m-d H:i:s');
            break;
            case 'allTime':
            default:
                $dateStart = null;
            break;
        }

        if (null !== $dateStart) {
            $query->where('date_add >= "' . $dateStart . '"');
        }

        $results = Db::getInstance()->executeS($query);
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
     * @param array $stats
     * @param string|null $dateStart
     *
     * @return void
     */
    public function computeConversionRate(&$stats, $dateStart = null)
    {
        $position = 0;

        foreach ($stats as $idProductAndAttribute => $count) {
            // first ID is product, second one is product_attribute
            $combination = '';
            $ids = explode('.', $idProductAndAttribute);
            $id_product = $ids[0];
            $id_product_attribute = $ids[1];
            $productDetails = $this->productAssembler->assembleProduct([
                'id_product' => $id_product,
                'id_product_attribute' => $id_product_attribute,
            ]);

            if (!empty($productDetails['attributes'])) {
                $combinationArr = [];
                foreach ($productDetails['attributes'] as $attribute) {
                    $combinationArr[] = $attribute['group'] . ' : ' . $attribute['name'];
                }
                $combination = implode(',', $combinationArr);
            }

            $imgDetails = $this->getProductImage($productDetails);
            $stats[$idProductAndAttribute] = [
                'position' => $position,
                'count' => $count,
                'id_product' => $id_product,
                'id_product_attribute' => $id_product_attribute,
                'name' => $productDetails['name'],
                'combination' => $combination,
                'category_name' => $productDetails['category_name'],
                'image_small_url' => $imgDetails['small']['url'],
                'link' => $productDetails['link'],
                'reference' => $productDetails['reference'],
                'price' => $this->locale->formatPrice($productDetails['price'], $this->context->currency->iso_code),
                'quantity' => $productDetails['quantity'],
                'conversionRate' => $this->computeConversionByProduct($id_product, $id_product_attribute, $dateStart) . '%',
            ];

            ++$position;
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

        $presenterFactory = new ProductPresenterFactory($this->context);
        $presentationSettings = $presenterFactory->getPresentationSettings();
        $imageRetriever = new ImageRetriever($this->context->link);
        $presenter = new ProductPresenter(
            $imageRetriever,
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
        if (!$imgDetails) {
            $imgDetails = $imageRetriever->getNoPictureImage($this->context->language);
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
        $nbOrderPaidAndShipped = [];
        $queryOrders = '
            SELECT count(distinct(o.id_order)) as nb
            FROM ' . _DB_PREFIX_ . 'orders o
            INNER JOIN ' . _DB_PREFIX_ . 'blockwishlist_statistics bws ON (o.id_cart = bws.id_cart )
            LEFT JOIN ' . _DB_PREFIX_ . 'order_history oh ON (o.`id_order` = oh.`id_order`)
            LEFT JOIN ' . _DB_PREFIX_ . 'order_state os ON (os.`id_order_state` = oh.`id_order_state` AND os.`paid` = 1 AND os.`shipped` = 1)
            LEFT JOIN ' . _DB_PREFIX_ . 'order_detail od ON (od.`id_order` = o.`id_order` AND od.`product_id` = bws.`id_product` AND od.`product_attribute_id` = bws.`id_product_attribute`)
            WHERE bws.`id_cart` <> 0 AND bws.`id_product` = ' . (int) $id_product . ' AND bws.`id_product_attribute` = ' . (int) $id_product_attribute . '
            AND bws.`id_shop` = ' . (int) $this->context->shop->id . '
            ';

        if (null != $dateStart) {
            $queryOrders .= 'AND bws.date_add >= "' . $dateStart . '"';
        }

        $nbOrderPaidAndShipped = Db::getInstance()->getRow($queryOrders);

        if (empty($nbOrderPaidAndShipped['nb'])) {
            return 0;
        }

        $queryCountAll = new DbQuery();
        $queryCountAll->select('COUNT(id_statistics)');
        $queryCountAll->from('blockwishlist_statistics');
        $queryCountAll->where('id_product = ' . $id_product);
        $queryCountAll->where('id_product_attribute = ' . $id_product_attribute);
        $queryCountAll->where('id_shop = ' . (int) $this->context->shop->id);

        if (null != $dateStart) {
            $queryCountAll->where('date_add >= "' . $dateStart . '"');
        }

        $countAddedToWishlist = Db::getInstance()->getValue($queryCountAll);

        if (0 != $countAddedToWishlist) {
            return round(($nbOrderPaidAndShipped['nb'] / $countAddedToWishlist) * 100, 2);
        }

        return 0;
    }
}
