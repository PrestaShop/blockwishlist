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
import EventBus from '../lib/event-bus';
import refresh from '../lib/refresh';

const closeDropdowns = () => {
  document.querySelectorAll('.wishlist-list-item .dropdown-menu.show').forEach((menu) => {
    menu.classList.remove('show');
  });
};

const actions = {
  'wishlist-list-item-actions': (item) => {
    const menu = item.querySelector('.dropdown-menu');
    const isOpen = menu.classList.contains('show');
    closeDropdowns();
    menu.classList.toggle('show', !isOpen);
  },
  'js-wishlist-rename': (item) => EventBus.$emit('showRenameWishlist', {
    detail: {listId: parseInt(item.dataset.id, 10), title: item.dataset.name},
  }),
  'js-wishlist-share': (item) => EventBus.$emit('showShareWishlist', {
    detail: {listId: parseInt(item.dataset.id, 10), shareUrl: item.dataset.shareUrl},
  }),
  'js-wishlist-delete': (item) => EventBus.$emit('showDeleteWishlist', {
    detail: {listId: parseInt(item.dataset.id, 10), listName: item.dataset.name},
  }),
};

/**
 * The page of the customer lists, rendered by the server and reloaded after each change
 *
 * @param {HTMLElement} container
 */
export default function initListsPage(container) {
  document.addEventListener('click', (event) => {
    const button = event.target.closest('.wishlist-list-item button');

    if (!button) {
      closeDropdowns();

      return;
    }

    event.preventDefault();
    const action = Object.keys(actions).find((name) => button.classList.contains(name));

    if (action !== 'wishlist-list-item-actions') {
      closeDropdowns();
    }
    if (action) {
      actions[action](button.closest('.wishlist-list-item'));
    }
  });

  container.querySelector('.wishlist-add-to-new').addEventListener('click', (event) => {
    event.preventDefault();
    EventBus.$emit('showCreateWishlist');
  });

  EventBus.$on('refetchList', () => refresh('.wishlist-list-container'));
}
