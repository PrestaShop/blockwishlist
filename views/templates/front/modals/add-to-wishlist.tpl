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
<div
  class="wishlist-add-to"
  data-url="{$url}"
  data-add-url="{$addUrl}"
>
  <div class="wishlist-modal modal fade" tabindex="-1" role="dialog" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{$wishlistsTitlePage}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="{l s='Close' d='Shop.Theme.Global'}">
            <span aria-hidden="true">×</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="wishlist-chooselist">
            <ul class="wishlist-list"></ul>

            <div class="wishlist-list-loader" hidden>
              <span></span>
              <span></span>
              <span></span>
              <span></span>
            </div>

            <p class="wishlist-list-empty" hidden>
              {l s='No list found.' d='Modules.Blockwishlist.Shop'}
            </p>

            <template class="js-wishlist-list-item-template">
              <li class="wishlist-list-item" role="button" tabindex="0">
                <p class="js-wishlist-list-item-name"></p>
              </li>
            </template>
          </div>
        </div>

        <div class="modal-footer">
          <a href="#" class="wishlist-add-to-new text-primary">
            <i class="material-icons">add_circle_outline</i> {$newWishlistCTA}
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="modal-backdrop fade"></div>
</div>
