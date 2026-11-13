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
import {addToList, getLists} from '../api';
import EventBus, {notify} from '../lib/event-bus';
import createModal from '../lib/modal';

/**
 * Let the customer choose the list to add a product to.
 * The lists are loaded the first time the modal opens, and after each change.
 *
 * @param {HTMLElement} root
 */
export default function initAddToWishlist(root) {
  const modal = createModal(root);
  const list = root.querySelector('.wishlist-list');
  const loader = root.querySelector('.wishlist-list-loader');
  const empty = root.querySelector('.wishlist-list-empty');
  const itemTemplate = root.querySelector('.js-wishlist-list-item-template');
  const product = {
    productId: 0,
    productAttributeId: 0,
    quantity: 0,
  };
  let loaded = false;

  const renderItem = (wishlist) => {
    const item = itemTemplate.content.firstElementChild.cloneNode(true);
    item.dataset.id = wishlist.id_wishlist;
    item.querySelector('.js-wishlist-list-item-name').textContent = wishlist.name;

    return item;
  };

  const fetchLists = async () => {
    loaded = true;
    loader.hidden = false;
    empty.hidden = true;

    const lists = await getLists(root.dataset.url);
    list.innerHTML = '';
    list.append(...lists.map(renderItem));

    loader.hidden = true;
    empty.hidden = lists.length > 0;
  };

  EventBus.$on('showAddToWishList', (event) => {
    const {detail} = event;
    modal.toggle(detail.forceOpen ? true : undefined);

    if (detail.productId) {
      product.productId = detail.productId;
    }
    if (typeof detail.productAttributeId === 'number') {
      product.productAttributeId = detail.productAttributeId;
    }
    if (detail.quantity) {
      product.quantity = detail.quantity;
    }

    if (!loaded) {
      fetchLists();
    }
  });

  EventBus.$on('refetchList', () => {
    loaded = false;
    if (modal.isOpen()) {
      fetchLists();
    }
  });

  list.addEventListener('click', async (event) => {
    const item = event.target.closest('.wishlist-list-item');

    if (!item) {
      return;
    }

    const listId = parseInt(item.dataset.id, 10);
    const response = await addToList(root.dataset.addUrl, {listId, ...product});
    modal.close();
    notify(response);

    EventBus.$emit('addedToWishlist', {
      detail: {
        productId: product.productId,
        listId,
        productAttributeId: product.productAttributeId,
      },
    });
  });

  root.querySelector('.wishlist-add-to-new').addEventListener('click', (event) => {
    event.preventDefault();
    modal.close();
    EventBus.$emit('showCreateWishlist', {
      detail: {
        fromAddToWishlist: true,
      },
    });
  });
}
