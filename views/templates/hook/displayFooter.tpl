{**
 * 2007-2020 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}

{if $context === "product"}
  {include file="module:blockwishlist/views/templates/components/modals/add-to-wishlist.tpl" url=$url addUrl=$addUrl newWishlistCTA=$newWishlistCTA}
  {include file="module:blockwishlist/views/templates/components/modals/create.tpl" url=$createUrl}
  {include file="module:blockwishlist/views/templates/components/modals/login.tpl"}
  {include file="module:blockwishlist/views/templates/components/toast.tpl"}
{/if}

{if $context === "index" || $context === "category" || $context === "blockwishlist" || $context === 'search'}
  {include file="module:blockwishlist/views/templates/components/modals/add-to-wishlist.tpl" url=$url addUrl=$addUrl newWishlistCTA=$newWishlistCTA}
  {include file="module:blockwishlist/views/templates/components/modals/create.tpl" url=$createUrl}
  {include file="module:blockwishlist/views/templates/components/modals/delete.tpl" productUrl=$deleteProductUrl}
  {include file="module:blockwishlist/views/templates/components/modals/login.tpl"}
  {include file="module:blockwishlist/views/templates/components/toast.tpl"}
{/if}

