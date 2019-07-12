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

namespace PrestaShop\Module\WishList\Core\Domain\WishList\QueryResult;

use DateTime;
use Exception;
use PrestaShop\Module\WishList\Core\Domain\Customer\Exception\CustomerConstraintException;
use PrestaShop\Module\WishList\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\Module\WishList\Core\Domain\Shop\Exception\ShopConstraintException;
use PrestaShop\Module\WishList\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\Module\WishList\Core\Domain\Shop\Group\Exception\ShopGroupConstraintException;
use PrestaShop\Module\WishList\Core\Domain\Shop\Group\ValueObject\ShopGroupId;
use PrestaShop\Module\WishList\Core\Domain\WishList\Exception\WishListConstraintException;
use PrestaShop\Module\WishList\Core\Domain\WishList\ValueObject\WishListName;
use PrestaShop\Module\WishList\Core\Domain\WishList\ValueObject\WishListToken;

class SearchOneWishListByCustomerQueryResult
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
     * @var WishListName
     */
    private $name;

    /**
     * @var WishListToken
     */
    private $token;

    /**
     * @var bool
     */
    private $isDefault;

    /**
     * @var DateTime
     */
    private $dateAdd;

    /**
     * @var DateTime
     */
    private $dateUpd;

    /**
     * @param int $customerId
     * @param int $shopId
     * @param int $shopGroupId
     * @param string $name
     * @param string $token
     * @param bool $isDefault
     * @param string $dateAdd
     * @param string $dateUpd
     *
     * @throws CustomerConstraintException
     * @throws ShopConstraintException
     * @throws ShopGroupConstraintException
     * @throws WishListConstraintException
     * @throws Exception
     */
    public function __construct(
        $customerId,
        $shopId,
        $shopGroupId,
        $name,
        $token,
        $isDefault,
        $dateAdd,
        $dateUpd
    ) {
        $this->customerId = new CustomerId($customerId);
        $this->shopId = new ShopId($shopId);
        $this->shopGroupId = new ShopGroupId($shopGroupId);
        $this->name = new WishListName($name);
        $this->token = new WishListToken($token);
        $this->isDefault = $isDefault;
        $this->dateAdd = new DateTime($dateAdd);
        $this->dateUpd = new DateTime($dateUpd);
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
     * @return WishListName
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isDefault()
    {
        return $this->isDefault;
    }

    /**
     * @return WishListToken
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return DateTime
     */
    public function getDateAdd()
    {
        return $this->dateAdd;
    }

    /**
     * @return DateTime
     */
    public function getDateUpd()
    {
        return $this->dateUpd;
    }
}
