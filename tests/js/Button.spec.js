/**
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
 */
import initHearts from '@js/components/button';
import EventBus from '@js/lib/event-bus';
import loadTemplate from './utils/loadTemplate';

describe('Button', () => {
  let events;

  before(() => {
    const template = document.createElement('template');
    template.classList.add('js-wishlist-button-template');
    template.content.append(loadTemplate('front/button'));
    document.body.append(template);
    document.body.insertAdjacentHTML('beforeend', `
      <article class="js-product-miniature" data-id-product="1" data-id-product-attribute="2">
        <div class="thumbnail-container"></div>
      </article>
      <article class="js-product-miniature" data-id-product="5" data-id-product-attribute="0">
        <div class="thumbnail-container"></div>
      </article>
    `);
    window.productsAlreadyTagged.push({
      id_product: '5', id_product_attribute: '0', id_wishlist: '7', quantity: '1',
    });

    initHearts();

    events = [];
    ['showLogin', 'showAddToWishList'].forEach((name) => EventBus.$on(name, (event) => events.push({name, event})));
  });

  const heart = (productId) => document.querySelector(
    `.js-product-miniature[data-id-product="${productId}"] .wishlist-button-add`,
  );

  it('should add a heart to the product miniatures, filled when the product is in a list', () => {
    expect(heart(1).textContent.trim()).toBe('favorite_border');
    expect(heart(5).textContent.trim()).toBe('favorite');
  });

  it('should ask the customer to sign in', () => {
    heart(1).click();

    expect(events.pop().name).toBe('showLogin');
  });

  it('should open the list choice for the product', () => {
    window.prestashop.customer.is_logged = true;
    heart(1).click();

    const {name, event} = events.pop();
    expect(name).toBe('showAddToWishList');
    expect(event.detail).toEqual({
      productId: 1, productAttributeId: 2, forceOpen: true, quantity: 0,
    });
  });

  it('should fill the heart once the product is added to a list', () => {
    EventBus.$emit('addedToWishlist', {detail: {productId: 1, productAttributeId: 2, listId: 7}});

    expect(heart(1).textContent.trim()).toBe('favorite');
  });
});
