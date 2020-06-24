<?php

namespace PrestaShop\Module\BlockWishList\Calculator;

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
        'currentDay'
    ];

    private $context;

    public function __construct($context)
    {
        $this->context = $context;
        $this->context->customer = new \Customer(); // (╯°□°)╯︵ ┻━┻
        $this->productAssembler = new \ProductAssembler($this->context);
    }

    public function computeAllStats()
    {
        $query = new \DbQuery();
        $query->select('id_product');
        $query->select('id_product_attribute');
        $query->select('date_add');
        $query->select('id_statistics');
        $query->from('blockwishlist_statistics');

        $results = \Db::getInstance()->executeS($query);

        $stats = [
            'allTime' => [],
            'currentYear' => [],
            'currentMonth' => [],
            'currentDay' => [],
        ];

        $currentDate = new \DateTime('now');

        foreach ($results as $result) {
            $productAttributeKey = $result['id_product'].'.'.$result['id_product_attribute'];

            if (isset($stats['allTime'][$productAttributeKey])) {
                $stats['allTime'][$productAttributeKey] = $stats['allTime'][$productAttributeKey] + 1;
            } else {
                $stats['allTime'][$productAttributeKey] = 1;
            }

            $dateTime = \DateTime::createFromFormat('Y-m-d H:i:s', $result['date_add']);
            $diff = $dateTime->diff($currentDate);

            if ($diff->y < 1) {
                if (isset($stats['currentYear'][$productAttributeKey])) {
                    $stats['currentYear'][$productAttributeKey] = $stats['currentYear'][$productAttributeKey] + 1;
                } else {
                    $stats['currentYear'][$productAttributeKey] = 1;
                }
            }

            if ($diff->m < 1) {
                if (isset($stats['currentMonth'][$productAttributeKey])) {
                    $stats['currentMonth'][$productAttributeKey] = $stats['currentMonth'][$productAttributeKey] + 1;
                } else {
                    $stats['currentMonth'][$productAttributeKey] = 1;
                }
            }

            if ($diff->d < 1) {
                if (isset($stats['currentDay'][$productAttributeKey])) {
                    $stats['currentDay'][$productAttributeKey] = $stats['currentDay'][$productAttributeKey] + 1;
                } else {
                    $stats['currentDay'][$productAttributeKey] = 1;
                }
            }
        }

        arsort($stats['allTime']);
        arsort($stats['currentYear']);
        arsort($stats['currentMonth']);
        arsort($stats['currentDay']);
        $stats['allTime'] = array_slice($stats['allTime'], 0, 10);
        $stats['currentYear'] = array_slice($stats['currentYear'], 0, 10);
        $stats['currentMonth'] = array_slice($stats['currentMonth'], 0, 10);
        $stats['currentDay'] = array_slice($stats['currentDay'], 0, 10);

        $this->computeAllConversionRate($stats);

        return $stats;
    }

    public function computeAllConversionRate(&$stats)
    {
        // add option to launch only one of theses
        foreach (self::ARRAY_KEYS_STATS as $statsKey) {
            switch ($statsKey) {
                case 'currentYear':
                    $dateStart = (new \DateTime('now'))->modify('-1 year')->format('Y-m-d H:i:s');
                break;
                case 'currentMonth':
                    $dateStart = (new \DateTime('now'))->modify('-1 month')->format('Y-m-d H:i:s');
                break;
                case 'currentDay':
                    $dateStart = (new \DateTime('now'))->modify('-1 day')->format('Y-m-d H:i:s');
                break;
                default:
                    $dateStart = null;
                break;
            }

            foreach ($stats[$statsKey] as $idProductAndAttribute => $count) {
                // first ID is product, second one is product_attribute
                $ids = explode('.', $idProductAndAttribute);
                $id_product = $ids[0];
                $id_product_attribute = $ids[1];
                $productDetails = $this->productAssembler->assembleProduct([
                    'id_product' => $id_product,
                    'id_product_attribute' => $id_product_attribute
                ]);
                $imgDetails = $this->getProductImage($productDetails);

                $stats[$statsKey][$idProductAndAttribute] = [
                    'count' => $count,
                    'id_product' => $id_product,
                    'id_product_attribute' => $id_product_attribute,
                    'name' => $productDetails['name'],
                    'category_name' => $productDetails['category_name'],
                    'image' => $imgDetails,
                    'link' => $productDetails['link'],
                    'reference' => $productDetails['reference'],
                    'price' => $productDetails['price'],
                    'quantity' => $productDetails['quantity'],
                    'conversionRate' => $this->computeConversionByProduct($id_product, $id_product_attribute, $dateStart),
                ];
            }
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
            $queryCarts->where('date_add >= "' . $dateStart. '"');
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
                // product is still in ?
                foreach ($order->getProducts() as $product) {
                    if ($product['product_id'] == $id_product
                        && $product['product_attribute_id'] == $id_product_attribute
                    ) {
                        $nbCartPaidAndShipped++;
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
            $queryCountAll->where('date_add >= "' . $dateStart. '"');
        }

        $countAddedToWishlist = \Db::getInstance()->getValue($queryCountAll);

        return round(($nbCartPaidAndShipped / $countAddedToWishlist) * 100, 2);
    }
}
