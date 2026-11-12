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
{extends file='page.tpl'}

{block name='page_header_container'}
{/block}

{block name='page_content_container'}
  {* Reloaded from the page after each change made to the list *}
  <div
    class="wishlist-products-container"
    data-list-id="{$id}"
    data-list-name="{$wishlistName}"
  >
    <div class="wishlist-products-container-header">
      <h1>
        {$wishlistName}

        {if $listing.products}
          <span class="wishlist-products-count">({$listing.pagination.total_items})</span>
        {/if}
      </h1>

      {if $listing.sort_orders}
        <div class="sort-by-row">
          <span class="hidden-sm-down col-sm-3 col-md-3 sort-by">{l s='Sort by:' d='Shop.Theme.Global'}</span>
          <div class="col-xs-12 col-sm-9 col-md-9 products-sort-order dropdown">
            <button
              class="btn-unstyle select-title"
              rel="nofollow"
              data-toggle="dropdown"
              data-bs-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false"
            >
              {$listing.sort_selected}
              <i class="material-icons float-xs-right">arrow_drop_down</i>
            </button>
            <div class="dropdown-menu">
              {foreach $listing.sort_orders as $sort_order}
                {if $sort_order.label != $listing.sort_selected}
                  <a rel="nofollow" href="{$sort_order.url}" class="select-list">
                    {$sort_order.label}
                  </a>
                {/if}
              {/foreach}
            </div>
          </div>
        </div>
      {/if}
    </div>

    <section id="content" class="page-content card card-block">
      {if $listing.products}
        <ul class="wishlist-products-list">
          {foreach $listing.products as $product}
            <li class="wishlist-products-item">
              {include file="module:blockwishlist/views/templates/front/products/product.tpl"}
            </li>
          {/foreach}
        </ul>
      {else}
        <p class="wishlist-list-empty">
          {l s='No products found' d='Modules.Blockwishlist.Shop'}
        </p>
      {/if}
    </section>

    {include file="module:blockwishlist/views/templates/front/products/pagination.tpl" pagination=$listing.pagination}
  </div>
{/block}

{block name='page_footer_container'}
  <div class="wishlist-footer-links">
    <a href="{$wishlistsLink}"><i class="material-icons">chevron_left</i>{l s='Return to wishlists' d='Modules.Blockwishlist.Shop'}</a>
    <a href="{$urls.base_url}"><i class="material-icons">home</i>{l s='Home' d='Shop.Theme.Global'}</a>
  </div>
{/block}
