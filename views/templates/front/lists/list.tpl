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
{* Reloaded from the page after each change made to the lists *}
<div class="wishlist-list-container">
  {if $wishlists}
    <ul class="wishlist-list">
      {foreach $wishlists as $wishlist}
        <li
          class="wishlist-list-item{if $wishlist.default} wishlist-list-item-default{/if}"
          data-id="{$wishlist.id_wishlist}"
          data-name="{$wishlist.name}"
          data-share-url="{$wishlist.shareUrl}"
        >
          <a class="wishlist-list-item-link" href="{$wishlist.listUrl}">
            <p class="wishlist-list-item-title">
              {$wishlist.name}
              <span>({$wishlist.nbProducts|intval})</span>
            </p>

            <div class="wishlist-list-item-right">
              {if $wishlist.default}
                <button type="button" class="js-wishlist-share">
                  <i class="material-icons">share</i>
                </button>
              {else}
                <button type="button" class="wishlist-list-item-actions">
                  <i class="material-icons">more_vert</i>
                </button>

                <div class="dropdown-menu">
                  <button type="button" class="js-wishlist-rename">
                    {l s='Rename' d='Modules.Blockwishlist.Shop'}
                  </button>
                  <button type="button" class="js-wishlist-share">
                    {l s='Share' d='Modules.Blockwishlist.Shop'}
                  </button>
                </div>

                <button type="button" class="js-wishlist-delete">
                  <i class="material-icons">delete</i>
                </button>
              {/if}
            </div>
          </a>
        </li>
      {/foreach}
    </ul>
  {else}
    <p class="wishlist-list-empty">
      {l s='No wishlist found.' d='Modules.Blockwishlist.Shop'}
    </p>
  {/if}
</div>
