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

{if $wishlists|count > 0}
  <div class="col">
    <div class="card d-print-none">
      <div class="card-header">
        <i class="material-icons">remove_red_eye</i>
        {$blockwishlist|escape:'html':'UTF-8'}
        <span class="badge badge-primary rounded">{$wishlists|count}</span>
      </div>

      <div class="card-body">
        <table class="table" id="customer-wishlist-list">
          <thead>
            <tr>
              <th scope="col">{l s='Name' d='Modules.Blockwishlist.Admin'}</th>
              <th scope="col">{l s='Products' d='Modules.Blockwishlist.Admin'}</th>
              <th scope="col">{l s='Actions' d='Modules.Blockwishlist.Admin'}</th>
            </tr>
          </thead>
          <tbody>
            {foreach from=$wishlists item=wishlist}
              <tr>
                <td>{$wishlist.name|escape:'html':'UTF-8'}</td>
                <td>{$wishlist.nbProducts}</td>
                <td><a href="{$wishlist.shareUrl}" target="_blank">{l s='View' d='Modules.Blockwishlist.Admin'}</a></td>
            {/foreach}
          </tbody>
        </table>
      </div>
    </div>
  </div>
{/if}
