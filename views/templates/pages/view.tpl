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

{block name='page_header_container'}
{/block}

{block name='page_content_container'}
  <div
    class="wishlist-products-container"
    data-wishlist="{$current_wishlist|@json_encode}"
    data-wishlist-products="{$products|@json_encode}"
    data-default-sort="{l s='Last added' mod='Modules.Blockwishlist.Shop'}"
    data-add-to-cart="{l s='Add to cart' mod='Modules.Blockwishlist.Shop'}"
    data-customize-text="{l s='Customize' mod='Modules.Blockwishlist.Shop'}"
    data-quantity-text="{l s='Quantity' mod='Modules.Blockwishlist.Shop'}"
    data-last-added="{l s='Last added' mod='Modules.Blockwishlist.Shop'}"
    data-price-low-high="{l s='Price, low to high' mod='Modules.Blockwishlist.Shop'}"
    data-price-high-low="{l s='Price, high to low' mod='Modules.Blockwishlist.Shop'}"
    data-default-sort="{l s='Last added' mod='Modules.Blockwishlist.Shop'}"
    data-title="{l s='Gift ideas for Juliette' mod='Modules.Blockwishlist.Shop'}"
    data-share="{if $isGuest}true{else}false{/if}"
  >
  </div>
{/block}
