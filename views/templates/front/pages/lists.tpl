{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *}
{extends file='customer/page.tpl'}

{block name='page_header_container'}
{/block}

{block name='page_content_container'}
  <div class="wishlist-container">
    <div class="wishlist-container-header">
      <h1>{$wishlistsTitlePage}</h1>

      <a href="#" class="wishlist-add-to-new text-primary">
        <i class="material-icons">add_circle_outline</i>
        {$newWishlistCTA}
      </a>
    </div>

    <section id="content" class="page-content card card-block">
      {include file="module:blockwishlist/views/templates/front/lists/list.tpl"}
    </section>
  </div>

  {include file="module:blockwishlist/views/templates/front/modals/share.tpl"}
  {include file="module:blockwishlist/views/templates/front/modals/rename.tpl"}
{/block}

{block name='page_footer_container'}
  <div class="wishlist-footer-links">
    <a href="{$link->getPageLink('my-account', true)|escape:'html'}" class="text-primary"><i class="material-icons">chevron_left</i>{l s='Return to your account' d='Modules.Blockwishlist.Shop'}</a>
    <a href="{$urls.base_url}" class="text-primary"><i class="material-icons">home</i>{l s='Home' d='Shop.Theme.Global'}</a>
  </div>
{/block}
