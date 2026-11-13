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
{$wishlistQuantity = $product.wishlist_quantity|intval}
{$quantityAvailable = $product.quantity_available|intval}
{$cartQuantity = $product.cart_quantity|intval}
{if $product.allow_oosp || $product.customizable}
  {$isDisabled = false}
{elseif isset($product.quantity_available) && ($wishlistQuantity > $quantityAvailable || (isset($product.cart_quantity) && ($cartQuantity >= $quantityAvailable || ($cartQuantity && $cartQuantity + $wishlistQuantity > $quantityAvailable))))}
  {$isDisabled = true}
{else}
  {$isDisabled = !$product.add_to_cart_url}
{/if}

<div
  class="wishlist-product"
  data-id-product="{$product.id_product}"
  data-id-product-attribute="{$product.id_product_attribute}"
  data-id-customization="{$product.id_customization}"
  data-quantity="{$wishlistQuantity}"
  data-add-to-cart-url="{if $product.add_to_cart_url && !$product.customizable}{$product.add_to_cart_url}{/if}"
  data-url="{$product.canonical_url}"
>
  <a class="wishlist-product-link" href="{$product.canonical_url}">
    <div class="wishlist-product-image">
      {if $product.default_image}
        {$image = $product.default_image}
      {elseif $product.cover}
        {$image = $product.cover}
      {else}
        {$image = null}
      {/if}

      {if $image}
        <img
          src="{$image.large.url}"
          alt="{$image.legend}"
          title="{$image.legend}"
          loading="lazy"
          {if !$product.add_to_cart_url}class="wishlist-product-unavailable"{/if}
        >
      {else}
        <img src="{$urls.no_picture_image.bySize.home_default.url}" loading="lazy">
      {/if}

      {if $product.show_availability}
    <p class="wishlist-product-availability">
      {if $product.availability == 'unavailable'}
        <i class="material-icons">block</i>
      {elseif $product.availability == 'last_remaining_items'}
        <i class="material-icons">warning</i>
      {/if}
      {$product.availability_message}
    </p>
  {/if}
    </div>

    <div class="wishlist-product-right">
      <p class="wishlist-product-title">{$product.name}</p>

      <p class="wishlist-product-price">
        {if $product.has_discount}
          <span class="wishlist-product-price-promo">{$product.regular_price}</span>
        {/if}
        {$product.price}
      </p>

      <div class="wishlist-product-combinations">
        <p class="wishlist-product-combinations-text">
          {foreach $product.attributes as $attribute}
            {$attribute.group} : {$attribute.name} -
          {/foreach}
          {l s='Quantity' d='Shop.Theme.Catalog'} : {$wishlistQuantity}
        </p>

        {if !$isGuest}
          <a href="{$product.canonical_url}">
            <i class="material-icons">create</i>
          </a>
        {/if}
      </div>
    </div>
  </a>

  <div class="wishlist-product-bottom">
    <button
      type="button"
      class="btn wishlist-product-addtocart {if $product.customizable}btn-secondary{else}btn-primary{/if}"
      {if $isDisabled}disabled{/if}
    >
      {if $product.customizable}
        {l s='Customize' d='Modules.Blockwishlist.Shop'}
      {else}
        <i class="material-icons shopping-cart">shopping_cart</i>
        {l s='Add to cart' d='Shop.Theme.Actions'}
      {/if}
    </button>

    {if !$isGuest}
      <button type="button" class="wishlist-button-add">
        <i class="material-icons">delete</i>
      </button>
    {/if}
  </div>

  {if $product.show_availability}
    <p class="wishlist-product-availability wishlist-product-availability-responsive">
      {if $product.availability == 'unavailable'}
        <i class="material-icons">block</i>
      {elseif $product.availability == 'last_remaining_items'}
        <i class="material-icons">warning</i>
      {/if}
      {$product.availability_message}
    </p>
  {/if}
</div>
