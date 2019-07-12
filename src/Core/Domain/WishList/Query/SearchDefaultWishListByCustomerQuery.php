<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\Module\WishList\Core\Domain\WishList\Query;

use PrestaShop\Module\WishList\Core\Domain\Customer\Exception\CustomerConstraintException;
use PrestaShop\Module\WishList\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\Module\WishList\Core\Domain\Shop\Exception\ShopConstraintException;
use PrestaShop\Module\WishList\Core\Domain\Shop\Group\Exception\ShopGroupConstraintException;
use PrestaShop\Module\WishList\Core\Domain\Shop\Group\ValueObject\ShopGroupId;
use PrestaShop\Module\WishList\Core\Domain\Shop\ValueObject\ShopId;

class SearchDefaultWishListByCustomerQuery
{
    /**
     * @var CustomerId
     */
    private $customerId;

    /**
     * @var ShopId
     */
    private $shopId;

    /**
     * @var ShopGroupId
     */
    private $shopGroupId;

    /**
     * @var bool
     */
    private $isDefault;

    /**
     * GetAllWishListByCustomerQuery constructor.
     *
     * @param int $customerId
     * @param int $shopId
     * @param int $shopGroupId
     * @param bool $isDefault
     *
     * @throws CustomerConstraintException
     * @throws ShopConstraintException
     * @throws ShopGroupConstraintException
     */
    public function __construct($customerId, $shopId, $shopGroupId, $isDefault)
    {
        $this->customerId = new CustomerId($customerId);
        $this->shopId = new ShopId($shopId);
        $this->shopGroupId = new ShopGroupId($shopGroupId);
        $this->isDefault = $isDefault;
    }

    /**
     * @return CustomerId
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @return ShopId
     */
    public function getShopId()
    {
        return $this->shopId;
    }

    /**
     * @return ShopGroupId
     */
    public function getShopGroupId()
    {
        return $this->shopGroupId;
    }

    /**
     * @return bool
     */
    public function isDefault()
    {
        return $this->isDefault;
    }
}
