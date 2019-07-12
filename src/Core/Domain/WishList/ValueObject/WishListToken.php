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

namespace PrestaShop\Module\WishList\Core\Domain\WishList\ValueObject;

use PrestaShop\Module\WishList\Core\Domain\WishList\Exception\WishListConstraintException;

/**
 * WishList token
 */
class WishListToken
{
    /**
     * Allowed WishList token pattern
     */
    const VALID_PATTERN = '/[0-9A-Z]+/s';

    /**
     * Allowed length for WishList token
     */
    const LENGTH = 16;

    /**
     * @var int
     */
    private $wishListToken;

    /**
     * @param int $wishListToken
     *
     * @throws WishListConstraintException
     */
    public function __construct($wishListToken)
    {
        $this->assertIsValidLengthString($wishListToken);
        $this->assertValueMatchesPattern($wishListToken);

        $this->wishListToken = $wishListToken;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->wishListToken;
    }

    /**
     * @param $value
     *
     * @throws WishListConstraintException
     */
    private function assertIsValidLengthString($value)
    {
        if (!is_string($value) || self::LENGTH !== strlen($value)) {
            throw new WishListConstraintException(
                sprintf(
                    'WishList token "%s" is invalid. It must be %s characters long string',
                    self::LENGTH,
                    var_export($value, true)
                ),
                WishListConstraintException::INVALID_WISH_LIST_TOKEN
            );
        }
    }

    /**
     * @param $value
     *
     * @throws WishListConstraintException
     */
    private function assertValueMatchesPattern($value)
    {
        if (!preg_match(self::VALID_PATTERN, $value)) {
            throw new WishListConstraintException(
                sprintf(
                    'WishList token "%s" is invalid. It must match "%s" pattern.',
                    var_export($value, true),
                    self::VALID_PATTERN
                ),
                WishListConstraintException::INVALID_WISH_LIST_TOKEN
            );
        }
    }
}
