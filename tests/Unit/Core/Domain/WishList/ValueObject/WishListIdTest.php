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

namespace PrestaShop\Module\WishList\Tests\Unit\Core\Domain\WishList\ValueObject;

use Generator;
use PHPUnit\Framework\TestCase;
use PrestaShop\Module\WishList\Core\Domain\WishList\Exception\WishListConstraintException;
use PrestaShop\Module\WishList\Core\Domain\WishList\ValueObject\WishListId;

class WishListIdTest extends TestCase
{
    /**
     * @dataProvider getValidValue
     *
     * @param string $validValue
     *
     * @throws WishListConstraintException
     */
    public function testItIsCreatedSuccessfullyWhenValidValueIsGiven($validValue)
    {
        $this->assertInstanceOf(
            WishListId::class,
            new WishListId($validValue)
        );
    }

    /**
     * @dataProvider getInvalidValue
     *
     * @param mixed $invalidValue
     *
     * @throws WishListConstraintException
     */
    public function testItThrowsExceptionWhenInvalidValueIsGiven($invalidValue)
    {
        $this->expectException(WishListConstraintException::class);
        $this->expectExceptionCode(WishListConstraintException::INVALID_WISH_LIST_ID);

        new WishListId($invalidValue);
    }

    /**
     * @return Generator
     */
    public function getValidValue()
    {
        yield [1];
        yield [rand(1, 99999)];
    }

    /**
     * @return Generator
     */
    public function getInvalidValue()
    {
        yield [-1];
        yield ['1'];
        yield [[]];
        yield [10.5];
        yield [null];
        yield [false];
    }
}
