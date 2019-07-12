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

namespace Tests\PrestaShop\Module\WishList\Core\Domain\WishList\Query;

use Generator;
use PHPUnit\Framework\TestCase;
use PrestaShop\Module\WishList\Core\Domain\Customer\Exception\CustomerConstraintException;
use PrestaShop\Module\WishList\Core\Domain\Shop\Exception\ShopConstraintException;
use PrestaShop\Module\WishList\Core\Domain\Shop\Group\Exception\ShopGroupConstraintException;
use PrestaShop\Module\WishList\Core\Domain\WishList\Query\SearchDefaultWishListByCustomerQuery;
use PrestaShop\Module\WishList\Core\Exception\CoreException;

class SearchDefaultWishListByCustomerQueryTest extends TestCase
{
    /**
     * @dataProvider getValidValue
     *
     * @param array $validValue
     *
     * @throws CustomerConstraintException
     * @throws ShopConstraintException
     * @throws ShopGroupConstraintException
     */
    public function testItIsCreatedSuccessfullyWhenValidValueIsGiven($validValue)
    {
        $this->assertInstanceOf(
            SearchDefaultWishListByCustomerQuery::class,
            $this->createQueryFromArray($validValue)
        );
    }

    /**
     * @dataProvider getInvalidValue
     *
     * @param array $invalidValue
     *
     * @throws CustomerConstraintException
     * @throws ShopConstraintException
     * @throws ShopGroupConstraintException
     */
    public function testItThrowsExceptionWhenInvalidValueIsGiven($invalidValue)
    {
        $this->expectException(CoreException::class);

        $this->createQueryFromArray($invalidValue);
    }

    /**
     * @return Generator
     */
    public function getValidValue()
    {
        yield [[
            'customerId' => 1,
            'shopId' => 1,
            'shopGroupId' => 1,
            'isDefault' => true,
        ]];
        yield [[
            'customerId' => rand(1, 9999),
            'shopId' => rand(1, 9999),
            'shopGroupId' => rand(1, 9999),
            'isDefault' => false,
        ]];
    }

    /**
     * @return Generator
     */
    public function getInvalidValue()
    {
        yield [[
            'customerId' => null,
            'shopId' => null,
            'shopGroupId' => null,
            'isDefault' => null,
        ]];
    }

    /**
     * @param array $data
     *
     * @return SearchDefaultWishListByCustomerQuery
     *
     * @throws CustomerConstraintException
     * @throws ShopConstraintException
     * @throws ShopGroupConstraintException
     */
    private function createQueryFromArray(array $data)
    {
        return new SearchDefaultWishListByCustomerQuery(
            $data['customerId'],
            $data['shopId'],
            $data['shopGroupId'],
            $data['isDefault']
        );
    }
}
