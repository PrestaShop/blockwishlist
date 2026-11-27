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
import selectors from '../lib/selectors';

const closeMenus = () => {
  document.querySelectorAll(`${selectors.lists.menu}.show`).forEach((menu) => {
    menu.classList.remove('show');
  });
};

const actions = {
  'wishlist-toggle-menu': (item) => {
    const menu = item.querySelector(selectors.lists.menu);
    const isOpen = menu.classList.contains('show');
    closeMenus();
    menu.classList.toggle('show', !isOpen);
  },
  'wishlist-rename': (item) => EventBus.$emit('showRenameWishlist', {
    detail: {listId: parseInt(item.dataset.id, 10), title: item.dataset.name},
  }),
  'wishlist-share': (item) => EventBus.$emit('showShareWishlist', {
    detail: {listId: parseInt(item.dataset.id, 10), shareUrl: item.dataset.shareUrl},
  }),
  'wishlist-delete': (item) => EventBus.$emit('showDeleteWishlist', {
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
    const button = event.target.closest(`${selectors.lists.item} ${selectors.lists.action}`);

    if (!button) {
      closeMenus();

      return;
    }

    // The actions are rendered inside the link to the list
    event.preventDefault();
    const {psAction} = button.dataset;

    if (psAction !== 'wishlist-toggle-menu') {
      closeMenus();
    }
    if (actions[psAction]) {
      actions[psAction](button.closest(selectors.lists.item));
    }
  });

  container.querySelector(selectors.createList).addEventListener('click', (event) => {
    event.preventDefault();
    EventBus.$emit('showCreateWishlist');
  });

  EventBus.$on('refetchList', () => refresh(selectors.lists.area));
}
