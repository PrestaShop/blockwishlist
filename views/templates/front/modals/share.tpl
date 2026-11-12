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
  class="wishlist-share"
>
  <div class="wishlist-modal modal fade" tabindex="-1" role="dialog" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <form class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{l s='Share wishlist' d='Modules.Blockwishlist.Shop'}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="{l s='Close' d='Shop.Theme.Global'}">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group form-group-lg">
            <label class="form-control-label" for="input2">{l s='Share link' d='Modules.Blockwishlist.Shop'}</label>
            <input
              type="text"
              class="form-control form-control-lg"
              id="input2"
              name="url"
              readonly
            />
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="modal-cancel btn btn-secondary" data-dismiss="modal">
            {l s='Cancel' d='Modules.Blockwishlist.Shop'}
          </button>
          <button type="submit" class="btn btn-primary">
            {l s='Copy text' d='Modules.Blockwishlist.Shop'}
          </button>
        </div>
      </form>
    </div>
  </div>

  <div class="modal-backdrop fade"></div>
</div>
