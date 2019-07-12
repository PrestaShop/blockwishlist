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

namespace PrestaShop\Module\WishList\Tests\Unit\Core\Domain\Product\ValueObject;

use Generator;
use PHPUnit\Framework\TestCase;
use PrestaShop\Module\WishList\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\Module\WishList\Core\Domain\Product\ValueObject\ProductId;

class ProductIdTest extends TestCase
{
    /**
     * @dataProvider getValidValue
     *
     * @param string $validValue
     *
     * @throws ProductConstraintException
     */
    public function testItIsCreatedSuccessfullyWhenValidValueIsGiven($validValue)
    {
        $this->assertInstanceOf(
            ProductId::class,
            new ProductId($validValue)
        );
    }

    /**
     * @dataProvider getInvalidValue
     *
     * @param mixed $invalidValue
     *
     * @throws ProductConstraintException
     */
    public function testItThrowsExceptionWhenInvalidValueIsGiven($invalidValue)
    {
        $this->expectException(ProductConstraintException::class);
        $this->expectExceptionCode(ProductConstraintException::INVALID_PRODUCT_ID);

        new ProductId($invalidValue);
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
