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

namespace PrestaShop\Module\WishList\Tests\Unit\Core\Domain\WishList\Command;

use Generator;
use PHPUnit\Framework\TestCase;
use PrestaShop\Module\WishList\Core\Domain\Customer\Exception\CustomerConstraintException;
use PrestaShop\Module\WishList\Core\Domain\Shop\Exception\ShopConstraintException;
use PrestaShop\Module\WishList\Core\Domain\Shop\Group\Exception\ShopGroupConstraintException;
use PrestaShop\Module\WishList\Core\Domain\WishList\Command\CreateEmptyWishListCommand;
use PrestaShop\Module\WishList\Core\Domain\WishList\Exception\WishListConstraintException;
use PrestaShop\Module\WishList\Core\Exception\CoreException;

class CreateEmptyWishListCommandTest extends TestCase
{
    /**
     * @dataProvider getValidValue
     *
     * @param array $validValue
     *
     * @throws CustomerConstraintException
     * @throws ShopConstraintException
     * @throws ShopGroupConstraintException
     * @throws WishListConstraintException
     */
    public function testItIsCreatedSuccessfullyWhenValidValueIsGiven($validValue)
    {
        $this->assertInstanceOf(
            CreateEmptyWishListCommand::class,
            $this->createCommandFromArray($validValue)
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
     * @throws WishListConstraintException
     */
    public function testItThrowsExceptionWhenInvalidValueIsGiven($invalidValue)
    {
        $this->expectException(CoreException::class);

        $this->createCommandFromArray($invalidValue);
    }

    /**
     * @return Generator
     */
    public function getValidValue()
    {
        yield [[
            'customerId' => rand(1, 9999),
            'shopId' => rand(1, 9999),
            'shopGroupId' => rand(1, 9999),
            'name' => 'My wishlist',
            'token' => '896A620603AF0A2D',
            'isDefault' => true,
        ]];
        yield [[
            'customerId' => rand(1, 9999),
            'shopId' => rand(1, 9999),
            'shopGroupId' => rand(1, 9999),
            'name' => 'Ma liste d‘envies',
            'token' => 'D7DEB10BA8762014',
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
            'name' => null,
            'token' => null,
            'isDefault' => null,
        ]];
    }

    /**
     * @param array $data
     *
     * @return CreateEmptyWishListCommand
     *
     * @throws CustomerConstraintException
     * @throws ShopConstraintException
     * @throws ShopGroupConstraintException
     * @throws WishListConstraintException
     */
    private function createCommandFromArray(array $data)
    {
        return new CreateEmptyWishListCommand(
            $data['customerId'],
            $data['shopId'],
            $data['shopGroupId'],
            $data['name'],
            $data['token'],
            $data['isDefault']
        );
    }
}
