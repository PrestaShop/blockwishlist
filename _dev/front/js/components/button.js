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
import prestashop from 'prestashop';
import removeFromWishlistUrl from 'removeFromWishlistUrl';
import {removeFromList} from '../api';
import EventBus, {notify} from '../lib/event-bus';

const getQuantityInput = () => document.querySelector('.product-quantity input#quantity_wanted');

const getData = (container) => ({
  productId: parseInt(container.dataset.productId, 10),
  productAttributeId: parseInt(container.dataset.productAttributeId, 10),
});

/**
 * Fill the heart when the product is in a list
 *
 * @param {HTMLElement} container The .wishlist-button element
 * @param {Number|null} listId The list containing the product
 */
const setChecked = (container, listId) => {
  const icon = container.querySelector('.wishlist-button-add .material-icons');
  container.dataset.listId = listId || '';

  if (icon) {
    icon.textContent = listId ? 'favorite' : 'favorite_border';
  }
};

const findTaggedList = ({productId, productAttributeId}, quantity = null) => {
  const tagged = window.productsAlreadyTagged.find(
    (item) => parseInt(item.id_product, 10) === productId
      && parseInt(item.id_product_attribute, 10) === productAttributeId
      && (quantity === null || item.quantity.toString() === quantity),
  );

  return tagged ? parseInt(tagged.id_wishlist, 10) : null;
};

/**
 * Add a heart to the product miniatures, then initialize every heart which is not yet:
 * the ones of the miniatures, and the one rendered by the displayProductActions hook
 */
const initButtons = () => {
  const template = document.querySelector('.js-wishlist-button-template');

  document.querySelectorAll('.js-product-miniature').forEach((product) => {
    const thumbnail = product.querySelector('.thumbnail-container');

    if (!thumbnail || thumbnail.querySelector('.wishlist-button')) {
      return;
    }

    const container = document.createElement('div');
    container.classList.add('wishlist-button');
    container.dataset.productId = product.dataset.idProduct;
    container.dataset.productAttributeId = product.dataset.idProductAttribute;
    container.dataset.url = removeFromWishlistUrl;
    thumbnail.append(container);
  });

  document.querySelectorAll('.wishlist-button:not([data-initialized])').forEach((container) => {
    if (!container.querySelector('.wishlist-button-add') && template) {
      container.append(template.content.cloneNode(true));
    }

    container.dataset.initialized = 'true';
    setChecked(container, findTaggedList(getData(container)));
  });
};

const toggleProduct = async (container) => {
  if (!prestashop.customer.is_logged) {
    EventBus.$emit('showLogin');

    return;
  }

  const product = getData(container);

  if (!container.dataset.listId) {
    const quantity = getQuantityInput();
    EventBus.$emit('showAddToWishList', {
      detail: {
        ...product,
        forceOpen: true,
        quantity: quantity ? parseInt(quantity.value, 10) : 0,
      },
    });

    return;
  }

  const response = await removeFromList(container.dataset.url, {
    ...product,
    listId: parseInt(container.dataset.listId, 10),
  });
  notify(response);

  if (response.success) {
    setChecked(container, null);
  }
};

export default function initHearts() {
  initButtons();

  document.addEventListener('click', (event) => {
    const button = event.target.closest('.wishlist-button .wishlist-button-add');

    if (button) {
      event.preventDefault();
      toggleProduct(button.closest('.wishlist-button'));
    }
  });

  EventBus.$on('addedToWishlist', (event) => {
    document.querySelectorAll('.wishlist-button').forEach((container) => {
      const {productId, productAttributeId} = getData(container);

      if (
        event.detail.productId === productId
        && parseInt(event.detail.productAttributeId, 10) === productAttributeId
      ) {
        setChecked(container, event.detail.listId);
      }
    });
  });

  const productButtons = () => document.querySelectorAll('.wishlist-button[data-is-product="true"]');

  prestashop.on('updateProduct', ({eventType}) => {
    if (eventType === 'updatedProductQuantity') {
      productButtons().forEach((container) => setChecked(container, null));
    }
  });

  prestashop.on('updatedProduct', (args) => {
    initButtons();

    const quantity = getQuantityInput();
    productButtons().forEach((container) => {
      container.dataset.productAttributeId = parseInt(args.id_product_attribute, 10);
      setChecked(container, findTaggedList(getData(container), quantity ? quantity.value : null));
    });
  });

  const observer = new MutationObserver(initButtons);
  document.querySelectorAll('#products, .featured-products').forEach((list) => {
    observer.observe(list, {attributes: false, childList: true});
  });
}
