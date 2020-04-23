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
{extends file='page.tpl'}

{block name='page_title'}
    {l s='My Wishlists' mod='blockwishlist'}
{/block}

{block name='page_content'}
  <div
    class="wishlist-container"
    data-url="#"
    data-return-link="#"
    data-home-link="#"
    data-title="Wishlists"
    data-rename-text="Rename"
    data-share-text="Share"
    data-add-text="New wishlist"
  ></div>

  {include file="module:blockwishlist/views/templates/components/modals/add-to-wishlist.tpl" url="http://dummy.com"}
  {include file="module:blockwishlist/views/templates/components/modals/create.tpl" url="http://dummy.com"}
  {include file="module:blockwishlist/views/templates/components/modals/rename.tpl" url="http://dummy.com"}
{/block}


