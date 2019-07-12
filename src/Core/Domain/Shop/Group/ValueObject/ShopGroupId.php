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

namespace PrestaShop\Module\WishList\Core\Domain\Shop\Group\ValueObject;

use PrestaShop\Module\WishList\Core\Domain\Shop\Group\Exception\ShopGroupConstraintException;

/**
 * ShopGroup identifier
 */
class ShopGroupId
{
    /**
     * @var int
     */
    private $shopGroupId;

    /**
     * @param int $shopGroupId
     *
     * @throws ShopGroupConstraintException
     */
    public function __construct($shopGroupId)
    {
        $this->assertIntegerIsGreaterThanZero($shopGroupId);

        $this->shopGroupId = $shopGroupId;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->shopGroupId;
    }

    /**
     * @param int $value
     *
     * @throws ShopGroupConstraintException
     */
    private function assertIntegerIsGreaterThanZero($value)
    {
        if (!is_int($value) || 0 >= $value) {
            throw new ShopGroupConstraintException(
                sprintf(
                    'ShopGroup id must be integer greater than zero, but %s given.',
                    var_export($value, true)
                ),
                ShopGroupConstraintException::INVALID_SHOP_GROUP_ID
            );
        }
    }
}
