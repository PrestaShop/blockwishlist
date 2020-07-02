<?php

namespace PrestaShop\Module\BlockWishList\Presenter;

use PrestaShop\PrestaShop\Adapter\Presenter\PresenterInterface;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Product\ProductPresenter;

class StatisticsPresenter implements PresenterInterface
{
    private $context;
    private $productAssembler;

    public function __construct($context)
    {
        $this->context = $context;
        $this->context->customer = new \Customer(); // (╯°□°)╯︵ ┻━┻
        $this->productAssembler = new \ProductAssembler($this->context);
    }

    /**
     * @param mixed $object
     *
     * @return array|AbstractLazyArray
     */
    public function present($statArray)
    {

    }
}
